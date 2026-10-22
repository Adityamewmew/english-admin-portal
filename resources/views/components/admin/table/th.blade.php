@props(['align' => 'start'])

<th scope="col" {{ $attributes->merge(['class' => 'px-5 py-3.5 text-' . $align]) }}>
    @if ($slot->isNotEmpty())
        <span class="font-bold text-[11px] uppercase text-primary/80 dark:text-neutral-300 tracking-wider">
            {{ $slot }}
        </span>
    @endif
</th>
