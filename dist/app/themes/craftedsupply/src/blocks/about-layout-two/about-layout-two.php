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
 
// Split the title at the <br />
$context['fields']['title'] = explode( '<br />', $context['fields']['title'] );
 
// Take the lines and wrap them in spans
$context['fields']['title'][0] = '<span>' . $context['fields']['title'][0] . '</span>';
$context['fields']['title'][1] = '<span>' . $context['fields']['title'][1] . '</span>';

// if the first string has an opening <em> but not a closing </em>,
if ( strpos( $context['fields']['title'][0], '<em>' ) !== false && strpos( $context['fields']['title'][0], '</em>' ) === false ) {
	// replace </span> with </em></span>
	$context['fields']['title'][0] = str_replace( '</span>', '</em></span>', $context['fields']['title'][0] );
	
	// and open the next string with a new <em>
	$context['fields']['title'][1] = str_replace( '<span>', '<span><em>', $context['fields']['title'][1] );
}

$context['fields']['title'] = implode( '<br />', $context['fields']['title'] );


// dump($context['fields']['title']); die;


Timber::render( 'about-layout-two.twig', $context );