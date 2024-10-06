export default ($el) => {
	const $people = $el.querySelectorAll('.people-grid-block-person')

	$people.forEach(($person) => {
		$person.addEventListener('touchstart', () => {
			showBio($person)
			hideBiosExcept($person)
		})

		$person.addEventListener('mouseenter', () => {
			showBio($person)
			hideBiosExcept($person)
		})

		$person.addEventListener('mouseleave', () => {
			hideBiosExcept(null)
		})
	})

	// if a touchstart event is detected on window that didnt come up from a "person" element, hide all bios
	window.addEventListener('touchstart', (e) => {
		if (!e.target.closest('.people-grid-block-person')) {
			hideBiosExcept(null)
		}
	})

	const hideBiosExcept = ($person) => {
		$people.forEach(($p) => {
			if ($p !== $person) {
				$p.classList.remove('-show-bio')
			}
		})
	}

	const showBio = ($person) => {
		$person.classList.add('-show-bio')

		// Calculate the height of the Person's name and job title, add them up,
		// and set it as a CSS variable for use in the padding of the gray box
		const $title = $person.querySelector('.people-grid-block-person-title')
		const $jobTitle = $person.querySelector('.people-grid-block-person-job_title')
		const height = $title.offsetHeight + $jobTitle.offsetHeight + 28
		$person.style.setProperty('--bio-titles-height', `${height}px`)
	}
}
