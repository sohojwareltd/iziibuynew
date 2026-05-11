<x-filament-panels::page>
    @php($data = $this->status)

    <x-filament::section :heading="__('Application')">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Laravel') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['laravelVersion'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('PHP') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['phpVersion'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Environment') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['environment'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Debug') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['debug'] ? __('Yes') : __('No') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Timezone') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['timezone'] }}</dd>
            </div>
        </dl>
    </x-filament::section>

    <x-filament::section :heading="__('Infrastructure')" class="mt-6">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Default database') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">
                    {{ $data['dbConnection'] }}
                    @if ($data['dbOk'])
                        <span class="ml-2 text-success-600 dark:text-success-400">({{ __('connected') }})</span>
                    @else
                        <span class="ml-2 text-danger-600 dark:text-danger-400">({{ __('error') }})</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Cache driver') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['cacheDriver'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Session driver') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['sessionDriver'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Queue driver') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['queueDriver'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Mailer') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['mailer'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Filesystem disk') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['disk'] }}</dd>
            </div>
        </dl>
    </x-filament::section>

    <x-filament::section :heading="__('Server & paths')" class="mt-6">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Storage writable') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['storageWritable'] ? __('Yes') : __('No') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Public writable') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['publicWritable'] ? __('Yes') : __('No') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Free space (volume)') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $data['freeDiskHuman'] }}</dd>
            </div>
        </dl>
    </x-filament::section>

    @if (count($this->warnings))
        <x-filament::section :heading="__('Warnings')" class="mt-6">
            <ul class="list-inside list-disc text-sm text-danger-600 dark:text-danger-400">
                @foreach ($this->warnings as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </x-filament::section>
    @endif
</x-filament-panels::page>
