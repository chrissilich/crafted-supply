<?php

namespace CraftedSupply\Modules\Timber;

use Kint\Twig\TwigExtension as BaseKintTwigExtension;
use Twig;
use Twig\Environment;
use Twig\TwigFunction;
use Timber;
use Timber\Site;
use CraftedSupply\Modules\Utils;


/**
 * Additional twig context to use globally
 *
 * @param array $context
 * @return array
 */
function add_to_context( array $context ): array {

  // General
  $context['WP_ENVIRONMENT_TYPE'] = WP_ENVIRONMENT_TYPE;
  $context['site']                = new Site;
  $context['site_url']            = get_site_url();
  $context['assets']              = Utils\root_relative_url( get_stylesheet_directory_uri() . '/assets' );

  // Home Page
  $context['is_home']             = is_front_page();

  // Posts Page
  $context['is_posts_page']       = is_home();

  // Archive Page Details
  $context['archive_pages']       = Utils\get_archive_page_details();

  // Add all menus to context with a registered location
  foreach ( array_keys( get_registered_nav_menus() ) as $location ) {
    // Bail out if menu has no location.
    if ( ! has_nav_menu( $location ) ) {
      continue;
    }
    $menu                 = Timber::get_menu( $location );
    $context[ $location ] = $menu;
  }

  // GOptions
  $options            = get_fields( 'options' );
  $context['options'] = $options;

  // Get recent news articles for generic/default News Promo component 
  $context['recent_articles'] = Timber::get_posts( [
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post__not_in'   => [ get_the_ID() ],
  ] );

  return $context;
}
add_filter( 'timber/context', __NAMESPACE__ . '\\add_to_context' );


/**
 * Add filters and extensions to Twig.
 */
function add_to_twig( Environment $twig ): Environment {

  $twig->addExtension( new \Twig\Extension\StringLoaderExtension() );

  // Get Image Array from Image ID
  $twig->addFunction( new Twig\TwigFunction( 'image_array_from_id', 'CraftedSupply\\Modules\\Utils\\image_array_from_id' ) );

  // Get Yoast Primary Term
  $twig->addFunction( new Twig\TwigFunction( 'get_primary_taxonomy_term', 'CraftedSupply\\Modules\\Utils\\get_primary_taxonomy_term' ) );

  // Add custom truncate filter
  $twig->addFilter( new Twig\TwigFilter( 'trim_characters', 'CraftedSupply\\Modules\\Utils\\trim_characters' ) );

    // Generate a random ID
  $twig->addFunction( new Twig\TwigFunction( 'generate_id', function() {
    return str_shuffle( uniqid() );
  } ) );

  // Get active trail for a menu item
  $twig->addFunction( new Twig\TwigFunction( 'get_active_trail', 'CraftedSupply\\Modules\\Utils\\get_active_trail' ) );

  return $twig;
}

add_filter( 'timber/twig', __NAMESPACE__ . '\\add_to_twig' );

/**
 * Add debug filters and extentions to Twig.
 */
function add_debug_tools_to_twig( Environment $twig ): Environment {
  if ( function_exists( 'kint_debug_ob' ) ) {
    // Add ddb debug function from kint-debugger plugin if it is enabled.
    $twig->addExtension( new KintTwigExtension() );
  } else {
    $twig->addExtension( new BaseKintTwigExtension() );
  }

  return $twig;
}
// Add Twig debug functions, if kint-php/kint-twig is installed.
if ( class_exists( BaseKintTwigExtension::class ) ) {
  add_filter( 'timber/twig', __NAMESPACE__ . '\\add_debug_tools_to_twig' );

  final class KintTwigExtension extends BaseKintTwigExtension {

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array {
      $functions = parent::getFunctions();

      $functions[] = new TwigFunction(
        'ddb',
        function ( Environment $env, array $context, array $args = [] ) {
          $dump = $this->dump( 'd', $env, $context, $args );
          // Let the kint debugger plugin take care of output buffering, as appropriate.
          return \kint_debug_ob( $dump );
        },
        [
          'is_safe' => [ 'html' ],
          'is_variadic' => true,
          'needs_context' => true,
          'needs_environment' => true,
        ]
      );

      return $functions;
    }

  }
}
