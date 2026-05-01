@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-bg-light focus:border-brand-primary focus:ring-brand-primary focus:ring-1 rounded-soft shadow-sm text-text-primary placeholder-text-secondary']) }}>
