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
                'brand-softer': '#f5f3ff', // purple-50
                'fg-brand-strong': '#2d1b5a', // darker brand
                'fg-brand': '#3b226e', // primary brand
                'indigo': {
                    50: '#f5f3ff',
                    100: '#ede9fe',
                    200: '#ddd6fe',
                    300: '#c4b5fd',
                    400: '#a78bfa',
                    500: '#8b5cf6',
                    600: '#3b226e', // Brand
                    700: '#2d1b5a',
                    800: '#251645',
                    900: '#1e1035',
                    950: '#0f081a',
                },
                'purple': {
                    50: '#f5f3ff',
                    100: '#ede9fe',
                    200: '#ddd6fe',
                    300: '#c4b5fd',
                    400: '#a78bfa',
                    500: '#8b5cf6',
                    600: '#3b226e', // Brand
                    700: '#2d1b5a',
                    800: '#251645',
                    900: '#1e1035',
                    950: '#0f081a',
                },
                'blue': {
                    50: '#f5f3ff',
                    100: '#ede9fe',
                    200: '#ddd6fe',
                    300: '#c4b5fd',
                    400: '#a78bfa',
                    500: '#8b5cf6',
                    600: '#3b226e', // Brand
                    700: '#2d1b5a',
                    800: '#251645',
                    900: '#1e1035',
                    950: '#0f081a',
                },
                'neutral-primary-soft': '#ffffff',
                'neutral-secondary-soft': '#f9fafb',
                'neutral-secondary-medium': '#f3f4f6',
                'neutral-tertiary': '#f3f4f6',
                'neutral-tertiary-medium': '#e5e7eb',
                'danger-soft': '#fef2f2',
                'danger-subtle': '#fecaca',
                'fg-danger-strong': '#991b1b',
                'success-soft': '#f0fdf4', // green-50
                'fg-success-strong': '#166534', // green-800
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
