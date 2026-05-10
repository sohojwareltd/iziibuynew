<x-filament-panels::page>
    @php
        $retailer = $this->getRetailerUser();
    @endphp

    <div class="space-y-8">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-950 dark:text-white">{{ __('Status') }}</label>
                    <select wire:model.live="filter"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                        <option value="">{{ __('All') }}</option>
                        <option value="1">{{ __('Completed') }}</option>
                        <option value="0">{{ __('Pending') }}</option>
                        <option value="2">{{ __('Canceled') }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-950 dark:text-white">{{ __('From') }}</label>
                    <input type="date" wire:model="formDateFrom"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-950 dark:text-white">{{ __('To') }}</label>
                    <input type="date" wire:model="formDateTo"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white" />
                </div>
                <div class="flex flex-wrap items-end gap-2">
                    <button type="button" wire:click="applyFilters"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        {{ __('Filter') }}
                    </button>
                    <button type="button" wire:click="resetFilters"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/15">
                        {{ __('Reset') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl bg-primary-600 p-4 text-white shadow">
                <div class="text-sm font-medium opacity-90">{{ __('Total paid') }} ({{ $stats['paid']['count'] }})</div>
                <div class="mt-1 text-2xl font-light">{{ $stats['paid']['total'] }}</div>
            </div>
            <div class="rounded-xl bg-primary-600 p-4 text-white shadow">
                <div class="text-sm font-medium opacity-90">{{ __('Pending') }} ({{ $stats['pending']['count'] }})</div>
                <div class="mt-1 text-2xl font-light">{{ $stats['pending']['total'] }}</div>
            </div>
            <div class="rounded-xl bg-primary-600 p-4 text-white shadow">
                <div class="text-sm font-medium opacity-90">{{ __('Canceled') }} ({{ $stats['canceled']['count'] }})</div>
                <div class="mt-1 text-2xl font-light">{{ $stats['canceled']['total'] }}</div>
            </div>
        </div>

        @if ($retailer)
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-900">
                <h3 class="mb-3 text-sm font-semibold text-gray-950 dark:text-white">{{ __('Request withdrawal') }}</h3>
                <div class="grid gap-4 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Amount') }}
                            <span class="text-gray-950 dark:text-white">({{ __('available') }}:
                                {{ $retailer->totalBalance() }} NOK)</span></label>
                        <input type="number" wire:model="withdrawAmount"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Transaction ID') }}</label>
                        <input type="text" wire:model="withdrawTrnxId"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Date') }}</label>
                        <input type="date" wire:model="withdrawDate"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950 dark:text-white" />
                    </div>
                    <div class="flex items-end">
                        <button type="button" wire:click="submitWithdrawBalance"
                            class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-primary-500">
                            {{ __('Withdraw') }}
                        </button>
                    </div>
                </div>
                @error('withdrawAmount')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('withdrawDate')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">{{ __('Date / ID') }}</th>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">{{ __('Amount') }}</th>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">{{ __('Trnx ID') }}</th>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">{{ __('Withdraw date') }}</th>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">{{ __('Status') }}</th>
                        <th class="px-4 py-2 text-start font-medium text-gray-950 dark:text-white">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($withdrawalRows as $row)
                        <tr class="border-t border-gray-100 dark:border-white/10" wire:key="wd-{{ $row['id'] }}">
                            <td class="px-4 py-2 text-gray-950 dark:text-white">{{ $row['id'] }} — {{ $row['created_at'] }}</td>
                            <td class="px-4 py-2 text-gray-950 dark:text-white">{{ $row['amount'] }} NOK</td>
                            <td class="px-4 py-2 text-gray-950 dark:text-white">{{ $row['trnx_id'] }}</td>
                            <td class="px-4 py-2 text-gray-950 dark:text-white">{{ $row['date'] }}</td>
                            <td class="px-4 py-2 text-gray-950 dark:text-white">{{ $row['status_label'] }}</td>
                            <td class="px-4 py-2">
                                @if ((int) $row['status'] === 0)
                                    @if ($row['has_bank'])
                                        <button type="button"
                                            wire:click="approveWithdrawal({{ $row['id'] }})"
                                            wire:confirm="{{ __('Approve this withdrawal?') }}"
                                            class="rounded-md bg-green-600 px-2 py-1 text-xs font-medium text-white hover:bg-green-500">
                                            {{ __('Approve') }}
                                        </button>
                                    @else
                                        @if ($row['retailer_meta_id'])
                                            <a href="{{ \App\Filament\Resources\RetailerMetas\RetailerMetaResource::getUrl('edit', ['record' => $row['retailer_meta_id']]) }}"
                                                class="inline-block rounded-md bg-red-600 px-2 py-1 text-xs font-medium text-white hover:bg-red-500">
                                                {{ __('Update bank account') }}
                                            </a>
                                        @endif
                                    @endif
                                    <button type="button"
                                        wire:click="cancelWithdrawal({{ $row['id'] }})"
                                        wire:confirm="{{ __('Cancel this withdrawal?') }}"
                                        class="ms-2 rounded-md bg-amber-600 px-2 py-1 text-xs font-medium text-white hover:bg-amber-500">
                                        {{ __('Cancel') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
