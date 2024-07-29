<?php

/**
 * Variables available in the block
 *
 * $block (array) The block settings and attributes.
 * $content (string) The block inner HTML (empty).
 * $is_preview (boolean) True during backend preview render.
 * $post_id (integer) The post ID the block is rendering content against.
 * $context (array) The context provided to the block by the post or its parent block.
 */

$context               = Timber::context();
$context['block']      = $block;
$context['is_preview'] = $is_preview;
$context['fields']     = get_fields() ?: [
  'FIELD_NAME' => 'Placeholder content'
];

Timber::render( 'accolade.twig', $context );




// NOTES: widths can really go from 6 to 22, but we show the user 1 to 17 and just add 5 in code