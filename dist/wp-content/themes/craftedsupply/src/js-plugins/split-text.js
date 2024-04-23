// Splits a string of text into individual characters and wraps them in spans, returning the HTML as a string.
export default ($el) => {
	const text = $el.textContent
	const splitText = text
		.split('')
		.map((char) => {
			return `<span>${char}</span>`
		})
		.join('')
	$el.innerHTML = splitText
}

export const splitTextByWord = ($el) => {
	const text = $el.textContent
	const splitText = text
		.split(' ')
		.map((word) => {
			return `<span>${word}</span>`
		})
		.join(' ')
	$el.innerHTML = splitText
}

export const splitTextByLine = ($el) => {
	const text = $el.textContent
	const splitTextByWord = text
		.split(' ')
		.map((word) => {
			return `<span>${word} </span>`
		})
		.join(' ')

	$el.innerHTML = splitTextByWord

	// Now that things are broken out by words, we can break them out by lines.
	const $words = $el.querySelectorAll('span')
	const $lines = [[]]
	let prevTop = null

	$words.forEach(($word) => {
		const top = $word.getBoundingClientRect().top
		if (prevTop && top !== prevTop) {
			$lines.push([])
		}
		$lines[$lines.length - 1].push($word)
		prevTop = top
	})

	$lines.forEach(($line) => {
		const $lineWrapper = document.createElement('div')
		$lineWrapper.classList.add('line')
		$lineWrapper.append(...$line)
		$el.append($lineWrapper)
	})

	// console.log($lines)
}
