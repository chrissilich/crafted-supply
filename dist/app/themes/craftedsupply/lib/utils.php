<?php

namespace CraftedSupply\Modules\Utils;

use Timber;


/**
 * Get current active trail
 *
 * @param Timber\Post|null $post
 * @param Timber\MenuItem $item
 * @return bool
 */
function get_active_trail( Timber\Post|null $post, Timber\MenuItem $item ): bool {

  if ( ! isset( $post ) ) {
    return FALSE;
  }

  $active = FALSE;

  $archive_pages = Timber::context()['archive_pages'];

  $item_id = (int) $item->object_id;

  $parent_id = NULL;
  if ( $post->parent() ) {
    $ancestors = get_post_ancestors( $post->ID );
    $root      = count( $ancestors ) - 1;
    $parent_id = $ancestors[ $root ];
  }

  if ( $item->current_post_type_landing_page || $item->current_item_parent || $item_id === $parent_id ) {
    $active = TRUE;
  }

  foreach ( array_keys( $archive_pages ) as $archive_type ) {
    if (
      $post->post_type === $archive_type
      && ( $item_id === $archive_pages[ $archive_type ]['id'] || $item_id === $archive_pages[ $archive_type ]['ancestor_id'] )
    ) {
      $active = TRUE;
    }
  }

  return $active;
}


/**
 * Insert an item into an array at any position
 *
 * @param array      $array
 * @param int|string $position
 * @param mixed      $insert
 */
function array_insert( array &$array, int|string $position, mixed $insert ): void {
  if ( is_int( $position ) ) {
    array_splice( $array, $position, 0, $insert );
  } else {
    $pos   = array_search( $position, array_keys( $array ) );
    $array = array_merge(
      array_slice( $array, 0, $pos ),
      $insert,
      array_slice( $array, $pos )
    );
  }
}


/**
 * Hooks a single callback to multiple tags
 *
 * @param array $tags
 * @param string $function
 * @param int $priority
 * @param int $accepted_args
 * @return void
 */
function add_filters( array $tags, string $function, int $priority = 10, int $accepted_args = 1 ): void {
  foreach ( ( array ) $tags as $tag ) {
    add_filter( $tag, $function, $priority, $accepted_args );
  }
}


/**
 * Returns a string of the current user's role name or null if not logged in
 *
 * @return null|string
 */
function get_user_role(): null|string {
  if ( ! is_user_logged_in() ) {
    return null;
  }
  $user_role = array_shift( wp_get_current_user()->roles );
  return $user_role;
}


/**
 * Make a URL relative
 *
 * @param string $input
 * @return string
 */
function root_relative_url( string $input ): string {

  if ( is_feed() ) {
    return $input;
  }

  $url = parse_url( $input );
  if ( ! isset( $url['host'] ) || ! isset( $url['path'] ) ) {
    return $input;
  }
  $site_url = parse_url( network_home_url() );  // falls back to home_url

  if ( ! isset( $url['scheme'] ) ) {
    $url['scheme'] = $site_url['scheme'];
  }

  $hosts_match   = $site_url['host'] === $url['host'];
  $schemes_match = $site_url['scheme'] === $url['scheme'];
  $ports_exist   = isset( $site_url['port'] ) && isset( $url['port'] );
  $ports_match   = ( $ports_exist ) ? $site_url['port'] === $url['port'] : TRUE;

  if ( $hosts_match && $schemes_match && $ports_match ) {
    return wp_make_link_relative( $input );
  }
  return $input;
}


/**
 * Returns the primary term for the chosen taxonomy set by Yoast SEO
 * or the first term selected.
 *
 * @link https://www.tannerrecord.com/how-to-get-yoasts-primary-category/
 * @param string  $taxonomy The taxonomy to query. Defaults to category.
 * @param integer $post The post id.
 * @return array The term with keys of 'name', 'slug', and 'url'.
 */
function get_primary_taxonomy_term( string $taxonomy = 'category', int $post = 0 ): array {
  if ( ! $post ) {
    $post = get_the_ID();
  }

  $terms        = get_the_terms( $post, $taxonomy );
  $primary_term = [];


  if ( $terms && !is_wp_error( $terms )) {
    $term_display = '';
    $term_slug    = '';
    $term_link    = '';
    if ( class_exists( 'WPSEO_Primary_Term' ) ) {
      $wpseo_primary_term = new \WPSEO_Primary_Term( $taxonomy, $post );
      $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
      $term               = get_term( $wpseo_primary_term );
      if ( is_wp_error( $term ) ) {
        $term_display = $terms[0]->name;
        $term_slug    = $terms[0]->slug;
        $term_link    = get_term_link( $terms[0]->term_id );
      } else {
        $term_display = $term->name;
        $term_slug    = $term->slug;
        $term_link    = get_term_link( $term->term_id );
      }
    } else {
      $term_display = $terms[0]->name;
      $term_slug    = $terms[0]->slug;
      $term_link    = get_term_link( $terms[0]->term_id );
    }
    $primary_term['url']   = $term_link;
    $primary_term['slug']  = $term_slug;
    $primary_term['name'] = $term_display;
  }
  return $primary_term;
}


