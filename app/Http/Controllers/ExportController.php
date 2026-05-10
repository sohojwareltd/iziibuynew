<?php

namespace App\Http\Controllers;

use App\Exports\ManagerExport;
use App\Exports\ProductExport;
use App\Http\Resources\ProductResource;
use App\Models\EnterpriseOnboarding;
use App\Models\Group;
use App\Models\Shop as ModelsShop;
use App\Models\User;
use Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    private function enterpriseOnboardingHeaders(): array
    {
        return array_merge(['id', 'user_id', 'created_at', 'updated_at'], EnterpriseOnboarding::EXPORT_META_FIELDS);
    }

    private function enterpriseOnboardingRow(EnterpriseOnboarding $record): array
    {
        $row = [
            $record->id,
            $record->user_id,
            optional($record->created_at)->toDateTimeString(),
            optional($record->updated_at)->toDateTimeString(),
        ];

        foreach (EnterpriseOnboarding::EXPORT_META_FIELDS as $field) {
            $value = $record->{$field};

            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }

            $row[] = $value;
        }

        return $row;
    }

    public function manager(User $user)
    {
        if ($user->shop_id != Shop::id()) {
            return abort(403, 'You are not allowed');
        };
        return Excel::download(new ManagerExport($user), $user->name . "'s vcard.csv");
    }
    public function shop_product_export_by_admin(ModelsShop $shop)
    {
        $products = $shop->products;
        
        return Excel::download(new ProductExport(ProductResource::collection($products)), 'imported_product.xlsx');
    }

    public function enterprise_onboarding_export()
    {
        $records = EnterpriseOnboarding::with('metas')->latest()->get();
        $headers = $this->enterpriseOnboardingHeaders();

        $callback = function () use ($records, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($records as $record) {
                fputcsv($file, $this->enterpriseOnboardingRow($record));
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'enterprise-onboardings.csv');
    }

    public function enterprise_onboarding_export_single(EnterpriseOnboarding $enterpriseOnboarding)
    {
        $enterpriseOnboarding->loadMissing('metas');
        $headers = $this->enterpriseOnboardingHeaders();

        $callback = function () use ($enterpriseOnboarding, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, $this->enterpriseOnboardingRow($enterpriseOnboarding));
            fclose($file);
        };

        return response()->streamDownload($callback, 'enterprise-onboarding-' . $enterpriseOnboarding->id . '.csv');
    }
}
