export default (el) => {
	// Find preceding paragraph block inside wysiwyg block
	const $el = $(el),
		$previousP = $el.prev('p')

	const controls = $el.attr('aria-controlledby')

	const $readMore = $(`<a class="read-more-link" aria-controls="${controls}">Read More &gt;</a>`)

	// Insert the read more button after the paragraph
	$previousP.append($readMore)

	// When the read more button is clicked, toggle the class on the parent block
	$readMore.on('click', function () {
		$el.attr('aria-expanded', true)
		$readMore.remove()
	})
}
