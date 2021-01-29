import { Xt } from 'xtendui'
import gsap from 'gsap'
import { ScrollToPlugin } from 'gsap/ScrollToPlugin'
import 'xtendui/src/addons/scrolltoanchor'
gsap.registerPlugin(ScrollToPlugin)

Xt.mount.push({
    matches: 'body',
    mount: ({ object }) => {
        // init

        let self = new Xt.Scrolltoanchor(object, {
            // options
        })

        // change

        const eventChange = () => {
            // val
            let pos = self.position - self.scrollSpace - self.scrollDistance
            const min = 0
            const max = self.scrollElement.scrollHeight - self.scrollElement.clientHeight
            pos = pos < min ? min : pos
            pos = pos > max ? max : pos
            // scroll
            gsap.killTweensOf(self.scrollElement)
            gsap.to(self.scrollElement, {
                scrollTo: pos,
                duration: 1,
                ease: 'quart.inOut',
            })
        }

        self.object.addEventListener('change.xt.scrolltoanchor', eventChange)

        // unmount

        return () => {
            self.destroy()
            self = null
        }
    }
})