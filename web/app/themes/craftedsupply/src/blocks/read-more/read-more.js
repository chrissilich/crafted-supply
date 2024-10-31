export default (el) => {
	// Find preceding paragraph block inside wysiwyg block
	const $el = $(el),
		$previousP = $el.prev('p')

	const controls = $el.attr('aria-controlledby')

	const $readMore = $(`<a class="read-more-link" aria-controls="${controls}">Read More &gt;</a>`)

	// Insert the read more button after the paragraph
	$previousP.append($readMore)

	// How to actually open the read more block
	const openReadMore = function () {
		$el.attr('aria-expanded', true)
		$readMore.remove()
	}

	// When the read more button is clicked, toggle the class on the parent block
	$readMore.on('click', openReadMore)

	// if there is no previous paragraph, open the read more block right away
	if (!$previousP.length) {
		// eslint-disable-next-line no-console
		console.warn('No previous paragraph found for read more block, opening it right away')
		openReadMore()
	}
}
