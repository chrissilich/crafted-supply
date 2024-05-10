<?php

namespace CraftedSupply\Modules\BodyClasses;


/**
 * Add post type to body class
 */
function custom_body_classes( $classes ) {
	global $post;
	$returnString = false;

	
	// if $classes is a string, convert it to an array
	if ( is_string( $classes ) ) {
		$returnString = true;
		$classes = explode( ' ', $classes );
	}

	// add the current wp-evnironment
	if (null !== WP_ENVIRONMENT_TYPE) {
		$classes[] = 'wp-env-' . WP_ENVIRONMENT_TYPE;
	}

	$post_type = get_post_type();
	if ( is_singular() ) {
		$classes[] = 'post-type-' . $post_type;
	} elseif ( is_archive() ) {
		$classes[] = 'archive-type-' . $post_type;
	} elseif ( is_admin() && $post ) {
		$classes[] = 'post-type-' . $post_type;
	}
	
	if ( $post ) {
		$classes[] = 'post-id-' . $post->ID;
		$classes[] = 'post-slug-' . $post->post_name;
		if (get_page_template_slug( $post->ID )) {
			$classes[] = 'post-template-' . get_page_template_slug( $post->ID );
		}
	}

	if ($returnString) {
		return implode( ' ', $classes );
	}

	return $classes;
}
add_filter( 'body_class', __NAMESPACE__ . '\\custom_body_classes' );
add_filter( 'admin_body_class', __NAMESPACE__ . '\\custom_body_classes' );


