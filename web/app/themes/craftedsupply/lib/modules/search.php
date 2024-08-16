<?php

namespace CraftedSupply\Modules\Search;


/**
* Redirects search results from /?s=query to /search/query/, converts %20 to +.
*
* @link http://txfx.net/wordpress-plugins/nice-search/
* @global \WP_Rewrite $wp_rewrite WordPress Rewrite Component.
* @return void
*/
function redirect(): void {
	global $wp_rewrite;

  // check URL begins with ?s=
  if ( isset( $_GET['s'] ) ) {
    	wp_safe_redirect( get_search_link() );
    	exit();
  }
}
add_action( 'template_redirect', __NAMESPACE__ . '\\redirect' );


/**
* Filter the WP SEO search URL.
*
* @param string $url Search URL passed by WPSEO.
* @return string
*/
function rewrite( string $url ): string {
	return str_replace( '/?s=', '/search/', $url );
}
add_filter( 'wpseo_json_ld_search_url', __NAMESPACE__ . '\\rewrite' );


/**
* Filter the search keywords
*
* @param string $keywords Search keywords
* @return string
*/
function filter_search_query( string $keywords ): string {
  return urldecode( $keywords );
}
add_filter( 'get_search_query', __NAMESPACE__ . '\\filter_search_query' );
add_filter( 'the_search_query', __NAMESPACE__ . '\\filter_search_query' );


/**
* Filter the search query vars
*
* @param object $query
* @return object
*/
function filter_search_query_vars( object $query ): object {
  if ( $query->is_search && ! is_admin() ) {
    $query->query_vars['s'] = urldecode( $query->query_vars['s'] );
  }
  return $query;
}
add_action( 'parse_query', __NAMESPACE__ . '\\filter_search_query_vars' );


/**
* Simple function to check if we're dealing with a search permalink.
*
* @param string $search_base Anything to be inserted before searches. Defaults to 'search/'.
* @return bool
*/
function is_search_permalink( string $search_base ): bool {
	return
    is_search() &&
    ! is_admin() &&
    strpos( filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL ), "/{$search_base}/" ) === FALSE &&
    strpos( filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL ), '&' ) === FALSE
  ;
}
