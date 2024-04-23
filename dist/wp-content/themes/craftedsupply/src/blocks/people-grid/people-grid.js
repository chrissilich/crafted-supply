export default ($el) => {
	const $navItems = $el.querySelectorAll('.people-grid-block-nav-item'),
		$sections = $el.querySelectorAll('.people-grid-block-sections-item')

	const clickNavItem = (event) => {
		// prevent only the regular clicks
		if (!event.metaKey && !event.ctrlKey && !event.shiftKey) {
			event.preventDefault()
		}

		const $clickTarget = event.currentTarget
		const clickTargetControls = $clickTarget.getAttribute('aria-controls')
		const sectionId = clickTargetControls.replace('people-grid-section--', '')

		// set the query param
		const url = new URL(window.location)
		url.searchParams.set('section', sectionId)
		window.history.pushState({}, '', url)

		$navItems.forEach(($navItem) => {
			if ($clickTarget === $navItem) {
				$navItem.setAttribute('aria-expanded', true)
			} else {
				$navItem.setAttribute('aria-expanded', false)
			}
		})

		$sections.forEach(($section) => {
			if ($section.getAttribute('id') === clickTargetControls) {
				$section.setAttribute('aria-hidden', false)
			} else {
				$section.setAttribute('aria-hidden', true)
			}
		})
	}

	$navItems.forEach(($navItem) => {
		$navItem.addEventListener('click', clickNavItem)
	})

	// check to see if the URL has a "section" query parameter
	const urlParams = new URLSearchParams(window.location.search)
	const sectionParam = urlParams.get('section')
	if (sectionParam) {
		$navItems.forEach(($navItem) => {
			// console.log('$navItem', $navItem)
			if ($navItem.getAttribute('aria-controls') === 'people-grid-section--' + sectionParam) {
				$navItem.click()
			}
		})
	}
}
