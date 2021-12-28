const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: {
        content: [
            './vendor/laravel/jetstream/**/*.blade.php',
            './storage/framework/views/*.php',
            './resources/**/*.blade.php',
            './resources/**/*.js',
        ],
        safelist: [
            'bg-light_blue',
            'bg-light_purple',
            'bg-light_purple2',
            'bg-dark_gray',
            'bg-dark_gray2',
            'bg-light_gray',
            'bg-blue',
            'bg-green',
            'bg-dashboard',
            'bg-revenue',
            'bg-pretotal',
            'bg-salestax',
            'bg-prereal',
            'bg-postreal',
            'border-revenue',
            'border-pretotal',
            'border-salestax',
            'border-prereal',
            'border-postreal',


            'text-light_blue',
            'text-light_purple',
            'text-light_purple2',
            'text-dark_gray',
            'text-dark_gray2',
            'text-light_gray',
            'text-blue',
            'text-green',
            'text-dashboard',
            'text-revenue',
            'text-pretotal',
            'text-salestax',
            'text-prereal',
            'text-postreal',
        ]
    },

    theme: {
        extend: {
            fontFamily: {
                sans: ['Questrial', ...defaultTheme.fontFamily.sans],
            },
            borderWidth: {
               '12': '12px',
               '16': '16px',
               '20': '20px',
            },
            colors: {
                'san-marino': {
                    DEFAULT: '#576FB7',
                    '50': '#F8F9FC',
                    '100': '#E6EAF4',
                    '200': '#C2CBE5',
                    '300': '#9FACD6',
                    '400': '#7B8EC6',
                    '500': '#576FB7',
                    '600': '#42589A',
                    '700': '#324376',
                    '800': '#232F52',
                    '900': '#141B2E'
                },
                'atlantis': {
                    DEFAULT: '#B2CC33',
                    '50': '#F7FAEB',
                    '100': '#F0F5D6',
                    '200': '#E0EBAD',
                    '300': '#D1E085',
                    '400': '#C2D65C',
                    '500': '#B2CC33',
                    '600': '#8FA329',
                    '700': '#6B7A1F',
                    '800': '#475214',
                    '900': '#24290A'
                },
                light_blue: '#CED0E5',
                light_purple: '#DBCEFF',
                light_purple2: '#F6F4FE',
                dark_gray: '#2D3748',
                dark_gray2: '#2F2F2F',
                light_gray: '#848484',
                blue: '#3E04E2',
                green: '#94B140',
                dashboard: '#FBFBFF',
                revenue: '#0E1747',
                pretotal: '#475EE0',
                salestax: '#2840C7',
                prereal: '#1F264D',
                postreal: '#1E2F94',
            },
            boxShadow: {
                shadow1: '0px 75px 100px rgba(206, 208, 229, 0.71)',
                shadow2: '0px 25px 50px rgba(206, 208, 229, 0.5)',
                shadow3: '0px 50px 100px rgba(206, 208, 229, 0.5)',
                shadow4: '0px 25px 75px rgba(206, 208, 229, 0.5)'
            },
            minWidth: {
                '8': '2rem',
                '12': '3rem',
                '16': '4rem',
                '20': '5rem',
                '24': '6rem',
                '28': '7rem',
                '32': '8rem'
            },
            cursor: {
                'copy': 'copy'
            }
        },
    },

    variants: {
        extend: {
            opacity: ['disabled'],
            backgroundColor: ['active'],
            textColor: ['active'],
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography')
    ],
};
