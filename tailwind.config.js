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
                brand: {
                    50:  '#f0faf5',
                    100: '#d1ede0',
                    200: '#a3dbc1',
                    300: '#6ec29d',
                    400: '#3fa87a',
                    500: '#2a8a61',
                    600: '#2D6A4F',
                    700: '#235941',
                    800: '#1a4532',
                    900: '#112e22',
                },
            },
        },
    },

    plugins: [forms],
};
