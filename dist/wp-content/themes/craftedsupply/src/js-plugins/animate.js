/* eslint-disable no-extra-boolean-cast */
export default class Animate {
	constructor(elements, rootMargin) {
		this.elements = $(elements)
		this.rootMargin = rootMargin || '0px 0px -100px 0px'
		this.observe()
	}

	observe() {
		if (!!window.IntersectionObserver) {
			const observer = new IntersectionObserver(
				(entries, observer) => {
					entries.forEach((entry) => {
						if (entry.isIntersecting) {
							const $element = $(entry.target),
								data = $element.data(),
								animation = data.animation,
								delay = data.animationDelay || null,
								duration = data.animationDuration || null
							$element
								.css({
									animationDelay: delay ? `${delay}ms` : '',
									animationDuration: duration ? `${duration}ms` : '',
								})
								.addClass(`-animated ${animation}`)
							observer.unobserve(entry.target)
						}
					})
				},
				{
					rootMargin: this.rootMargin,
				},
			)
			this.elements.each((index, element) => {
				observer.observe(element)
			})
		}
	}
}
