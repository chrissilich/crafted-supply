<?php

namespace CraftedSupply\Modules\ThemeSupports;

/**
 * Let WordPress manage the document title.
 * By adding theme support, we declare that this theme does not use a
 * hard-coded <title> tag in the document head, and expect WordPress to
 * provide it for us.
 */
add_theme_support( 'title-tag' );


/**
 * Switch default core markup for search form, comment form, and comments
 * to output valid HTML5.
 */
add_theme_support(
  'html5', [
    'gallery',
    'caption',
    'search-form'
  ]
);


/**
 * Enable CSS to be included in the Gutenberg editor
 */
add_theme_support( 'editor-styles' );
function relocate_editor_style( ): void {
  add_editor_style( '/assets/css/style.css' );
}
add_action( 'admin_init', __NAMESPACE__ . '\\relocate_editor_style' );


/**
 * Add filemtime as query arg to bust cache in admin
 *
 * @param string $stylesheets
 * @return string
 */
function fresh_editor_style( string $stylesheets ): string {
  global $editor_styles;
  $mce_css_list = explode( ',', $stylesheets );

  if ( ! empty( $editor_styles ) ) {
    foreach ( $editor_styles as $filename ) {
      foreach ( $mce_css_list as $key => $fileurl ) {
        if ( strstr( $fileurl, '/' . $filename ) ) {
          $filetime = filemtime( get_stylesheet_directory() . '/' . $filename );
          $mce_css_list[ $key ] = add_query_arg( 'ver', $filetime, $fileurl );
        }
      }
    }
  }

  return implode( ',', $mce_css_list );
}
add_filter( 'mce_css', __NAMESPACE__ . '\\fresh_editor_style' );


/**
 * Allow SVG Upload
 *
 * @param array $data
 * @param string $file
 * @param string $filename
 * @param mixed $mimes
 * @return array
 */
add_filter( 'wp_check_filetype_and_ext', function( array $data, string $file, string $filename, mixed $mimes = null ): array {

  $filetype = wp_check_filetype( $filename, $mimes );

  return [
    'ext'             => $filetype['ext'],
    'type'            => $filetype['type'],
    'proper_filename' => $data['proper_filename']
  ];

}, 10, 4 );


/**
 * Add SVG to mime types for upload
 *
 * @param array $mimes
 * @return array
 */
function add_svg_to_mime_types( array $mimes ): array {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', __NAMESPACE__ . '\\add_svg_to_mime_types' );


/**
 * Fix SVG thumbnail display in media library
 *
 * @return void
 */
function fix_svg_thumb_display(): void {
  echo '<style> .media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail { width: 100% !important; height: auto !important; } </style>';
}
add_action( 'admin_head', __NAMESPACE__ . '\\fix_svg_thumb_display' );
