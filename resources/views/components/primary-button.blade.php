<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-2.5 bg-brand-primary border border-transparent rounded-soft font-semibold text-sm text-white uppercase tracking-wide hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
