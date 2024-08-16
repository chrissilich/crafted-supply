/* eslint-disable no-unused-vars */

import Swiper from 'swiper'
import { Autoplay, EffectFade } from 'swiper/modules'

export default (el) => {
	const $slider = el.querySelector('.swiper'),
		between = parseInt(el.dataset.slideshowBetween) || 3000,
		transition = parseInt(el.dataset.slideshowTransition) || 1500

	console.log('between', between)
	console.log('transition', transition)

	new Swiper($slider, {
		modules: [Autoplay, EffectFade],
		direction: 'horizontal',
		loop: true,
		speed: transition,
		autoplay: { delay: between, disableOnInteraction: true },
		effect: 'fade',
	})

	// Make the hamburger nav change color appropriately
	window.addEventListener('scroll', () => {
		const coords = el.getBoundingClientRect()
		if (coords.top > 120 || coords.bottom < 120) {
			document.querySelector('html').classList.remove('-white-menu-mode')
		} else {
			document.querySelector('html').classList.add('-white-menu-mode')
		}
	})
}
