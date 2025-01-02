// -----------------------------------------------------------------------------
// Plugin Includes
// -----------------------------------------------------------------------------
import Animate from '@plugins/animate'
import anchorSmoothScroll from '@plugins/anchor-smooth-scroll'
import externalLinks from '@plugins/external-links'
import jsLinkEvent from '@plugins/js-link-event'

// -----------------------------------------------------------------------------
// Component Includes
// -----------------------------------------------------------------------------
import siteHeader from '@components/site-header'
import hamburgerButton from '@components/hamburger-button'

// -----------------------------------------------------------------------------
// Block Includes
// -----------------------------------------------------------------------------
import fullscreenSlideshow from '@blocks/fullscreen-slideshow'
import peopleGrid from '@blocks/people-grid'
import readMore from '@blocks/read-more'

const initializer = {
	components: [
		// regular components
		['.site-header', siteHeader],
		['.hamburger-button', hamburgerButton],
		// blocks
		['.fullscreen-slideshow-block', fullscreenSlideshow],
		['.people-grid-block', peopleGrid],
		['.read-more-block', readMore],
	],
	plugins: [
		// cssSizeVariables,
		// extendJquery,
		jsLinkEvent,
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
