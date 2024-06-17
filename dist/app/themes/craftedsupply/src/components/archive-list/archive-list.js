/* global FWP */
export default (el) => {
	const $selections = $(el).find('.archive-list-meta-selections')

	$(document)
		// This JS event gets triggered before FacetWP begins the refresh process.
		//
		// It happens before any AJAX is requested, and before the URL hash gets updated.
		// This event is useful for modifying any FWP variables before getting sent to the server.
		.on('facetwp-refresh', () => {
			if (!FWP.buildQueryString()) {
				$selections.hide()
			}
		})
		// This JS event gets triggered when FacetWP finishes refreshing.
		//
		// It’s triggered after a user interacts with a facet or pagination control.
		// This event is useful for modifying facet output after being rendered.
		.on('facetwp-loaded', () => {
			if (FWP.buildQueryString()) {
				$selections.show()
			}
		})

	const setUpAccordions = () => {
		// Facet accordions
		const $facetAccordions = $(el).find('.archive-list-facet')

		$facetAccordions.each((i, $facetAccordion) => {
			$facetAccordion = $($facetAccordion)

			const $trigger = $facetAccordion.find('.archive-list-facet-trigger')
			const contentID = $trigger.attr('aria-controls')
			const $content = $facetAccordion.find('#' + contentID)

			// console.log('$trigger', $trigger)
			// console.log('contentID', contentID)
			// console.log('$content', $content)

			// Hide empty accordions
			if ($content.find('.facetwp-facet').children().length === 0) {
				$facetAccordion.addClass('-empty')
			}

			// Set up click event
			$trigger.on('click', () => {
				const closed = $content.attr('aria-hidden') === 'true'
				// console.log('closed?', closed, contentID, $content)
				$trigger.attr('aria-expanded', closed ? 'true' : 'false')
				$content.attr('aria-hidden', closed ? 'false' : 'true')
			})

			// If any have stuff selected on page load, open them
			if ($content.find('.facetwp-checkbox[aria-checked="true"]').length) {
				$trigger.attr('aria-expanded', 'true')
				$content.attr('aria-hidden', 'false')
			}
		})

		// Make topics in the archive list clickable
		// When clicked, the corresponding checkbox is checked (if unchecked)
		// and the corresponding accordion is opened (if closed)
		$(document).on('click', '.js-select-topic', (e) => {
			e.preventDefault()
			e.stopPropagation()
			const value = $(e.target).data('value')

			const $targetCheckbox = $(`.facetwp-checkbox[data-value="${value}"]`)

			const $targetCheckboxParentAccordionTrigger = $targetCheckbox
				.parents('.archive-list-facet')
				.find('.archive-list-facet-trigger')

			if ($targetCheckbox.attr('aria-checked') === 'false') {
				$targetCheckbox.click()
			}

			if ($targetCheckboxParentAccordionTrigger.attr('aria-expanded') === 'false') {
				$targetCheckboxParentAccordionTrigger.click()
			}
		})
	}

	// hack to make this go to the bottom of the call stack, to wait for facet to do its thing
	setTimeout(setUpAccordions, 1)
}
