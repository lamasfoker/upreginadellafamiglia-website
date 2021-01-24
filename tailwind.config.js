module.exports = {
    presets: [require('tailwindcss/defaultConfig'), require('xtendui/tailwind.preset')],
    purge: {
        content: ['./assets/**/*.css', './assets/**/*.js'], // put your purge content
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
            width: {
                '1/7': '14.2857143%'
            }
        }
    }
}