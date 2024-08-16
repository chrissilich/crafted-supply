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

$template = [
  ['core/heading', [
    'level' => 2,
    'content' => 'Your content Here',
  ]],
  [ 'core/paragraph', [
    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in dui mauris. Vivamus hendrerit arcu sed erat molestie vehicula. ',
  ]],
];

$allowed_blocks = [
  'core/paragraph',
  'core/heading',
  'core/list',
  'core/list-item',
  'core/image',
  'core/quote',
  'core/separator',
  'craftedsupply/buttons',
  'craftedsupply/form',
];

$context['fields'] = get_fields();
$context['block'] = $block;
$context['is_preview'] = $is_preview;
$context['innerblocks_template'] = esc_attr( wp_json_encode( $template ) );
$context['allowed_blocks'] = esc_attr( wp_json_encode( $allowed_blocks ) );

Timber::render( 'wysiwyg.twig', $context );
