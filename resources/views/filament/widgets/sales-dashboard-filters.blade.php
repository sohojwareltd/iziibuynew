<x-filament-widgets::widget class="fi-wi-sales-dashboard-filters">
    <x-filament::section
        :heading="__('Sales dashboard')"
        :description="__('Filter by shop and date range (same behaviour as the legacy Voyager admin home).')"
    >
        <form
            method="GET"
            action="{{ route('filament.admin.pages.dashboard') }}"
            class="flex flex-col gap-5 rounded-xl bg-white/60 p-4 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10 sm:p-5"
        >
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
                <div class="flex min-w-0 flex-col gap-1.5 sm:col-span-2 lg:col-span-1">
                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('Short by') }}
                    </span>
                    <select
                        name="short"
                        class="fi-input block w-full rounded-lg border-0 bg-white py-2 pe-9 ps-3 text-sm text-gray-950 shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:bg-white/5 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500"
                    >
                        <option value="">{{ __('All Shop') }}</option>
                        @foreach (\App\Models\Shop::query()->orderBy('user_name')->get() as $shop)
                            <option value="{{ $shop->user_name }}" @selected(request('short') === $shop->user_name)>
                                {{ $shop->name ?: $shop->user_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Start') }}</span>
                    <input
                        type="date"
                        name="start"
                        value="{{ request('start') }}"
                        class="fi-input block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm text-gray-950 shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:bg-white/5 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500"
                    />
                </div>
                <div class="flex flex-col gap-1.5">
                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('End') }}</span>
                    <input
                        type="date"
                        name="end"
                        value="{{ request('end') }}"
                        class="fi-input block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm text-gray-950 shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:bg-white/5 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500"
                    />
                </div>
                <div class="flex flex-wrap gap-2 lg:justify-end">
                    <x-filament::button type="submit">
                        {{ __('Short') }}
                    </x-filament::button>
                    <x-filament::button
                        color="gray"
                        outlined
                        tag="a"
                        :href="route('filament.admin.pages.dashboard')"
                    >
                        {{ __('Reset') }}
                    </x-filament::button>
                </div>
            </div>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
