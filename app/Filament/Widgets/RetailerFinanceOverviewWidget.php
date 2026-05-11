<?php

namespace App\Filament\Widgets;

use App\Models\RetailerMeta;
use App\Models\RetailerWithdrawal;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RetailerFinanceOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -19;

    protected ?string $heading = 'Retailers & payouts';

    protected ?string $description = 'Partner network and withdrawal queue.';

    /**
     * @var int|array<string, int|null>|null
     */
    protected int|array|null $columns = [
        'default' => 1,
        'sm' => 3,
    ];

    protected function getStats(): array
    {
        $pending = RetailerWithdrawal::query()->where('status', 0)->count();

        return [
            Stat::make(__('Retailers'), number_format(RetailerMeta::query()->count()))
                ->description(__('Onboarded partners'))
                ->descriptionIcon(Heroicon::OutlinedBuildingStorefront)
                ->color('gray')
                ->icon(Heroicon::OutlinedBuildingStorefront),
            Stat::make(__('Pending withdrawals'), number_format($pending))
                ->description(__('Awaiting approval'))
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color('warning')
                ->icon(Heroicon::OutlinedBanknotes),
            Stat::make(
                __('Paid withdrawals (30 days)'),
                number_format(
                    RetailerWithdrawal::query()
                        ->where('status', 1)
                        ->where('created_at', '>=', now()->subDays(30))
                        ->count()
                )
            )
                ->description(__('Completed in the last month'))
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->icon(Heroicon::OutlinedCheckCircle),
        ];
    }
}
