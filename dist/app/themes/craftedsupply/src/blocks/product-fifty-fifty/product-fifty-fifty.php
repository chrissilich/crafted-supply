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

$context['block'] = $block;
$context['is_preview'] = $is_preview;
$context['fields'] = get_fields() ?: [
	'text' => 'Body Copy Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in dui mauris. Vivamus hendrerit arcu sed erat molestie vehicula.',
	'image_side' => 'left',
];


// Take the title field and convert any underscores to italics
$context['fields']['text'] = preg_replace( '/_([^_]+)_/', '<span class="-vendor">$1</span>', $context['fields']['text'] );


Timber::render( 'product-fifty-fifty.twig', $context );
