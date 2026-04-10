@props([
    'class' => '',
])

@if ($errors->any())
    <div
        {{ $attributes->merge([
            'class' => trim('rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 '.$class),
        ]) }}
        role="alert"
    >
        <p class="mb-2 font-bold">{{ __('Corrija os erros abaixo:') }}</p>
        <ul class="list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
