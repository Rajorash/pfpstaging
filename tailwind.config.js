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
            colors: {
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
