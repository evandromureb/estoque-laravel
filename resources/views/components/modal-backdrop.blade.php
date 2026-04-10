@props([
    'close' => 'closeModal()',
    'overlayClass' => 'bg-gray-900/60',
])

<div
    {{ $attributes->merge([
        'class' => 'fixed inset-0 z-40 backdrop-blur-sm transition-opacity '.$overlayClass,
    ]) }}
    @click="{{ $close }}"
></div>
