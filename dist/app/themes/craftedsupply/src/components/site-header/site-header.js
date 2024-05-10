export default (el) => {
	const $siteHeader = $(el)
	const $dropdownTriggers = $(el).find('nav > ul > li > button')
	const $dropdowns = $(el).find('nav > ul > li > ul')

	$dropdownTriggers.on('click', ({ currentTarget }) => {
		const $trigger = $(currentTarget)
		const connectedDropdown = $trigger.attr('aria-controls')
		const $dropdown = $(`#${connectedDropdown}`)

		// unclick other buttons
		$dropdownTriggers.not($trigger).attr('aria-expanded', 'false')

		// hide other dropdowns
		$dropdowns.not($dropdown).attr('aria-hidden', 'true')

		$trigger.attr('aria-expanded', $trigger.attr('aria-expanded') === 'true' ? 'false' : 'true')
		$dropdown.attr('aria-hidden', $dropdown.attr('aria-hidden') === 'true' ? 'false' : 'true')

		// if we ended up opening one, set the header to open
		$siteHeader.attr('data-open', $dropdown.attr('aria-hidden') === 'true' ? 'false' : 'true')
	})

	const closeNavFromElsewhere = () => {
		$dropdowns.attr('aria-hidden', 'true')
		$dropdownTriggers.attr('aria-expanded', 'false')
		$siteHeader.attr('data-open', false)
	}

	// close dropdowns when clicking outside of them
	$(document).on('click', ({ target }) => {
		if ($(target).closest('nav').length === 0) {
			closeNavFromElsewhere()
		}
	})

	// listen for custom event to close nav from elsewhere
	$(document).on('closenav', closeNavFromElsewhere)
}
