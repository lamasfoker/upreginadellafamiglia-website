const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    presets: [require('tailwindcss/defaultConfig'), require('xtendui/tailwind.preset')],
    purge: {
        content: ['./node_modules/xtendui/src/**/*[!.css].js', './src/**/*.ejs', './src/**/*.css', './src/**/*.js'],
        options: {
            safelist: {
                greedy: [
                    // popperjs
                    /^data-popper-/,
                ],
            },
        },
    },
    theme: {
        container: {
            center: true,
            padding: {
                DEFAULT: '0',
                sm: '2rem',
                md: '2.25rem',
                lg: '2.5rem',
                xl: '2.75rem',
            },
        },
        extend: {
            width: {
                '1/7': '14.2857143%'
            },
            colors: {
                "theme-1": {
                    light: '#f2aa98',
                    DEFAULT: '#E75532',
                    dark: '#7d240e'
                },
                "theme-2": {
                    light: '#ede6c4',
                    DEFAULT: '#DBCD8A',
                    dark: '#88782a'
                },
                "theme-3": {
                    light: '#a4dcd8',
                    DEFAULT: '#4ABAB3',
                    dark: '#235e5a'
                },
                "theme-4": {
                    light: '#80a4d4',
                    DEFAULT: '#2B4F7F',
                    dark: '#15273f'
                },
                "theme-5": {
                    light: '#a98686',
                    DEFAULT: '#3A2929',
                    dark: '#1d1414'
                }
            },
            fontFamily: {
                sans: ['"Nunito"', ...defaultTheme.fontFamily.sans],
            }
        },
        screens: {
            'xxs': '370px',
            'xs': '576px',
            'sm': '768px',
            'md': '992px',
            'lg': '1200px',
            'xl': '1600px',
        },
        xtendui: {
            layout: {
                component: theme => ({
                    '::selection': {
                        backgroundColor: theme('colors.theme-1.DEFAULT'),
                        color: theme('colors.theme-2.light')
                    },
                }),
            },
        },
    }
}