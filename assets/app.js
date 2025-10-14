import './styles/app.css'

import { Xt } from 'xtendui'
import 'xtendui/src/drop'
import gsap from 'gsap'
import 'xtendui/src/form'
import 'xtendui/src/slider'
import 'xtendui/src/toggle'
import 'xtendui/src/overlay'
import 'xtendui/src/infinitescroll'
import './scripts/mobile-menu'
import './scripts/infinitescroll'
import './scripts/slider'
import './scripts/cookie-banner'
import './scripts/autogenerate-heading-id'

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

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').then(registration => {
            console.log('SW registered: ', registration)
        }).catch(registrationError => {
            console.log('SW registration failed: ', registrationError)
        })
    })
}
