<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */

$context   = Timber::context();
$templates = [ 'src/pages/archive.twig' ];


// Custom post type archive
if ( is_post_type_archive() ) {
  $post_type       = get_queried_object()->name;
  $archive         = $context['archive_pages'][ $post_type ];
  $post            = Timber::get_post( $archive['id'] );
  $context['post'] = $post;
  array_unshift( $templates, 'src/pages/archive-' . $post_type . '.twig' );

// taxonomy term archive
} else if ( is_tax() ) {
  
  // get queried object
  $term = get_queried_object();

  // get the taxonomy
  $taxonomy = get_taxonomy( $term->taxonomy );

  // get associated post type
  $post_type = $taxonomy->object_type[0];

  // get the archive page
  $archive = $context['archive_pages'][ $post_type ];

  // start building the URL
  $url = $archive['url'];
  
  $facet_id = str_replace('craftedsupply_','_',$term->taxonomy);

  // add query strings
  $url = add_query_arg( $facet_id, $term->slug, $url );
  
  
  // dump( $url );
  // die;
  wp_redirect( $url, 302 );
  exit;
} 


Timber::render( $templates, $context );
