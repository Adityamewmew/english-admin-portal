<tr {{ $attributes->merge(['class' => 'hover:bg-surface/60 border-b border-slate-100 last:border-b-0 dark:hover:bg-neutral-700/50 dark:border-neutral-700/60 transition-colors']) }}>
    {{ $slot }}
</tr>
