/* eslint-disable no-unused-vars */

import Swiper from 'swiper'
import { Autoplay, EffectFade } from 'swiper/modules'

export default (el) => {
	const $slider = el.querySelector('.swiper')

	new Swiper($slider, {
		modules: [Autoplay, EffectFade],
		direction: 'horizontal',
		loop: true,
		speed: 600,
		spaceBetween: 30,
		autoplay: { delay: 3000, disableOnInteraction: true },
		effect: 'fade',
	})
}
