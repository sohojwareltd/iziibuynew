<div
    class="border-t border-gray-200/90 bg-gray-50/80 px-4 py-3.5 text-center dark:border-white/10 dark:bg-white/[0.03]"
>
    <a
        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium text-primary-600 underline-offset-4 transition hover:text-primary-700 hover:underline dark:text-primary-400 dark:hover:text-primary-300"
        href="{{ $url }}"
        wire:navigate
    >
        {{ $label }}
        <span aria-hidden="true" class="text-xs">&rarr;</span>
    </a>
</div>
