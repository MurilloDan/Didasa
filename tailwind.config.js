import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                didasa: {
                    red:    '#c80000',
                    redhov: '#a30000',
                    red2:   '#d10026',
                    black:  '#1a1a1a',
                    dark:   '#333333',
                    gold:   '#E09900',
                    light:  '#fff5f5',
                },
            },
        },
    },

    plugins: [forms],
};
