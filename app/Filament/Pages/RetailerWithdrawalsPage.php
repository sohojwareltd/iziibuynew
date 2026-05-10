<?php

namespace App\Filament\Pages;

use App\Models\RetailerWithdrawal;
use App\Models\User;
use BackedEnum;
use Error;
use Exception;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;

class RetailerWithdrawalsPage extends Page
{
    protected static ?string $slug = 'retailers/withdrawals';

    protected static string|\UnitEnum|null $navigationGroup = 'retailers';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Withdrawals';

    protected static bool $shouldRegisterNavigation = true;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected string $view = 'filament.pages.retailer-withdrawals';

    /**
     * Retailer user id (bound via query string).
     */
    #[Url(as: 'user')]
    public ?int $user = null;

    #[Url]
    public ?string $filter = null;

    #[Url]
    public ?string $from = null;

    #[Url]
    public ?string $to = null;

    /** @var array<string, mixed> */
    public array $stats = [
        'paid' => ['count' => 0, 'total' => 0],
        'pending' => ['count' => 0, 'total' => 0],
        'canceled' => ['count' => 0, 'total' => 0],
    ];

    /** @var array<int, array<string, mixed>> */
    public array $withdrawalRows = [];

    public string $formDateFrom = '';

    public string $formDateTo = '';

    public ?string $withdrawAmount = null;

    public string $withdrawTrnxId = '';

    public string $withdrawDate = '';

    public function mount(): void
    {
        $retailer = $this->getRetailerUser();
        if ($retailer !== null) {
            $this->formDateFrom = $retailer->created_at->format('Y-m-d');
            $this->formDateTo = now()->format('Y-m-d');
        }
        $this->withdrawDate = now()->format('Y-m-d');
        if (filled($this->from)) {
            $this->formDateFrom = $this->from;
        }
        if (filled($this->to)) {
            $this->formDateTo = $this->to;
        }
        $this->reloadWithdrawals();
    }

    public function updatedUser(): void
    {
        $retailer = $this->getRetailerUser();
        if ($retailer !== null) {
            $this->formDateFrom = $retailer->created_at->format('Y-m-d');
            $this->formDateTo = now()->format('Y-m-d');
        }
        $this->reloadWithdrawals();
    }

    public function updatedFilter(): void
    {
        $this->reloadWithdrawals();
    }

    public function updatedFrom(): void
    {
        $this->reloadWithdrawals();
    }

    public function updatedTo(): void
    {
        $this->reloadWithdrawals();
    }

    public function getTitle(): string | Htmlable
    {
        return __('Retailer withdrawals');
    }

    public function getRetailerUser(): ?User
    {
        return $this->user ? User::find($this->user) : null;
    }

    public function applyFilters(): void
    {
        $this->from = $this->formDateFrom ?: null;
        $this->to = $this->formDateTo ?: null;
        $this->reloadWithdrawals();
    }

    public function resetFilters(): void
    {
        $this->filter = null;
        $retailer = $this->getRetailerUser();
        if ($retailer !== null) {
            $this->formDateFrom = $retailer->created_at->format('Y-m-d');
            $this->formDateTo = now()->format('Y-m-d');
        } else {
            $this->formDateFrom = '';
            $this->formDateTo = '';
        }
        $this->from = null;
        $this->to = null;
        $this->reloadWithdrawals();
    }

    public function submitWithdrawBalance(): void
    {
        $retailer = $this->getRetailerUser();
        if ($retailer === null) {
            Notification::make()->title(__('Select a retailer first.'))->danger()->send();

            return;
        }

        $this->validate([
            'withdrawAmount' => ['required', 'numeric', 'lt:'.$retailer->totalBalance()],
            'withdrawTrnxId' => ['nullable', 'string', 'max:50'],
            'withdrawDate' => ['required', 'date'],
        ]);

        try {
            $withdrawal = $retailer->withdraw((int) $this->withdrawAmount);
            $withdrawal->createMetas([
                'trnx_id' => $this->withdrawTrnxId,
                'date' => $this->withdrawDate,
            ]);
            $this->withdrawAmount = '';
            $this->withdrawTrnxId = '';
            $this->withdrawDate = now()->format('Y-m-d');
            Notification::make()->title(__('Withdrawal request placed'))->success()->send();
            $this->reloadWithdrawals();
        } catch (Exception|Error $e) {
            Notification::make()->title($e->getMessage())->danger()->send();
        }
    }

    public function approveWithdrawal(int $withdrawalId): void
    {
        try {
            $data = RetailerWithdrawal::findOrFail($withdrawalId);
            $data->status = 1;
            $data->update();
            Notification::make()->title(__('Withdrawal approved'))->success()->send();
            $this->reloadWithdrawals();
        } catch (Exception $e) {
            Notification::make()->title($e->getMessage())->danger()->send();
        }
    }

    public function cancelWithdrawal(int $withdrawalId): void
    {
        try {
            $data = RetailerWithdrawal::findOrFail($withdrawalId);
            $data->status = 2;
            $data->update();
            Notification::make()->title(__('Withdrawal cancelled'))->success()->send();
            $this->reloadWithdrawals();
        } catch (Exception $e) {
            Notification::make()->title($e->getMessage())->danger()->send();
        }
    }

    protected function reloadWithdrawals(): void
    {
        $retailer = $this->getRetailerUser();

        $effectiveFrom = $this->from;
        $effectiveTo = $this->to;
        if ($retailer !== null && ! filled($effectiveFrom) && ! filled($effectiveTo)) {
            $effectiveFrom = $retailer->created_at->format('Y-m-d');
            $effectiveTo = now()->format('Y-m-d');
        }

        $scoped = function () use ($retailer, $effectiveFrom, $effectiveTo): \Illuminate\Database\Eloquent\Builder {
            $q = RetailerWithdrawal::query();
            if ($retailer !== null) {
                $q->where('user_id', $retailer->id);
            }
            if (filled($effectiveFrom) && filled($effectiveTo)) {
                $q->whereBetween('created_at', [$effectiveFrom, $effectiveTo]);
            }

            return $q;
        };

        $sumForStatus = function (int $status) use ($scoped): array {
            return [
                'count' => $scoped()->where('status', $status)->count(),
                'total' => ((int) $scoped()->where('status', $status)->sum('amount')) / 100,
            ];
        };

        $this->stats = [
            'paid' => $sumForStatus(1),
            'pending' => $sumForStatus(0),
            'canceled' => $sumForStatus(2),
        ];

        $list = $scoped()->latest();
        if ($this->filter !== null && $this->filter !== '') {
            $status = match ($this->filter) {
                '0', '1', '2' => (int) $this->filter,
                default => null,
            };
            if ($status !== null) {
                $list->where('status', $status);
            }
        }

        $this->withdrawalRows = $list->with(['user.retailer'])->get()->map(fn (RetailerWithdrawal $w): array => [
            'id' => $w->id,
            'created_at' => $w->created_at->format('d-m-Y'),
            'amount' => $w->amount,
            'trnx_id' => $w->trnx_id,
            'date' => $w->date,
            'status' => $w->status,
            'status_label' => $w->status(),
            'user_id' => $w->user_id,
            'has_bank' => (bool) ($w->user?->retailer?->bank_account_number),
            'bank_account' => $w->user?->retailer?->bank_account_number,
            'retailer_meta_id' => $w->user?->retailer?->id,
        ])->all();
    }
}
