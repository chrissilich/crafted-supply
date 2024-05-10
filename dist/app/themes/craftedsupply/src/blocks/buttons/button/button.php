<?php
/*
* Variables available in the block
* $block (array) The block settings and attributes.
* $content (string) The block inner HTML (empty).
* $is_preview (boolean) True during backend preview render.
* $post_id (integer) The post ID the block is rendering content against. This is either the post ID currently being displayed inside a query loop, or the post ID of the post hosting this block.
* $context (array) The context provided to the block by the post or its parent block.
*/

$context = Timber::context();

$context['fields'] = get_fields();
$context['block'] = $block;
$context['is_preview'] = $is_preview;

// derive the button style from the classname applied by gutenberg. 
// e.g. is-style-donation => donation
//      is-style-secondary => secondary
if (isset($block['className'])) {
	$context['style'] =  str_replace('is-style-', '', $block['className']);
} else {
	$context['style'] = "primary";
}

Timber::render( 'button.twig', $context );