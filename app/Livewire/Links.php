<?php

namespace App\Livewire;

use App\Models\Shop;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Links extends Component
{
    /** @var list<array<string, mixed>> */
    public array $links = [];

    public function mount(?Shop $shop = null): void
    {
        $shopModel = $shop ?? auth()->user()?->shop;
        if (! $shopModel instanceof Shop) {
            $this->links = [['platform' => '', 'url' => '', 'position' => 'footer']];

            return;
        }

        $decoded = $shopModel->links;
        $this->links = array_values(array_map(fn ($row): array => (array) $row, $decoded));
    }

    public function add(): void
    {
        $this->links[] = ['platform' => '', 'url' => '', 'position' => 'footer'];
    }

    public function remove(int $index): void
    {
        unset($this->links[$index]);
        $this->links = array_values($this->links);

        if ($this->links === []) {
            $this->links[] = ['platform' => '', 'url' => '', 'position' => 'footer'];
        }
    }

    public function render(): View
    {
        return view('livewire.links', [
            'links' => $this->links,
        ]);
    }
}
