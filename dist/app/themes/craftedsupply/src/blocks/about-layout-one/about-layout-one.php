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
$context['post'] = Timber::get_post($post_id);
$context['fields'] = get_fields();

// Take the title field and convert any underscores to italics
$context['fields']['title'] = preg_replace( '/_([^_]+)_/', '<em>$1</em>', $context['fields']['title'] );


Timber::render( 'about-layout-one.twig', $context );