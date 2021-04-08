import { Xt } from 'xtendui'

Xt.mount({
    matches: '#infinitescroll',
    mount: ({ ref }) => {
        const unmountInfinitescroll = mountInfinitescroll({ ref })

        return () => {
            unmountInfinitescroll()
        }
    },
})

const mountInfinitescroll = ({ ref }) => {
    let self = new Xt.Infinitescroll(ref, {
        get: 'pagina',
        max: parseInt(document.querySelector('[data-page-count]').dataset.pageCount),
        elements: {
            itemsContainer: '[data-page-count]',
            item: ':scope > *',
            scrollUp: '[data-xt-infinitescroll-up]',
            scrollDown: '[data-xt-infinitescroll-down]',
            spaceAdditional: '[data-xt-infinitescroll-up]',
            pagination: '[data-xt-infinitescroll-pagination]',
        },
        events: {
            scrollUp: true,
            scrollDown: false,
        }
    })

    return () => {
        self.destroy()
        self = null
    }
}