/* global wp, acf */

// This contains a helper that waits for the block editor to be ready before resolving a promise.
// import { whenEditorIsReady } from '@plugins/gutenberg-helpers'

// import exampleBlockJS from '@blocks/example-block'

// Wait for the block editor to be ready
// wp.domReady(() => {
// 	whenEditorIsReady().then(() => {
// 		document.querySelectorAll('.example-block').forEach(function (el) {
// 			Any front end JS that needs to run on the block editor should be called here.
// 			exampleBlockJS(el)
// 		})
// 	})
// })

/**
 * Remove all the core formats from the rich text editor except the allow-listed ones.
 */
wp.domReady(() => {
	// Fetch list of all build in formats
	const coreFormats = wp.data.select('core/rich-text').getFormatTypes()

	// These are the formats we specifically want to allow
	const allowedFormats = [
		'core/bold',
		'core/italic',
		'core/link',
		'core/subscript',
		'core/superscript',
	]

	/* List of all formats, as of WP 6.2.4
    'core/annotation',
    'core/bold',
    'core/code',
    'core/footnote',
    'core/image',
    'core/italic',
    'core/strikethrough',
    'core/underline',
    'core/text-color',
    'core/subscript',
    'core/superscript',
    'core/keyboard',
    'core/language',
    'core/link',
    'core/unknown',
  */

	// If you want to add/remove formats based on the post type, you can do it like this:
	const postType = wp.data.select('core/editor').getCurrentPostType()
	if (postType === 'post-type-slug') {
		allowedFormats.push('core/underline')
	}
	coreFormats.forEach((format) => {
		if (!allowedFormats.includes(format.name)) {
			wp.richText.unregisterFormatType(format.name)
		}
	})
})

/**
 * Remove default styles from core blocks as needed
 */
wp.domReady(() => {
	wp.blocks.unregisterBlockStyle('core/button', ['default', 'outline', 'squared', 'fill'])
	wp.blocks.unregisterBlockStyle('core/quote', ['default', 'large', 'plain'])
	wp.blocks.unregisterBlockStyle('core/pullquote', ['default', 'solid-color'])
	wp.blocks.unregisterBlockStyle('core/table', ['default', 'stripes'])
	wp.blocks.unregisterBlockStyle('core/verse', ['default', 'large'])
	wp.blocks.unregisterBlockStyle('core/code', ['default', 'squared'])
	wp.blocks.unregisterBlockStyle('core/list', ['default', 'large'])
	wp.blocks.unregisterBlockStyle('core/heading', ['default', 'large'])
	wp.blocks.unregisterBlockStyle('core/image', ['default', 'rounded'])
	wp.blocks.unregisterBlockStyle('core/cover', ['default', 'overlay'])
	wp.blocks.unregisterBlockStyle('core/media-text', ['default', 'stacked-on-mobile'])
	wp.blocks.unregisterBlockStyle('core/separator', ['default', 'wide', 'dots'])
	wp.blocks.unregisterBlockStyle('core/paragraph', ['default', 'large'])
})

/**
 * Add some of our own styles to core blocks as needed
 */
wp.domReady(() => {
	// wp.blocks.registerBlockStyle('core/heading', {
	// 	name: 'eyebrow',
	// 	label: 'Eyebrow',
	// })
	// wp.blocks.registerBlockStyle('core/paragraph', {
	// 	name: 'large',
	// 	label: 'Large',
	// })
	// wp.blocks.registerBlockStyle('core/paragraph', {
	// 	name: 'small',
	// 	label: 'Small',
	// })
})

/*
 * Null the parent property on various blocks to prevent them from being placed anywhere
 * except inside of an IS wysiwyg block.
 * Note: Blocks can specifically enable child blocks in their InnerBlocks allowedBlocks
 * property, and this doesn't interfere with that.
 */
wp.domReady(() => {
	const allBlocks = wp.blocks.getBlockTypes()
	allBlocks.forEach((block) => {
		// Apply to all core blocks
		// if (block.name.startsWith('core/')) {
		// 	block.parent = null
		// }
		// Apply to specific list of blocks by name
		// const blocksToSetParent = ['craftedsupply/buttons']
		// if (blocksToSetParent.includes(block.name)) {
		// 	block.parent = ['craftedsupply/wysiwyg']
		// }
		// Apply to all non-IS blocks.
		if (!block.name.startsWith('craftedsupply/')) {
			block.parent = ['craftedsupply/wysiwyg']
		}
	})
})

/**
 * Add custom color palette to ACF color picker
 */
wp.domReady(() => {
	// acf.add_filter('color_picker_args', function (args) {
	// 	args.palettes = ['#E92941', '#CF2030', '#F05730', '#F9A21D', '#333333']
	// 	return args
	// })
})
