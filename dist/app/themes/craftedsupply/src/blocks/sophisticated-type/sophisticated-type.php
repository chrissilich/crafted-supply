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
  'layout' => 'crc',
  'line_1' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
  'line_2' => 'Donec velit urna, bibendum a pellentesque vel, bibendum sed purus.',
  'line_3' => 'Vivamus mi velit, dignissim et tincidunt ut, molestie nec mi.'
];

// find text wrapped in asterisks and add a span with a class around it
$context['fields']['line_1'] = preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', $context['fields']['line_1']);
$context['fields']['line_2'] = preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', $context['fields']['line_2']);
$context['fields']['line_3'] = preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', $context['fields']['line_3']);

Timber::render( 'sophisticated-type.twig', $context );