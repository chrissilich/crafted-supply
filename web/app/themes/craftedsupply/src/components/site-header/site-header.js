export default (el) => {
	// const $siteHeader = $(el)
	const $hamburgerBtn = $('.hamburger-button')
	const $hamburgerNav = $('.hamburger-nav')

	const closeNav = () => {
		console.log('closeNav')
		$hamburgerNav.attr('aria-hidden', 'true')
		$hamburgerBtn.attr('aria-expanded', 'false')
	}

	const openNav = () => {
		console.log('openNav')
		$hamburgerNav.attr('aria-hidden', 'false')
		$hamburgerBtn.attr('aria-expanded', 'true')
	}

	// when the hamburger is clicked, toggle the nav
	$hamburgerBtn.on('click', () => {
		console.log('$hamburgerNav hidden state', $hamburgerNav.attr('aria-hidden'))
		$hamburgerNav.attr('aria-hidden') === 'true' ? openNav() : closeNav()
	})

	// close dropdowns when clicking outside of them
	// $(document).on('click', ({ target }) => {
	// 	if ($(target).closest('nav').length === 0) {
	// 		closeNav()
	// 	}
	// })

	// listen for custom event to close nav from elsewhere
	$(document).on('closenav', closeNav)
}
