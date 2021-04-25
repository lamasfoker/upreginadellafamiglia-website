import { Xt } from 'xtendui'

Xt.mount({
    matches: '#overlay-cookie-banner',
    mount: ({ ref }) => {
        const unmountBanner = mountBanner({ ref })

        return () => {
            unmountBanner()
        }
    },
})

const mountBanner = ({ ref }) => {
    if (localStorage.getItem('are-cookies-accepted') === null) {
        ref.querySelector('button').addEventListener('click', () => {
            localStorage.setItem('are-cookies-accepted', 'true')
        })
        ref.dispatchEvent(new CustomEvent('on.trigger.xt.overlay'))
    }

    return () => {}
}