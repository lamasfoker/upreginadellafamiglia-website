module.exports = {
    presets: [require('tailwindcss/defaultConfig'), require('xtendui/tailwind.preset')],
    purge: {
        content: ['./src/**/*.html', './src/**/*.css', './src/**/*.js'], // put your purge content
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