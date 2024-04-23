<?php

namespace CraftedSupply\Modules\ThemeAssets;

/**
 * Theme Assets
 *
 * @return void
 */
function theme_assets(): void {

  /** De-register default jQuery and include from our repo */
  wp_deregister_script( 'jquery' );
  wp_enqueue_script( 'jquery', get_template_directory_uri() . '/assets/js/jquery.min.js', [], '3.7.1', FALSE );

  /** Add custom JS */
  wp_enqueue_script( 'theme', get_template_directory_uri() . '/assets/js/bundle.js', ['jquery'], filemtime( get_template_directory() . '/assets/js/bundle.js' ), FALSE );

  /** Remove core WP CSS */
  wp_dequeue_style( 'wp-block-library' );
  wp_dequeue_style( 'core-block-supports' );
  wp_dequeue_style( 'classic-theme-styles' );

  /** Add custom CSS */
  wp_enqueue_style( 'theme', get_template_directory_uri() . '/assets/css/style.css', [], filemtime( get_template_directory() . '/assets/css/style.css' ), FALSE );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\theme_assets' );


/**
 * Add Admin CSS and JS
 *
 * @return void
 */
function admin_theme_scripts(): void {
  $font_css_url = "https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap";

  wp_enqueue_style( 'craftedsupply-admin-fonts', $font_css_url, [], 1, FALSE );
  wp_enqueue_style( 'craftedsupply-admin-style', get_template_directory_uri() . '/assets/admin/admin.css', [], filemtime( get_template_directory() . '/assets/admin/admin.css' ), FALSE );
  wp_enqueue_script( 'craftedsupply-admin-js', get_template_directory_uri() . '/assets/admin/admin.js', [ 'jquery' ], filemtime(get_template_directory() . '/assets/admin/admin.js' ), FALSE);
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_theme_scripts' );

