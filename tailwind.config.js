import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', '-apple-system', '"Segoe UI"', 'Roboto', 'sans-serif', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    blue: '#2376D6',
                    charcoal: '#2B333E',
                },
                surface: {
                    white: '#FFFFFF',
                    gray: '#F4F4F5',
                },
                border: {
                    gray: '#E2E4E8',
                },
                'text-muted': '#6B7280',
                status: {
                    success: '#16A34A',
                    warning: '#D97706',
                    danger: '#DC2626',
                    info: '#2376D6',
                },
            },
        },
    },

    plugins: [forms],
};
