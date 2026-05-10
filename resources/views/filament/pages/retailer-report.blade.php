<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-950 dark:text-white">{{ __('Method contains') }}</label>
                <input type="text" wire:model.live.debounce.400ms="filter"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white" />
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-950 dark:text-white">{{ __('From') }}</label>
                <input type="date" wire:model.live="from"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white" />
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-950 dark:text-white">{{ __('To') }}</label>
                <input type="date" wire:model.live="to"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white" />
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">#</th>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">{{ __('Date & time') }}</th>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">{{ __('Amount') }}</th>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">{{ __('Shop') }}</th>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">{{ __('Method') }}</th>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">{{ __('Details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->sells as $sell)
                        <tr class="border-t border-gray-100 dark:border-white/10">
                            <td class="px-4 py-2 text-gray-950 dark:text-white">{{ $sell->id }}</td>
                            <td class="px-4 py-2 text-gray-950 dark:text-white">{{ $sell->created_at->format('d M, Y ( h:i A )') }}</td>
                            <td class="px-4 py-2 text-gray-950 dark:text-white">{{ app('iziibuy')->round_num($sell->amount) }} NOK</td>
                            <td class="px-4 py-2 text-gray-950 dark:text-white">
                                @if ($sell->shop)
                                    {{ $sell->shop->user_name }}
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-gray-950 dark:text-white">{{ $sell->method }}</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">
                                @if ($sell->details)
                                    {{ \Illuminate\Support\Str::limit($sell->details, 80) }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('No earnings found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($this->sells->isNotEmpty())
                    <tfoot>
                        <tr class="border-t-2 border-gray-200 dark:border-white/10">
                            <td colspan="2" class="px-4 py-3 font-semibold text-gray-950 dark:text-white">{{ __('Total') }}</td>
                            <td colspan="4" class="px-4 py-3 font-semibold text-gray-950 dark:text-white">
                                {{ app('iziibuy')->round_num($this->sells->sum('amount')) }} NOK
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</x-filament-panels::page>
