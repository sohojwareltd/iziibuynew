<x-filament-panels::page>
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div></div>
        <x-filament::button
            wire:click="$toggle('showAddForm')"
            :color="$showAddForm ? 'gray' : 'primary'"
            size="sm"
            :icon="$showAddForm ? 'heroicon-o-x-mark' : 'heroicon-o-plus'"
        >
            {{ $showAddForm ? __('Cancel') : __('Add Setting') }}
        </x-filament::button>
    </div>

    {{-- Add Setting Form --}}
    @if ($showAddForm)
        <x-filament::section :heading="__('New Setting')">
            <form wire:submit="addSetting">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="fi-fo-field-wrp-label text-sm font-medium text-gray-950 dark:text-white">{{ __('Key') }}</label>
                        <input
                            type="text"
                            wire:model="newKey"
                            placeholder="e.g. site.name"
                            class="fi-input mt-1 block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white sm:text-sm"
                        />
                        @error('newKey') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="fi-fo-field-wrp-label text-sm font-medium text-gray-950 dark:text-white">{{ __('Label') }}</label>
                        <input
                            type="text"
                            wire:model="newLabel"
                            placeholder="e.g. Site Name"
                            class="fi-input mt-1 block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white sm:text-sm"
                        />
                        @error('newLabel') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="fi-fo-field-wrp-label text-sm font-medium text-gray-950 dark:text-white">{{ __('Type') }}</label>
                        <select
                            wire:model="newType"
                            class="fi-input mt-1 block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white sm:text-sm"
                        >
                            <option value="text">{{ __('Text') }}</option>
                            <option value="textarea">{{ __('Text Area') }}</option>
                            <option value="number">{{ __('Number') }}</option>
                            <option value="checkbox">{{ __('Checkbox') }}</option>
                            <option value="file">{{ __('File') }}</option>
                            <option value="image">{{ __('Image') }}</option>
                            <option value="select_dropdown">{{ __('Select Dropdown') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="fi-fo-field-wrp-label text-sm font-medium text-gray-950 dark:text-white">{{ __('Group') }}</label>
                        <input
                            type="text"
                            wire:model="newGroup"
                            class="fi-input mt-1 block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white sm:text-sm"
                        />
                    </div>
                    <div>
                        <label class="fi-fo-field-wrp-label text-sm font-medium text-gray-950 dark:text-white">{{ __('Sort Order') }}</label>
                        <input
                            type="number"
                            wire:model="newSortOrder"
                            class="fi-input mt-1 block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white sm:text-sm"
                        />
                    </div>
                </div>
                <div class="mt-4">
                    <x-filament::button type="submit" color="primary" size="sm">
                        {{ __('Create Setting') }}
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    @endif

    {{-- Group Tabs --}}
    @if (count($groups))
        <div class="overflow-x-auto border-b border-gray-200 dark:border-white/10">
            <nav class="flex gap-x-1 -mb-px" aria-label="Settings groups">
                @foreach ($groups as $group)
                    <button
                        type="button"
                        wire:click="switchGroup('{{ $group }}')"
                        @class([
                            'whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors duration-200',
                            'border-primary-500 text-primary-600 dark:text-primary-400' => $activeGroup === $group,
                            'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $activeGroup !== $group,
                        ])
                    >
                        {{ ucfirst($group) }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Settings Panel --}}
        <x-filament::section>
            <form wire:submit="save">
                @php
                    $groupSettings = collect($settings)
                        ->filter(fn($s) => $s['group_name'] === $activeGroup)
                        ->sortBy('sort_order');
                @endphp

                @forelse ($groupSettings as $id => $setting)
                    <div @class([
                        'py-5',
                        'border-b border-gray-100 dark:border-white/5' => ! $loop->last,
                    ])>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            {{-- Label column --}}
                            <div class="sm:w-1/3">
                                <label for="setting-{{ $id }}" class="block text-sm font-semibold text-gray-950 dark:text-white">
                                    {{ $setting['label'] }}
                                </label>
                                <p class="mt-0.5 font-mono text-xs text-gray-400 dark:text-gray-500">
                                    {{ $setting['key'] }}
                                </p>
                                <span class="mt-1 inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-white/10 dark:text-gray-400">
                                    {{ $setting['type'] }}
                                </span>
                            </div>

                            {{-- Input column --}}
                            <div class="flex-1">
                                @switch($setting['type'])
                                    @case('text')
                                        <input
                                            id="setting-{{ $id }}"
                                            type="text"
                                            wire:model.blur="values.{{ $id }}"
                                            class="fi-input block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white sm:text-sm"
                                        />
                                        @break

                                    @case('textarea')
                                        <textarea
                                            id="setting-{{ $id }}"
                                            wire:model.blur="values.{{ $id }}"
                                            rows="4"
                                            class="fi-input block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white sm:text-sm"
                                        ></textarea>
                                        @break

                                    @case('number')
                                        <input
                                            id="setting-{{ $id }}"
                                            type="number"
                                            wire:model.blur="values.{{ $id }}"
                                            class="fi-input block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white sm:text-sm"
                                        />
                                        @break

                                    @case('checkbox')
                                        <label class="relative inline-flex cursor-pointer items-center">
                                            <input
                                                id="setting-{{ $id }}"
                                                type="checkbox"
                                                wire:model="values.{{ $id }}"
                                                class="peer sr-only"
                                            />
                                            <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-full peer-checked:after:border-white dark:border-gray-600 dark:bg-gray-700"></div>
                                        </label>
                                        @break

                                    @case('image')
                                        <div class="space-y-3">
                                            {{-- Current image preview --}}
                                            @if ($values[$id])
                                                <div class="group relative inline-block">
                                                    <img
                                                        src="{{ Storage::disk('public')->url($values[$id]) }}"
                                                        alt="{{ $setting['label'] }}"
                                                        class="h-32 max-w-xs rounded-lg border border-gray-200 object-contain shadow-sm dark:border-white/10"
                                                    />
                                                    <button
                                                        type="button"
                                                        wire:click="removeFile({{ $id }})"
                                                        wire:confirm="{{ __('Remove this image?') }}"
                                                        class="absolute -right-2 -top-2 rounded-full bg-danger-500 p-1 text-white shadow-md opacity-0 transition group-hover:opacity-100"
                                                    >
                                                        <x-heroicon-s-x-mark class="h-3.5 w-3.5" />
                                                    </button>
                                                </div>
                                            @endif

                                            {{-- New upload preview --}}
                                            @if (isset($uploads[$id]) && $uploads[$id])
                                                <div class="rounded-lg border-2 border-dashed border-primary-300 bg-primary-50 p-2 dark:border-primary-700 dark:bg-primary-900/20">
                                                    <img
                                                        src="{{ $uploads[$id]->temporaryUrl() }}"
                                                        alt="Preview"
                                                        class="h-32 max-w-xs rounded object-contain"
                                                    />
                                                    <p class="mt-1 text-xs font-medium text-primary-600 dark:text-primary-400">
                                                        {{ __('New image selected — save to apply') }}
                                                    </p>
                                                </div>
                                            @endif

                                            {{-- Upload input --}}
                                            <div>
                                                <label
                                                    for="upload-{{ $id }}"
                                                    class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-white/10 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10"
                                                >
                                                    <x-heroicon-o-arrow-up-tray class="h-4 w-4" />
                                                    {{ __('Choose Image') }}
                                                </label>
                                                <input
                                                    id="upload-{{ $id }}"
                                                    type="file"
                                                    wire:model="uploads.{{ $id }}"
                                                    accept="image/*"
                                                    class="hidden"
                                                />
                                                <div wire:loading wire:target="uploads.{{ $id }}" class="mt-2">
                                                    <x-filament::loading-indicator class="h-5 w-5 text-primary-500" />
                                                </div>
                                            </div>
                                        </div>
                                        @break

                                    @case('file')
                                        <div class="space-y-3">
                                            @if ($values[$id])
                                                <div class="flex items-center gap-2">
                                                    <x-heroicon-o-document class="h-5 w-5 text-gray-400" />
                                                    <a
                                                        href="{{ Storage::disk('public')->url($values[$id]) }}"
                                                        target="_blank"
                                                        class="text-sm font-medium text-primary-600 underline hover:text-primary-500 dark:text-primary-400"
                                                    >
                                                        {{ basename($values[$id]) }}
                                                    </a>
                                                    <button
                                                        type="button"
                                                        wire:click="removeFile({{ $id }})"
                                                        wire:confirm="{{ __('Remove this file?') }}"
                                                        class="text-danger-500 hover:text-danger-700"
                                                    >
                                                        <x-heroicon-o-trash class="h-4 w-4" />
                                                    </button>
                                                </div>
                                            @endif

                                            @if (isset($uploads[$id]) && $uploads[$id])
                                                <p class="text-xs font-medium text-primary-600 dark:text-primary-400">
                                                    {{ __('New file selected — save to apply') }}
                                                </p>
                                            @endif

                                            <div>
                                                <label
                                                    for="upload-{{ $id }}"
                                                    class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-white/10 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10"
                                                >
                                                    <x-heroicon-o-arrow-up-tray class="h-4 w-4" />
                                                    {{ __('Choose File') }}
                                                </label>
                                                <input
                                                    id="upload-{{ $id }}"
                                                    type="file"
                                                    wire:model="uploads.{{ $id }}"
                                                    class="hidden"
                                                />
                                                <div wire:loading wire:target="uploads.{{ $id }}" class="mt-2">
                                                    <x-filament::loading-indicator class="h-5 w-5 text-primary-500" />
                                                </div>
                                            </div>
                                        </div>
                                        @break

                                    @case('select_dropdown')
                                        <select
                                            id="setting-{{ $id }}"
                                            wire:model="values.{{ $id }}"
                                            class="fi-input block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white sm:text-sm"
                                        >
                                            <option value="">{{ __('— Select —') }}</option>
                                            @foreach (($setting['details'] ?? []) as $optValue => $optLabel)
                                                <option value="{{ $optValue }}">{{ $optLabel }}</option>
                                            @endforeach
                                        </select>
                                        @break

                                    @default
                                        <input
                                            id="setting-{{ $id }}"
                                            type="text"
                                            wire:model.blur="values.{{ $id }}"
                                            class="fi-input block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white sm:text-sm"
                                        />
                                @endswitch
                            </div>

                            {{-- Delete button --}}
                            <div class="flex-shrink-0">
                                <button
                                    type="button"
                                    wire:click="deleteSetting({{ $id }})"
                                    wire:confirm="{{ __('Are you sure you want to delete this setting?') }}"
                                    class="rounded-lg p-2 text-gray-400 transition hover:bg-danger-50 hover:text-danger-600 dark:hover:bg-danger-500/10"
                                    title="{{ __('Delete') }}"
                                >
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ __('No settings in this group.') }}
                    </div>
                @endforelse

                @if ($groupSettings->isNotEmpty())
                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-white/5">
                        <div wire:loading wire:target="save">
                            <x-filament::loading-indicator class="h-5 w-5 text-primary-500" />
                        </div>
                        <x-filament::button type="submit" color="primary" icon="heroicon-o-check">
                            {{ __('Save Settings') }}
                        </x-filament::button>
                    </div>
                @endif
            </form>
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="py-12 text-center">
                <x-heroicon-o-cog-6-tooth class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-950 dark:text-white">{{ __('No settings yet') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Get started by adding your first setting.') }}</p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
