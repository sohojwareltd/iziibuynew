<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class FinancialReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $rows;
    public Carbon $from;
    public Carbon $to;
    public string $pdfString;

    public function __construct(Collection $rows, Carbon $from, Carbon $to, string $pdfString)
    {
        $this->rows = $rows;
        $this->from = $from;
        $this->to = $to;
        $this->pdfString = $pdfString;
    }

    public function build()
    {
        $filename = 'financial-report-' . $this->from->format('Ymd') . '-' . $this->to->format('Ymd') . '.pdf';

        return $this->subject('Financial report ' . $this->from->toDateString() . ' to ' . $this->to->toDateString())
            ->view('emails.financial_report')
            ->with([
                'rows' => $this->rows,
                'from' => $this->from,
                'to' => $this->to,
            ])
            ->attachData($this->pdfString, $filename, [
                'mime' => 'application/pdf',
            ]);
    }
}


