document.querySelector('#mobile-overlay-menu .xt-dismiss').addEventListener('click', () => {
    for (const toggle of document.querySelectorAll('#mobile-overlay-menu .xt-toggle')) {
        toggle.dispatchEvent(new CustomEvent('off.trigger.xt.toggle'))
    }
})