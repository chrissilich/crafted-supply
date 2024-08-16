<?php

namespace CraftedSupply\PostTypes\Gallery;

$singular = "gallery"; // also used as post type key
$plural = "galleries";
$singular_upper = ucwords($singular);
$plural_upper = ucwords($plural);

add_action( 'init', function () use ($singular, $plural, $singular_upper, $plural_upper) {
	register_post_type( $singular, array(
		'labels' => array(
			'name' => $plural_upper,
			'singular_name' => $singular_upper,
			'menu_name' => $plural_upper,
			'all_items' => 'All Locations',
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
		'menu_icon' => 'dashicons-admin-site-alt',
		'supports' => array(
			0 => 'title',
			1 => 'editor',
			2 => 'page-attributes',
		),
		'delete_with_user' => false,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite'               => array(
			'slug'                  => $singular,
			'with_front'            => false,
			'pages'                 => false,
			'feeds'                 => true,
		),
		'capability_type'       => 'page',
	) );

} );



/*
* Block template for Gallery post type 
*
* @return void
*/
add_action( 'init', function () use ($singular) {
	$post_type_object = get_post_type_object( $singular );
	$post_type_object->template = [
		[ 'craftedsupply/gallery-setup', [
			'lock' => [
				'remove'	=> true,
				'move'		=> true
			]
		] ]
	];
} );

