import { Xt } from 'xtendui'
import gsap from 'gsap'

Xt.mount({
    matches: '.xt-slider',
    mount: ({ ref }) => {
        const unmountSlider = mountSlider({ ref })

        return () => {
            unmountSlider()
        }
    },
})

const mountSlider = ({ ref }) => {
    const dragTime = 1
    const dragEase = 'quint.out'

    const self = new Xt.Slider(ref, {})

    const dragposition = () => {
        gsap.killTweensOf(self.detail)
        gsap.to(self.detail, {
            dragPosition: self.detail.dragFinal,
            duration: self.initial || self.detail.dragging ? 0 : dragTime,
            ease: dragEase,
        })
        gsap.killTweensOf(self.dragger)
        gsap.to(self.dragger, {
            x: self.detail.dragFinal,
            duration: self.initial || self.detail.dragging ? 0 : dragTime,
            ease: dragEase,
        })
    }

    self.dragger.addEventListener('dragposition.xt.slider', dragposition)

    return () => {}
}
