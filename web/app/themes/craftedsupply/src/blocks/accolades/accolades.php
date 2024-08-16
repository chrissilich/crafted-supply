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


$allowed_blocks = [ 'craftedsupply/accolade' ];

$template = [
  ['craftedsupply/accolade'],
  ['craftedsupply/accolade'],
  ['craftedsupply/accolade'],
];

$context['innerblocks_allowed'] = esc_attr( wp_json_encode( $allowed_blocks ) );
$context['innerblocks_template'] = esc_attr( wp_json_encode( $template ) );

Timber::render( 'accolades.twig', $context );



// NOTES: widths can really go from 6 to 22, but we show the user 1 to 17 and just add 5 in code