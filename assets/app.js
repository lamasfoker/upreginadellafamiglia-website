import './styles/app.css'

import { Xt } from 'xtendui'
import 'xtendui/src/core/drop'
import gsap from 'gsap'
import 'xtendui/src/core/slider'
import './scripts/layout'

/**
 * animations setup
 */

gsap.config({ force3D: false })

if (Xt.durationTimescale === 1000) {
    // instant animations accessibility
    gsap.globalTimeline.timeScale(1000)
    // double auto time accessibility
    Xt.autoTimescale = 0.5
}

const animationResponsive = () => {
    // faster javascript animations on small screens
    if (Xt.durationTimescale !== 1000 && matchMedia('(max-width: 767px)').matches) {
        gsap.globalTimeline.timeScale(1.5)
        Xt.durationTimescale = 1.5
    } else {
        gsap.globalTimeline.timeScale(1)
        Xt.durationTimescale = 1
    }
}
addEventListener('resize', animationResponsive)
animationResponsive()