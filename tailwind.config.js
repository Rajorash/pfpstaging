const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: [
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

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
                blue: '#3E04E2'
            },
            boxShadow: {
                shadow1: '0px 75px 100px rgba(206, 208, 229, 0.71)',
                shadow2: '0px 25px 50px rgba(206, 208, 229, 0.5)',
                shadow3: '0px 50px 100px rgba(206, 208, 229, 0.5)',
                shadow4: '0px 25px 75px rgba(206, 208, 229, 0.5)'
            }
        },
    },

    variants: {
        extend: {
            opacity: ['disabled'],
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/custom-forms'),
        require('@tailwindcss/typography')
    ],
};
