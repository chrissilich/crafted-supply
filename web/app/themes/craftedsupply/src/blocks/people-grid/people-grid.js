export default ($el) => {
	const $people = $el.querySelectorAll('.people-grid-block-person')

	$people.forEach(($person) => {
		$person.addEventListener('touchstart', () => {
			$person.classList.toggle('-show-bio')
			hideOthers($person)
		})

		$person.addEventListener('mouseenter', () => {
			$person.classList.toggle('-show-bio')
			hideOthers($person)
		})

		$person.addEventListener('mouseleave', () => {
			$person.classList.toggle('-show-bio')
		})
	})

	// if a touchstart event is detected on window that didnt come up from a "person" element, hide all bios
	window.addEventListener('touchstart', (e) => {
		if (!e.target.closest('.people-grid-block-person')) {
			hideOthers(null)
		}
	})

	const hideOthers = ($person) => {
		$people.forEach(($p) => {
			if ($p !== $person) {
				$p.classList.remove('-show-bio')
			}
		})
	}
}
