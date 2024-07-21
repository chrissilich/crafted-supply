// -----------------------------------------------------------------------------
// Plugin Includes
// -----------------------------------------------------------------------------
import Animate from '@plugins/animate'
import anchorSmoothScroll from '@plugins/anchor-smooth-scroll'
import externalLinks from '@plugins/external-links'

// -----------------------------------------------------------------------------
// Component Includes
// -----------------------------------------------------------------------------
import siteHeader from '@components/site-header'

// -----------------------------------------------------------------------------
// Block Includes
// -----------------------------------------------------------------------------
import fullscreenSlideshow from '@blocks/fullscreen-slideshow'

const initializer = {
	components: [
		// regular components
		['.site-header', siteHeader],
		// blocks
		['.fullscreen-slideshow-block', fullscreenSlideshow],
	],
	plugins: [
		// cssSizeVariables,
		// extendJquery,
		externalLinks,
		anchorSmoothScroll,
	],
}

class CraftedSupplyJS {
	constructor(initializer) {
		this.initializer = initializer
		this.init()
	}

	init() {
		this.callAll(this.initializer.plugins)
		new Animate('[data-animation]')
		this.callAll(this.initializer.components, this.initComponent)

		// -----------------------------------------------------------------------------
		// Service Worker registration
		// -----------------------------------------------------------------------------
		if ('serviceWorker' in navigator) {
			window.addEventListener('load', () => {
				navigator.serviceWorker.register('/service-worker.js', {
					scope: '/',
				})
			})
		}
	}

	callAll(items, fn = (fn) => fn()) {
		items.forEach(fn)
	}

	initComponent([selector, handler]) {
		const $component = $(selector)

		if (!$component.length) {
			return null
		}

		return $component.each((i, item) => {
			return handler(item)
		})
	}
}

;(($) => {
	// Document Ready
	$(() => {
		new CraftedSupplyJS(initializer)
	})
})($)
