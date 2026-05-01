import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'brand-softer': '#eff6ff', // blue-50
                'fg-brand-strong': '#1e40af', // blue-800
                'fg-brand': '#2563eb', // blue-600
                'neutral-primary-soft': '#ffffff',
                'neutral-secondary-soft': '#f9fafb',
                'neutral-secondary-medium': '#f3f4f6',
                'neutral-tertiary': '#f3f4f6',
                'neutral-tertiary-medium': '#e5e7eb',
                'danger-soft': '#fef2f2',
                'danger-subtle': '#fecaca',
                'fg-danger-strong': '#991b1b',
                'default': '#e5e7eb', // border-default
                'default-medium': '#d1d5db',
                'body': '#6b7280', // text-gray-500
                'heading': '#111827', // text-gray-900
                'disabled': '#9ca3af', // text-gray-400
                'fg-disabled': '#9ca3af', // text-gray-400
            },
            borderRadius: {
                'base': '0.5rem',
            }
        },
    },

    plugins: [forms, typography],
};
