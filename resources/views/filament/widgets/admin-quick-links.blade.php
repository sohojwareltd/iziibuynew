<x-filament-widgets::widget class="fi-wi-admin-quick-links">
    <x-filament::section
        :heading="__('Quick links')"
        :description="__('Jump to common admin areas')"
        class="fi-admin-quick-links-section"
    >
        <div
            class="grid gap-3 sm:grid-cols-2 2xl:grid-cols-3"
        >
            @foreach ($this->getLinks() as $link)
                <a
                    href="{{ $link['url'] }}"
                    wire:navigate
                    @class([
                        'group relative flex items-center justify-between gap-3 overflow-hidden rounded-xl px-4 py-3.5 text-sm font-medium shadow-sm ring-1 transition',
                        'bg-white text-gray-950 ring-gray-950/5 hover:bg-primary-50 hover:ring-primary-500/25',
                        'dark:bg-white/5 dark:text-white dark:ring-white/10 dark:hover:bg-primary-500/10 dark:hover:ring-primary-400/30',
                    ])
                >
                    <span class="relative z-10">{{ $link['label'] }}</span>
                    <span
                        class="relative z-10 flex size-8 shrink-0 items-center justify-center rounded-lg bg-primary-600/10 text-primary-600 transition group-hover:bg-primary-600 group-hover:text-white dark:bg-primary-400/15 dark:text-primary-300 dark:group-hover:bg-primary-500 dark:group-hover:text-white"
                        aria-hidden="true"
                    >
                        &rarr;
                    </span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
