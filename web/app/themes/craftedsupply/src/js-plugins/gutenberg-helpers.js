/* global wp */

// import { select, subscribe } from '@wordpress/data'

export function whenEditorIsReady() {
	return new Promise((resolve) => {
		const unsubscribe = wp.data.subscribe(() => {
			// This will trigger after the initial render blocking, before the window load event
			// This seems currently more reliable than using __unstableIsEditorReady
			if (
				wp.data.select('core/editor').isCleanNewPost() ||
				wp.data.select('core/block-editor').getBlockCount() > 0
			) {
				unsubscribe()
				resolve()
			}
		})
	})
}
