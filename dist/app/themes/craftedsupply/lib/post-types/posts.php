<?php

namespace CraftedSupply\PostTypes\Posts;

use CraftedSupply\Modules\Utils;


/**
 * Alter default Post object
 *
 * @return void
 */
function change_post_object(): void {
	global $wp_post_types;

	$post = &$wp_post_types['post'];
	
	// $labels                     = &$post->labels;
	// $labels->name               = 'Articles';
	// $labels->singular_name      = 'Article';
	// $labels->add_new            = 'Add Article';
	// $labels->add_new_item       = 'Add New Article';
	// $labels->edit_item          = 'Edit Article';
	// $labels->new_item           = 'Article';
	// $labels->view_item          = 'View Article';
	// $labels->search_items       = 'Search Articles';
	// $labels->not_found          = 'No Articles found';
	// $labels->not_found_in_trash = 'No Articles found in Trash';
	// $labels->all_items          = 'All Articles';
	// $labels->menu_name          = 'Articles';
	// $labels->name_admin_bar     = 'Article';

	$post->public 			 = TRUE;
	$post->has_archive = TRUE;

	// dump($post); die;
}
add_action( 'init', __NAMESPACE__ . '\\change_post_object' );


/**
 * Remove Posts from Nav Menus
 *
 * @param array $args
 * @param string $post_type
 * @return array
*/
function remove_posts_from_nav_menus( array $args, string $post_type ): array {
	if ( $post_type === 'post' ) {
		$args['show_in_nav_menus'] = FALSE;
	}
	return $args;
}
add_filter('register_post_type_args', __NAMESPACE__ . '\\remove_posts_from_nav_menus', 20, 2);



/*
* Block template for Post post type (article) 
*
* @return void
*/
function block_template_for_post() {
	$post_type_object = get_post_type_object( 'post' );
	$post_type_object->template = [
		[ 'craftedsupply/hero-article', [
			'lock' => [
				'remove'	=> true,
				'move'		=> true
			]
		] ],
		[ 'craftedsupply/wysiwyg']
	];
}
// add_action( 'init', __NAMESPACE__ . '\\block_template_for_post' );