/**
 * Trims text to a certain number of characters.
 * This function can be useful for excerpt of the post
 * As opposed to wp_trim_words trims characters that makes text to
 * take the same amount of space in each post for example
 *
 * @param   string $text      Text to trim.
 * @param   int    $num_chars Number of characters. Default is 60.
 * @param   string $more      What to append if $text needs to be trimmed. Defaults to '&hellip;'.
 * @return  string trimmed text.
 */
function trim_characters( string $text, int $num_chars = 60, string $more = '&hellip;' ): string {
  $text = wp_strip_all_tags( $text );
  $text = mb_strimwidth( $text, 0, $num_chars, $more );
  return $text;
}


/**
 * Returns an array with post types as keys and corresponding page IDs as values
 *
 * @return array
 */
function get_archive_page_ids(): array {

  $page_ids = [];

  foreach ( get_post_types( [], 'objects' ) as $post_type ) {
    if ( ! $post_type->has_archive ) {
      continue;
    }

    if ( 'post' === $post_type->name ) {
      $page_id = get_option( 'page_for_posts' );
    } else {
      $page_id = get_option( "page_for_{$post_type->name}" );
    }

    if ( ! $page_id ) {
      continue;
    }

    $page_ids[ $post_type->name ] = $page_id;
  }

  return $page_ids;
}


/**
 * Returns an array with post types as keys and corresponding data as values
 *
 * @return array
 */
function get_archive_page_details(): array {

  $details = [];

  foreach ( get_post_types( [], 'objects' ) as $post_type ) {
    if ( ! $post_type->has_archive ) {
      continue;
    }

    if ( 'post' === $post_type->name ) {
      $page_id = get_option( 'page_for_posts' );
    } else {
      $page_id = get_option( "page_for_{$post_type->name}" );
    }

    if ( ! $page_id ) {
      continue;
    }

    $post = Timber::get_post( $page_id );

    if ( $post->post_parent ) {
      $ancestors = get_post_ancestors( $page_id );
      $root      = count( $ancestors ) - 1;
      $parent    = $ancestors[ $root ];
    } else {
      $parent = $page_id;
    }


    $details[ $post_type->name ] = [
      'id'          => (int) $page_id,
      'title'       => $post->title(),
      'url'         => root_relative_url( $post->link() ),
      'ancestor_id' => (int) $parent
    ];
  }

  return $details;
}


/**
 * Returns the archive page for the current post type, ignoring the loop
 *
 * @return Timber\Post|null
 */
function get_archive_page_ID() {
  //if on the blog page
	if ( is_home() && ! in_the_loop() ) {
    $ID = get_option('page_for_posts');
	} elseif ( is_post_type_archive()) {
		//reference a custom archive page based it's slug
		//eg. for a 'houses' custom post type, you would create a page called `houses` and store any archive front matter on this page
		$query = get_queried_object();
		$slug = $query->name;
    $page_ids = get_archive_page_ids();
    $ID = $page_ids[ $query->name ];
	} else {
		$ID = get_the_ID();
	}
	return $ID;
}



/**
 * Modify the admin URL based on the current admin URL plus whatever query vars are provided.
 *
 * @param array $query_vars
 * @return string
 */
function modify_admin_url($query_vars): string {
  // get GET vars from URL and merge them with the provided query vars
  $query_vars = array_merge($_GET, $query_vars);
  // remove any empty vars
  $query_vars = array_filter($query_vars);
  // build query string
  $query_string = http_build_query($query_vars);
  // strip query vars off current URL
  $admin_url = strtok($_SERVER['REQUEST_URI'], '?');
  // add query string to current admin URL
  $admin_url = $admin_url . '?' . $query_string;
  return $admin_url;
}



/*
* Convert youtube url if various formats to youtube iframe embed
*
* @param string $string
* @return string
*/
function convert_youtube_url_to_embed($string) {
  return preg_replace(
      "/[a-zA-Z\/\/:\.]*youtu(?:be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)(?:[&?\/]t=)?(\d*)(?:[a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
      "<iframe width=\"1000\" height=\"562\" src=\"https://www.youtube.com/embed/$1?start=$2\" allowfullscreen></iframe>",
      $string
  );
}



/**
 * Retrieves the singular name of a given post type.
 *
 * @param string $post_type The post type to retrieve the singular name for.
 * @return string The singular name of the post type.
 */
function get_post_type_singular_name($post_type) {
  $post_type_object = get_post_type_object($post_type);
  return $post_type_object->labels->singular_name;
}



/**
 * Retrieves the plural name of a given post type.
 *
 * @param string $post_type The post type to retrieve the plural name for.
 * @return string The plural name of the post type.
 */
function get_post_type_plural_name($post_type) {
  $post_type_object = get_post_type_object($post_type);
  return $post_type_object->labels->name;
}





/*
* Recreate Image Array that mimics the one ACF produces for the Image field, from just an attachment ID 
*
* @param Attachment ID
* @return array
*/
function image_array_from_id( $attachment_id ) {

  $url = wp_get_attachment_url($attachment_id);
  $meta = wp_get_attachment_metadata($attachment_id);
  $post = get_post($attachment_id);
  $alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true) ?: '';

  return [
    'url' => $url,
    'mime_type' => $post->post_mime_type,
    'width' => $meta['width'],
    'height' => $meta['height'],
    'original_image' => [
      'alt' => $alt
    ]
  ];
}