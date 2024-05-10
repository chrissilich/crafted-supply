<?php

namespace CraftedSupply\Modules\Cleanup;



/**
 * Clean up wp_head()
 *
 * Remove unnecessary <link>'s
 * Remove inline CSS and JS from WP emoji support
 * Remove inline CSS used by Recent Comments widget
 * Remove inline CSS used by posts with galleries
 * Remove self-closing tag
 *
 * @return void
 */
function head_cleanup(): void {
  remove_action( 'wp_head', 'feed_links_extra', 3 );
  add_action( 'wp_head', 'ob_start', 1, 0 );
  add_action( 'wp_head', function () {
    $pattern = '/.*' . preg_quote( esc_url( get_feed_link( 'comments_' . get_default_feed() ) ), '/' ) . '.*[\r\n]+/';
    echo preg_replace( $pattern, '', ob_get_clean() );
  }, 3, 0 );
  remove_action( 'wp_head', 'rsd_link' );
  remove_action( 'wp_head', 'wlwmanifest_link' );
  remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
  remove_action( 'wp_head', 'wp_generator' );
  remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
  remove_action( 'wp_head', 'wp_oembed_add_host_js' );
  remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  add_filter( 'use_default_gallery_style', '__return_false' );
  add_filter( 'emoji_svg_url', '__return_false' );
  add_filter( 'show_recent_comments_widget_style', '__return_false' );
}
add_action( 'init', __NAMESPACE__ . '\\head_cleanup' );


/**
 * Remove the WordPress version from RSS feeds
 *
 * @return FALSE
 */
add_filter( 'the_generator', '__return_false' );

/**
 * Disable default dashboard widgets
 *
 * @return void
 */
function disable_default_dashboard_widgets(): void {
  remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );
  remove_meta_box( 'dashboard_quick_press', 'dashboard', 'core' );
  remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\disable_default_dashboard_widgets' );


/**
 * Remove things from the admin bar displayed on front-end for logged-in users
 *
 * @param WP_Admin_Bar $wp_admin_bar
 * @return void
 */
function admin_bar_edit(  \WP_Admin_Bar $wp_admin_bar ): void {
  $wp_admin_bar->remove_node( 'wp-logo' );
}
add_action( 'admin_bar_menu', __NAMESPACE__ . '\\admin_bar_edit', 999 );



/**
 * Clean up language_attributes() used in <html> tag
 *
 * Remove dir="ltr"
 *
 * @return string
 */
function language_attributes(): string {
  $attributes = [];

  if ( is_rtl() ) {
    $attributes[] = 'dir="rtl"';
  }

  $lang = get_bloginfo( 'language' );

  if ( $lang ) {
    $attributes[] = "lang=\"$lang\"";
  }

  $output = implode( ' ', $attributes );
  $output = apply_filters( 'craftedsupply/language_attributes', $output );

  return $output;
}
add_filter('language_attributes', __NAMESPACE__ . '\\language_attributes');


/**
 * Clean up output of stylesheet <link> tags
 *
 * @param string $input
 * @return string
 */
function clean_style_tag( string $input ): string {
  preg_match_all( "!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches );
  if ( empty( $matches[2] ) ) {
    return $input;
  }
  // Only display media if it is meaningful
  $media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
  return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
}
add_filter( 'style_loader_tag', __NAMESPACE__ . '\\clean_style_tag' );

