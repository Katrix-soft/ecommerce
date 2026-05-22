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
                'brand': {
                    50:  '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                    950: '#042f2e',
                },
                'neutral-primary-soft': '#ffffff',
                'neutral-secondary-soft': '#f9fafb',
                'neutral-secondary-medium': '#f3f4f6',
                'neutral-tertiary': '#f3f4f6',
                'neutral-tertiary-medium': '#e5e7eb',
                'danger-soft': '#fef2f2',
                'danger-subtle': '#fecaca',
                'fg-danger-strong': '#991b1b',
                'success-soft': '#f0fdf4',
                'fg-success-strong': '#166534',
                'default': '#e5e7eb',
                'default-medium': '#d1d5db',
                'body': '#6b7280',
                'heading': '#111827',
                'disabled': '#9ca3af',
                'fg-disabled': '#9ca3af',
            },
            borderRadius: {
                'base': '0.5rem',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                fadeIn: 'fadeIn 0.4s ease-out',
            },
        },
    },

    plugins: [forms, typography],
};
