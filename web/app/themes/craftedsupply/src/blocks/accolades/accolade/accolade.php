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
  'background' => [
    "url" => "https://placehold.co/800x800/cccccc/aaaaaa/?text=Placeholder+Image",
    "alt" => "Placeholder Image",
  ],
  'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
  'name' => 'John Doe',
  'company' => 'Company Name',
  'layout' => 'left',
  'width' => 10,
  'position' => 4,
  'span' => false
];

Timber::render( 'accolade.twig', $context );
