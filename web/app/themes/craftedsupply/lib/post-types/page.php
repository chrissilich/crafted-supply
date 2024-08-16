<?php

namespace CraftedSupply\PostTypes\Pages;



/*
* Block template for Page post type 
*/
function block_template_for_page() {
	$post_type_object = get_post_type_object( 'page' );
	$post_type_object->template = [
		[ 'craftedsupply/hero-default', [
    	'lock' => [
				'remove'	=> true,
				'move'		=> true
      ]
    ] ],
		[ 'craftedsupply/wysiwyg']
  ];
}
// add_action( 'init', __NAMESPACE__ . '\\block_template_for_page' );



