<x-filament-widgets::widget class="fi-wi-voyager-sales-dashboard-filters">
    <form
        method="GET"
        action="{{ route('filament.admin.pages.dashboard') }}"
        class="fi-voyager-dash-toolbar ml-2 grid gap-5 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-stretch"
    >
        <div class="overflow-hidden rounded-[10px] bg-[#333B52] shadow-sm ring-1 ring-black/10">
            <div class="px-4 py-3">
                <h2 class="text-lg font-normal tracking-wide text-white">{{ __('Sales Dashboard') }}</h2>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-4 bg-[#7C9DA6] px-4 py-3">
                <span class="text-lg font-medium text-white">{{ __('Show by') }}</span>
                <div class="min-w-[12rem] flex-1 sm:flex-none">
                    <select
                        name="short"
                        onchange="this.form.submit()"
                        class="fi-input block w-full rounded-lg border-0 bg-white/95 py-2 pe-9 ps-3 text-sm text-[#333B52] shadow-sm ring-1 ring-black/10 focus:ring-2 focus:ring-[#333B52]/40"
                    >
                        <option value="">{{ __('All shop') }}</option>
                        @foreach (\App\Models\Shop::query()->orderBy('user_name')->get() as $shop)
                            <option value="{{ $shop->user_name }}" @selected(request('short') === $shop->user_name)>
                                {{ $shop->name ?: $shop->user_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-end gap-3 text-[#888]">
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="fi-voyager-dash-start">{{ __('Start') }}</label>
                <input
                    id="fi-voyager-dash-start"
                    class="fi-voyager-date-field rounded-[10px] border border-[#ddd] px-2.5 py-1.5 text-sm text-gray-700 shadow-sm focus:border-[#7C9DA6] focus:outline-none focus:ring-1 focus:ring-[#7C9DA6]"
                    type="date"
                    name="start"
                    value="{{ request('start') }}"
                />
            </div>
            <span class="hidden pb-2 text-sm sm:inline">{{ __('To') }}</span>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="fi-voyager-dash-end">{{ __('End') }}</label>
                <input
                    id="fi-voyager-dash-end"
                    class="fi-voyager-date-field rounded-[10px] border border-[#ddd] px-2.5 py-1.5 text-sm text-gray-700 shadow-sm focus:border-[#7C9DA6] focus:outline-none focus:ring-1 focus:ring-[#7C9DA6]"
                    type="date"
                    name="end"
                    value="{{ request('end') }}"
                />
            </div>
            <div class="flex flex-wrap gap-2 pb-0.5">
                <x-filament::button type="submit" color="primary">
                    {{ __('Filter') }}
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
</x-filament-widgets::widget>
