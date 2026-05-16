@php
    use App\Enums\MenuContext;
    use App\Models\CmsMenu;

    $menuContext = MenuContext::tryFrom($context ?? 'frontend') ?? MenuContext::Frontend;
    $menus = CmsMenu::query()->forContext($menuContext)->orderBy('name')->get();
    $activeMenu = filled($menuId ?? null) ? $menus->firstWhere('id', (int) $menuId) : null;
@endphp

<x-filament::section>
    <x-slot name="heading">
        {{ __('Menu builder') }}
    </x-slot>
    <x-slot name="description">
        {{ __('Build frontend navigation and Filament admin sidebar links from one place.') }}
    </x-slot>

    <div class="mb-6 flex flex-wrap gap-2">
        @foreach (MenuContext::cases() as $case)
            <a
                href="{{ \App\Filament\Resources\CmsMenuItems\Pages\MenuBuilder::getUrl(['context' => $case->value]) }}"
                @class([
                    'inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium ring-1 transition',
                    'bg-primary-600 text-white ring-primary-600' => $menuContext === $case,
                    'bg-white text-gray-700 ring-gray-200 hover:bg-gray-50 dark:bg-white/5 dark:text-gray-200 dark:ring-white/10' => $menuContext !== $case,
                ])
            >
                {{ $case->label() }}
            </a>
        @endforeach
    </div>

    @if ($menuContext === MenuContext::Admin)
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
            {{ __('Enable “Replace admin sidebar” on a menu (Menus screen) to use only that menu in the panel. Otherwise, links here are added alongside the default Filament navigation.') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
        <div class="min-w-[12rem] flex-1">
            <label class="fi-fo-field-wrp-label mb-1 block text-sm font-medium leading-6 text-gray-950 dark:text-white">
                {{ __('Menu') }}
            </label>
            <select
                wire:model.live="menuId"
                class="fi-select-input block w-full rounded-lg border border-gray-950/10 bg-white py-2 ps-3 pe-8 text-base text-gray-950 transition duration-75 focus:border-primary-600 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-primary-400 dark:focus:ring-primary-400"
            >
                @forelse ($menus as $m)
                    <option value="{{ $m->id }}">
                        {{ $m->name }}
                        @if ($m->replaces_panel_navigation)
                            ({{ __('replaces sidebar') }})
                        @endif
                    </option>
                @empty
                    <option value="">{{ __('No menus yet') }}</option>
                @endforelse
            </select>
        </div>

        @if ($activeMenu)
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium text-gray-950 dark:text-white">{{ __('Slug') }}:</span> {{ $activeMenu->slug }}
                @if ($activeMenu->location)
                    <span class="mx-2">·</span>
                    <span class="font-medium text-gray-950 dark:text-white">{{ __('Location') }}:</span> {{ $activeMenu->location }}
                @endif
            </div>
        @endif

        @if ($parentId)
            <div class="flex flex-col gap-1 text-sm">
                <span class="font-medium text-gray-950 dark:text-white">
                    {{ __('Level') }}: {{ __('child items') }}
                </span>
                <button
                    type="button"
                    wire:click="goUpParent"
                    class="text-start text-sm font-medium text-primary-600 hover:underline dark:text-primary-400"
                >
                    {{ __('↑ Back to parent level') }}
                </button>
            </div>
        @endif
    </div>
</x-filament::section>
