import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Clarity & Flow Design System
                'brand-primary': '#4A90E2',
                'brand-success': '#2ECC71',
                'brand-danger': '#FF5E5E',
                'brand-warning': '#F5A623',
                'text-primary': '#2D3436',
                'text-secondary': '#636E72',
                'bg-light': '#F0F0F0',
            },
            borderRadius: {
                'soft': '12px',
                'softer': '16px',
                'softest': '20px',
            },
        },
    },

    plugins: [forms],
};
