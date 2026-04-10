@props([
    'panelClass' => 'max-w-2xl',
])

<div
    {{ $attributes->merge([
        'class' => 'relative z-50 w-full transform overflow-hidden rounded-3xl bg-white shadow-2xl '.$panelClass,
    ]) }}
>
    {{ $slot }}
</div>
