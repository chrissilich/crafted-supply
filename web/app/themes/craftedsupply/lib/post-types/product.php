<?php

namespace CraftedSupply\PostTypes\Product;

$singular = "product"; // also used as post type key
$plural = "products";
$singular_upper = ucwords($singular);
$plural_upper = ucwords($plural);

add_action( 'init', function () use ($singular, $plural, $singular_upper, $plural_upper) {
	register_post_type( $singular, array(
		'labels' => array(
			'name' => $plural_upper,
			'singular_name' => $singular_upper,
			'menu_name' => $plural_upper,
			'all_items' => 'All ' . $plural_upper,
			'edit_item' => 'Edit '.$singular_upper,
			'view_item' => 'View '.$singular_upper,
			'view_items' => 'View '.$plural_upper,
			'add_new_item' => 'Add New '.$singular_upper,
			'new_item' => 'New '.$singular_upper,
			'parent_item_colon' => 'Parent '.$singular_upper.':',
			'search_items' => 'Search '.$plural_upper,
			'not_found' => 'No '.$plural.' found',
			'not_found_in_trash' => 'No '.$plural.' found in Trash',
			'archives' => $singular_upper.' Archives',
			'attributes' => $singular_upper.' Attributes',
			'insert_into_item' => 'Insert into '.$singular,
			'uploaded_to_this_item' => 'Uploaded to this '.$singular,
			'filter_items_list' => 'Filter '.$plural.' list',
			'filter_by_date' => 'Filter '.$plural.' by date',
			'items_list_navigation' => $plural_upper.' list navigation',
			'items_list' => $plural_upper.' list',
			'item_published' => $singular_upper.' published.',
			'item_published_privately' => $singular_upper.' published privately.',
			'item_reverted_to_draft' => $singular_upper.' reverted to draft.',
			'item_scheduled' => $singular_upper.' scheduled.',
			'item_updated' => $singular_upper.' updated.',
			'item_link' => $singular_upper.' Link',
			'item_link_description' => 'A link to a '.$singular.'.',
		),
		'public' => true,
		'hierarchical' => true,
		'show_in_rest' => true,
		'menu_position' => 4.2,
		'menu_icon' => 'dashicons-cart',
		'supports' => array(
			0 => 'title',
			1 => 'editor',
			2 => 'page-attributes',
		),
		'delete_with_user' => false,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite' => array(
			'with_front' => true,
			'slug' => $plural
		),
		'has_archive' => false,
		'capability_type' => 'page',
	) );

} );



/*
* Block template for Product post type 
*
* @return void
*/
add_action( 'init', function () use ($singular) {
	$post_type_object = get_post_type_object( $singular );
	$post_type_object->template = [
		[ 'craftedsupply/product-intro', [
			'lock' => [
				'remove'	=> true,
				'move'		=> true
			]
		] ],
		[ 'craftedsupply/product-fifty-fifty'],
		[ 'craftedsupply/product-fifty-fifty'],
		[ 'craftedsupply/product-fifty-fifty'],
		[ 'craftedsupply/wysiwyg'],
	];
} );

