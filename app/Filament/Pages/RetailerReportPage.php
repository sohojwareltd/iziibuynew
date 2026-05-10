<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class RetailerReportPage extends Page
{
    protected static ?string $slug = 'retailers/report/{user}';

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected string $view = 'filament.pages.retailer-report';

    public User $user;

    #[Url]
    public ?string $filter = null;

    #[Url]
    public ?string $from = null;

    #[Url]
    public ?string $to = null;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function getTitle(): string | Htmlable
    {
        return __('Retailer report').': '.($this->user->email ?? '');
    }

    /**
     * @return \Illuminate\Support\Collection<int, mixed>
     */
    #[Computed]
    public function sells()
    {
        $query = $this->user->earnings();

        if (filled($this->filter)) {
            $query = $query->where('method', $this->filter);
        }

        if (filled($this->from) && filled($this->to)) {
            $query = $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        return $query->latest()->get();
    }
}
