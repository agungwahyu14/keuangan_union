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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // ── Sidebar ──────────────────────────────────────
                sidebar: {
                    DEFAULT: '#1C1C1E',
                    hover:   '#2C2C2E',
                    border:  '#3A3A3C',
                    text:    '#AEAEB2',
                    label:   '#636366',
                },
                // ── Primary: Amber-Gold ───────────────────────────
                gold: {
                    50:      '#FFFBEB',
                    100:     '#FEF3C7',
                    200:     '#FDE68A',
                    300:     '#FCD34D',
                    400:     '#FBBF24',
                    500:     '#A07800',  // primary
                    600:     '#8B6700',
                    700:     '#6B4F00',
                    DEFAULT: '#A07800',
                },
                // ── Highlight: Kuning asli (aksen di atas gelap) ──
                accent: {
                    DEFAULT: '#FFEA6C',
                    hover:   '#FFE033',
                    dark:    '#B8860B',
                },
                // ── Pemasukan ─────────────────────────────────────
                income: {
                    50:      '#ECFDF5',
                    100:     '#D1FAE5',
                    500:     '#1D9E75',
                    600:     '#178A63',
                    DEFAULT: '#1D9E75',
                },
                // ── Pengeluaran ───────────────────────────────────
                expense: {
                    50:      '#FEF2F2',
                    100:     '#FEE2E2',
                    500:     '#C0392B',
                    600:     '#A93226',
                    DEFAULT: '#C0392B',
                },
                // ── HPP ───────────────────────────────────────────
                hpp: {
                    50:      '#FFF7ED',
                    100:     '#FFEDD5',
                    500:     '#BA7517',
                    600:     '#9A6112',
                    DEFAULT: '#BA7517',
                },
            },
        },
    },

    plugins: [forms],
};
