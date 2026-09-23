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
            colors: {
                canvas: {
                    DEFAULT: '#FBF8F3',
                    soft: '#F1EAD9',
                },
                ink: {
                    DEFAULT: '#2B2A28',
                    muted: '#6B6459',
                    faint: '#9A9284',
                },
                forest: {
                    50: '#EAF0EC',
                    100: '#CFDDD4',
                    300: '#5C8A73',
                    500: '#1F4536',
                    600: '#1A3B2E',
                    700: '#14332A',
                    900: '#0D2019',
                },
                brass: {
                    50: '#F8F1DE',
                    200: '#E3CB8C',
                    400: '#C7A24C',
                    500: '#B08D3E',
                    600: '#8F7130',
                },
                wine: {
                    500: '#7A3B41',
                    600: '#612E33',
                },
            },
            fontFamily: {
                display: ['Fraunces', ...defaultTheme.fontFamily.serif],
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },
            boxShadow: {
                soft: '0 1px 2px rgba(43, 42, 40, 0.04), 0 8px 24px -12px rgba(31, 69, 54, 0.18)',
                softer: '0 1px 2px rgba(43, 42, 40, 0.03), 0 4px 14px -8px rgba(31, 69, 54, 0.12)',
            },
            backgroundImage: {
                'canvas-gradient': 'linear-gradient(180deg, #FBF8F3 0%, #F1EAD9 100%)',
                'hero-gradient': 'radial-gradient(120% 140% at 15% 0%, rgba(31,69,54,0.07) 0%, rgba(31,69,54,0) 55%), linear-gradient(160deg, #FBF8F3 0%, #F1EAD9 100%)',
            },
        },
    },

    plugins: [forms],
};
