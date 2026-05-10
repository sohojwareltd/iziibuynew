<x-filament-widgets::widget class="fi-wi-admin-quick-links">
    <x-filament::section
        :heading="__('Quick links')"
        :description="__('Jump to common admin areas')"
    >
        <div
            class="grid gap-2 sm:grid-cols-2 xl:grid-cols-3"
        >
            @foreach ($this->getLinks() as $link)
                <a
                    href="{{ $link['url'] }}"
                    wire:navigate
                    @class([
                        'group flex items-center justify-between gap-3 rounded-lg bg-gray-50 px-4 py-3 text-sm font-medium text-gray-950 shadow-sm ring-1 ring-gray-950/5 transition hover:bg-gray-100 dark:bg-white/5 dark:text-white dark:ring-white/10 dark:hover:bg-white/10',
                    ])
                >
                    <span>{{ $link['label'] }}</span>
                    <span
                        class="text-primary-600 transition group-hover:translate-x-0.5 dark:text-primary-400"
                        aria-hidden="true"
                    >&rarr;</span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
