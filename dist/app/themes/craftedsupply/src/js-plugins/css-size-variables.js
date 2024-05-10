import { throttle } from 'throttle-debounce'

export default () => {
	const $body = document.body
	let $pageContainer = document.querySelector('.page-container'),
		$viewport = window

	const findElements = function () {
		if (!$pageContainer) {
			$pageContainer = document.querySelector('.editor-styles-wrapper .is-root-container')
		}
		const $gutenbergViewport = document.querySelector(
			'.interface-navigable-region:has(.is-desktop-preview)',
		)
		if ($gutenbergViewport) {
			$viewport = $gutenbergViewport
		}
	}

	const changeSizeVariables = function () {
		if ($viewport.offsetWidth) {
			$body.style.setProperty('--viewport-width', $viewport.offsetWidth + 'px')
			$body.style.setProperty('--viewport-height', $viewport.offsetHeight + 'px')
		} else {
			$body.style.setProperty('--viewport-width', $viewport.innerWidth + 'px')
			$body.style.setProperty('--viewport-height', $viewport.innerHeight + 'px')
		}
		if ($pageContainer) {
			$body.style.setProperty('--main-container-width', $pageContainer.offsetWidth + 'px')
			$body.style.setProperty('--main-container-height', $pageContainer.offsetHeight + 'px')
		}
	}

	const throttledChangeSizeVariables = throttle(50, changeSizeVariables)

	window.addEventListener('load', findElements)

	window.addEventListener('resize', throttledChangeSizeVariables)
	window.addEventListener('orientationchange', throttledChangeSizeVariables)
	window.addEventListener('load', throttledChangeSizeVariables)
	changeSizeVariables()
}
