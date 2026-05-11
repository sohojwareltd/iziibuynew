@php
    $menus = \App\Models\CmsMenu::query()->orderBy('name')->get();
@endphp

<x-filament::section :heading="__('Build menu')" :description="__('Choose a menu, reorder links at this level, then open Sub-items to edit nested levels (similar to Voyager menu builder).')">
    <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
        <div class="min-w-[12rem] flex-1">
            <label class="fi-fo-field-wrp-label mb-1 block text-sm leading-6 font-medium text-gray-950 dark:text-white">
                {{ __('Menu') }}
            </label>
            <select
                wire:model.live="menuId"
                class="fi-select-input block w-full rounded-lg border border-gray-950/10 bg-white py-2 ps-3 pe-8 text-base text-gray-950 transition duration-75 focus:border-primary-600 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-primary-400 dark:focus:ring-primary-400"
            >
                @foreach ($menus as $m)
                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                @endforeach
            </select>
        </div>

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
