export default ($el) => {
	const $currentNavItem = document.querySelector('nav.hamburger-nav a[aria-current="page"]')
	const $breadcrumb = $el.querySelector('.breadcrumb')

	// console.log('currentNavItem:', $currentNavItem)

	if ($currentNavItem) {
		$breadcrumb.innerText = $currentNavItem.innerText
	}
}
