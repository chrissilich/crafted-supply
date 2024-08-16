<?php
use CraftedSupply\Modules\Utils;


/**
 * The Template for displaying all single posts
 */
$context         = Timber::context();
$post            = Timber::get_post();
$context['post'] = $post;



// if post type is product
if ( 'product' === $post->post_type ) {
  // wrap the word "and" in the post titlte with <em> tag
  $context['title'] = preg_replace( '/\b(and)\b/', '<em>$1</em>', $post->post_title );

// If this is an article or resource, look for related posts
} else if ($post->post_type == 'post' || $post->post_type == 'resource') {
  
  // $related_posts = Timber::get_posts( [
  //   'post_type'      => $post->post_type,
  //   'posts_per_page' => 3,
  //   'post__not_in'   => [ $post->ID ],
  //   // 'orderby'        => 'rand'
  // ] );

  // if ($related_posts->found_posts > 0) {
  //   $heading = 'Related '. Utils\get_post_type_plural_name($post->post_type);

  //   $context['related_posts'] = [
  //     'heading' => $heading,
  //     'posts' => $related_posts,
  //   ];
  // }
} else if ($post->post_type == 'gallery') {

  $context['all_galleries']  = Timber::get_posts( [
    'post_type'      => 'gallery',
    'posts_per_page' => -1,
  ] );

}
  

if ( post_password_required( $post->ID ) ) {
  Timber::render( 'page-password.twig', $context );
} else {
  Timber::render( [
    'src/pages/single-' . $post->post_type . '.twig',
    'src/pages/single.twig'
  ], $context );
}

