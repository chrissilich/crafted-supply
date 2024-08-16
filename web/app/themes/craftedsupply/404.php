<?php
/**
 * The template for displaying 404 pages (Not Found)
 */

$context               = Timber::context();
$error_404             = get_field( '404_page', 'option' );
$context['error_404']  = $error_404;
$context['hero_title'] = $error_404['title'] ?? __( 'Page not found' );

Timber::render( 'src/pages/404.twig', $context );
