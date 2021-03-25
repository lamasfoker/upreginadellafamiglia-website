const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    presets: [require('tailwindcss/defaultConfig'), require('xtendui/tailwind.preset')],
    purge: {
        content: ['./node_modules/xtendui/src/**/*[!.css].js', './templates/**/*.html.twig', './assets/**/*.css', './assets/**/*.js'],
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
        extend: {
            screens: {
                'xs': '500px',
            },
            container: {
                center: true,
                padding: {
                    DEFAULT: '1.25rem',
                    sm: '1.5rem',
                    md: '1.5rem',
                    lg: '2rem',
                    xl: '2rem',
                },
            },
            width: {
                '1/7': '14.2857143%'
            },
            colors: {
                "color-1": {
                    light: '#eaeaf2',
                    DEFAULT: '#5a94c7',
                    dark: '#165185'
                },
                "color-2": {
                    DEFAULT: '#e85a37'
                }
            },
            fontFamily: {
                sans: ['"Nunito"', ...defaultTheme.fontFamily.sans],
            }
        },
        xtendui: {
            layout: {
                component: theme => ({
                    '::selection': {
                        backgroundColor: theme('colors.color-1.DEFAULT'),
                        color: theme('colors.white')
                    },
                }),
            },
        },
    }
}