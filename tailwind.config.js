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
}