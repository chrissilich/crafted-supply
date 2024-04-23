<?php
/**
 * The template for displaying Author Archive pages
 */

global $wp_query;

$context = Timber::context();

if ( isset( $wp_query->query_vars['author'] ) ) {
  $author            = Timber::get_user( $wp_query->query_vars['author'] );
  $context['author'] = $author;
  $context['title']  = 'Author Archives: ' . $author->name();
}

Timber::render( [
  'src/pages/author.twig',
  'src/pages/archive.twig'
], $context );
