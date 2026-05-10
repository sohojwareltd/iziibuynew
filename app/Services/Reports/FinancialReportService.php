<?php

namespace App\Services\Reports;

use App\Models\Charge;
use App\Models\SubscriptionCharge;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use App\Mail\FinancialReportMail;

class FinancialReportService
{
    /**
     * Build a unified collection of admin earnings from Charges and SubscriptionCharges.
     *
     * @param  string|\DateTimeInterface|Carbon  $fromDate
     * @param  string|\DateTimeInterface|Carbon  $toDate
     * @return Collection
     */
    public function buildReportCollection($fromDate = null, $toDate = null): Collection
    {

    
        if ($fromDate) {
            $from = Carbon::parse($fromDate)->startOfDay();
        } else {
            $from = null;
        }

        if ($toDate) {
            $to = Carbon::parse($toDate)->startOfDay();
        } else {
            $to = null;
        }



        $chargeItems = Charge::with(['shop'])
            ->where('is_demo', 0)
            ->where('status', 1)
            ->when($from, function ($query) use ($from) {
                $query->where('created_at', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->where('created_at', '<=', $to);
            })
            ->get()
            ->map(function (Charge $charge) use ($from, $to) {
                $shop = $charge->shop;
                $details = json_decode($charge->payment_body);

                return [
                    'source' => 'charge',
                    'customer_name' => optional($shop)->user->name ?? '',
                    'amount' => (float) $charge->amount,
                    'invoice_details' => $charge->comment ?? '',
                    'payment_date' => optional($charge->created_at)?->toDateTimeString() ?? '',
                    'server_id' => (string) $charge->id,
                    'customer_id' => optional($shop)->user_id ?? '',
                    'shop_slug' => optional($shop)->user_name ?? '',
                    'card_number' => $details?->metadata?->last4 ?? '',
                    'period' => optional($charge->created_at)?->format('M Y') ?? '',
                    'bet_period' => $from && $to ? $from->format('Y-m-d') . ' to ' . $to->format('Y-m-d') : '',
                    'email' => optional($shop)->contact_email ?? (optional($shop)->email ?? ''),
                ];
            });
   
        $subscriptionItems = SubscriptionCharge::with(['subscription.subscribable'])
            ->where('status', 1)
            ->when($from, function ($query) use ($from) {
                $query->where('created_at', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->where('created_at', '<=', $to);
            })
            ->get()
            ->map(function (SubscriptionCharge $subCharge) use ($from, $to) {
                $subscription = $subCharge->subscription;
                $subscribable = optional($subscription)->subscribable; // might be Enterprise/Shop/etc.

                // Try to derive some sensible defaults
                $customerName = optional($subscribable)->name
                    ?? (optional($subscribable)->company_name ?? '');
                $shopSlug = $subscribable && method_exists($subscribable, 'getAttribute') ? ($subscribable->slug ?? '') : '';

                // Build human-friendly subscription context
                $subscriptionType = $subscribable ? class_basename($subscribable) : 'Unknown';
                $subscriptionId = optional($subscription)->id ?? '';
                $subscriptionTitle = optional($subscription)->title ?? '';
                $subscriptionDomain = optional($subscription)->domain ?? optional($subscription)->company_domain ?? '';
                $subscriptionDescriptor = trim(collect([$subscriptionType, $subscriptionId ? ('#' . $subscriptionId) : null, $subscriptionTitle ?: $subscriptionDomain])->filter()->implode(' '));
                $invoiceDetails              = trim('Subscription charge' . ($subscriptionDescriptor ? (' — ' . $subscriptionDescriptor) : ''));
                $details = json_decode($subCharge->charge_details);

                return [
                    'source' => 'subscription_charge',
                    'customer_name' => $customerName,
                    'amount' => (float) $subCharge->amount,
                    'invoice_details' => $invoiceDetails,
                    'payment_date' => optional($subCharge->created_at)?->toDateTimeString() ?? '',
                    'server_id' => (string) $subCharge->id,
                    'customer_id' => optional($subscription)->user_id ?? (optional($subscribable)->user_id ?? ''),
                    'shop_slug' => $subCharge->domain ?? ($subscriptionDomain ?: $shopSlug),
                    'card_number' => $details?->metadata?->last4 ?? '',
                    'period' => optional($subCharge->created_at)?->format('M Y') ?? '',
                    'bet_period' => $from && $to ? $from->format('Y-m-d') . ' to ' . $to->format('Y-m-d') : '',
                    'email' => optional($subscribable)->contact_email ?? (optional($subscribable)->email ?? ''),
                ];
            });
        return $chargeItems->concat($subscriptionItems)
            ->sortBy('payment_date')
            ->values();
    }

    /**
     * Build rows and summary together.
     */
    public function buildReportData($fromDate = null, $toDate = null): array
    {
        if ($fromDate) {
            $from = Carbon::parse($fromDate)->startOfDay();
        } else {
            $from = null;
        }
        if ($toDate) {
            $to = Carbon::parse($toDate)->endOfDay();
        } else {
            $to = null;
        }
        $rows = $this->buildReportCollection($from, $to);

        $totalAmount = (float) $rows->sum('amount');
        $totalCount = (int) $rows->count();
        $totalChargesAmount = (float) $rows->where('source', 'charge')->sum('amount');
        $totalSubChargesAmount = (float) $rows->where('source', 'subscription_charge')->sum('amount');
        $uniqueCustomers = (int) $rows->pluck('customer_id')->filter()->unique()->count();

        $summary = [
            'from' => $from,
            'to' => $to,
            'total_amount' => $totalAmount,
            'total_count' => $totalCount,
            'total_charges_amount' => $totalChargesAmount,
            'total_subscription_amount' => $totalSubChargesAmount,
            'unique_customers' => $uniqueCustomers,
        ];

        return compact('rows', 'summary');
    }

    /**
     * Generate a PDF for the report and return the DomPDF instance.
     */
    public function generatePdf($fromDate = null, $toDate = null)
    {
        if ($fromDate) {
            $from = Carbon::parse($fromDate)->startOfDay();
        } else {
            $from = null;
        }
        if ($toDate) {
            $to = Carbon::parse($toDate)->endOfDay();
        } else {
            $to = null;
        }
        $data = $this->buildReportData($from, $to);
    
        return Pdf::loadView('reports.financial_report', [
            'rows' => $data['rows'],
            'from' => $from,
            'to' => $to,
            'summary' => $data['summary'],
        ])->setPaper('a4', 'landscape');
    }

    /**
     * Stream the PDF to the browser.
     */
    public function streamPdf($fromDate = null, $toDate = null)
    {
        if ($fromDate) {
            $from = Carbon::parse($fromDate)->startOfDay();
        } else {
            $from = null;
        }
        if ($toDate) {
            $to = Carbon::parse($toDate)->endOfDay();
        } else {
            $to = null;
        }
        $pdf = $this->generatePdf($from, $to);
        
        $fromStr = $fromDate ? Carbon::parse($fromDate)->format('Ymd') : 'start';
        $toStr = $toDate ? Carbon::parse($toDate)->format('Ymd') : 'end';
        $filename = 'financial-report-' . $fromStr . '-' . $toStr . '.pdf';
        
        return $pdf->stream($filename);
    }

    /**
     * Download the PDF.
     */
    public function downloadPdf($fromDate = null, $toDate = null)
    {
        if ($fromDate) {
            $from = Carbon::parse($fromDate)->startOfDay();
        } else {
            $from = null;
        }
        if ($toDate) {
            $to = Carbon::parse($toDate)->endOfDay();
        } else {
            $to = null;
        }
        $pdf = $this->generatePdf($from, $to);
        
        $fromStr = $fromDate ? Carbon::parse($fromDate)->format('Ymd') : 'start';
        $toStr = $toDate ? Carbon::parse($toDate)->format('Ymd') : 'end';
        $filename = 'financial-report-' . $fromStr . '-' . $toStr . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Email the PDF report.
     */
    public function emailReport($fromDate, $toDate, string $recipientEmail)
    {
        if ($fromDate) {
            $from = Carbon::parse($fromDate)->startOfDay();
        } else {
            $from = null;
        }
        if ($toDate) {
            $to = Carbon::parse($toDate)->endOfDay();
        } else {
            $to = null;
        }
        $pdf = $this->generatePdf($from, $to);
        $rows = $this->buildReportCollection($from, $to);
        Mail::to($recipientEmail)->send(new FinancialReportMail($rows, $from, $to, $pdf->output()));
    }
}
