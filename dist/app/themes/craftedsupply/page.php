<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 */
$context         = Timber::context();
$post            = Timber::get_post();
$context['post'] = $post;


if ( post_password_required( $post->ID ) ) {
  $context['password_form'] = get_the_password_form();
  Timber::render( 'src/pages/page-password.twig', $context );
} else {
  Timber::render( 'src/pages/page.twig', $context );
}
