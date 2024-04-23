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
$context['post'] = Timber::get_post($post_id);

$context['all'] = Timber::get_posts( array(
	'post_type' => 'person',
	'posts_per_page' => -1,
	'orderby' => 'meta_value',
	'meta_key' => 'last_name',
	'order' => 'ASC',
	'meta_query' => array(
		array(
			'key' => 'all',
			'value' => true,
			'compare' => '=',
		),
	),
) );


Timber::render( 'people-grid.twig', $context );