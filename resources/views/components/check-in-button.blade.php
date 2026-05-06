@props(['color' => null])

@php
    $buttonStyle = $color ? "background-color: {$color}; border-color: {$color};" : null;
    $buttonClasses = 'inline-flex items-center justify-center rounded-full px-4 py-2 text-sm font-semibold text-white transition focus:outline-none focus:ring-2 focus:ring-offset-2';

    if (! $color) {
        $buttonClasses .= ' bg-violet-600 hover:bg-violet-700 focus:ring-violet-500';
    }
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => $buttonClasses, 'style' => $buttonStyle]) }}>
    {{ $slot }}
</button>
