/**
 * Add JS functionality for full card hover/focus, so that we don't have to put
 * gobs of text inside anchors which is not very screen reader friendly.
 */
export default () => {
	$('html')
		.on('click', '.js-link-event', (e) => {
			if ($(e.target).prop('tagName') === 'A') {
				return
			}

			const $resultLink = $(e.currentTarget).find('.js-link-event-link')

			if ($resultLink.length) {
				$resultLink.get(0).click()
			}
		})
		.on('focusin focusout', '.js-link-event .js-link-event-link', (e) => {
			$(e.currentTarget).closest('.js-link-event').toggleClass('-focused')
		})
}
