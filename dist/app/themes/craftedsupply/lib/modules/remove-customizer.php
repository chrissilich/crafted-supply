<?php

namespace CraftedSupply\Modules\RemoveCustomizer;

/**
 * Remove Theme Customizer from WP
 */
class Remove_Customizer {

  /**
   * @var Remove_Customizer
   */
  private static $instance;


  /**
   * Main Instance
   *
   * Allows only one instance of Remove_Customizer in memory.
   *
   * @static
   * @staticvar array $instance
   * @uses Remove_Customizer::init() Setup hooks and filters
   * @return object $instance The one true Remove_Customizer
   * @see Remove_Customizer()
   */
  public static function instance() {
    if ( ! isset( self::$instance ) && ! self::$instance instanceof Remove_Customizer ) {

      // Start your engines!
      self::$instance = new Remove_Customizer;

      // Load the structures to trigger initially
      add_action( 'init', [ self::$instance, 'init' ], 10 ); // was priority 5
      add_action( 'admin_init', [ self::$instance, 'admin_init' ], 10 ); // was priority 5

    }
    return self::$instance;
  }

  /**
   * Run all plugin stuff on init.
   *
   * @return void
   */
  public function init(): void {

    // Remove customize capability
    add_filter( 'map_meta_cap', [ self::$instance, 'filter_to_remove_customize_capability' ], 10, 4 );
  }

  /**
   * Run all of our plugin stuff on admin init.
   *
   * @return void
   */
  public function admin_init(): void {

    // Drop some customizer actions
    remove_action( 'plugins_loaded', '_wp_customize_include', 10 );
    remove_action( 'admin_enqueue_scripts', '_wp_customize_loader_settings', 11 );

    // Manually override Customizer behaviors
    add_action( 'load-customize.php', [ self::$instance, 'override_load_customizer_action' ] );
  }

  /**
   * Remove customize capability
   *
   * This needs to be in public so the admin bar link for 'customize' is hidden.
   *
   * @param array $caps
   * @param string $cap
   * @param int $user_id
   * @param array $args
   * @return array
   */
  public function filter_to_remove_customize_capability( array $caps = [], string $cap = '', int $user_id = 0, array $args = [] ): array {
    if ( $cap == 'customize' ) {
      return ['nope'];
    }

    return $caps;
  }

  /**
   * Manually overriding specific Customizer behaviors
   *
   * @return void
   */
  public function override_load_customizer_action(): void {
    wp_die( __( 'The Customizer is currently disabled.', 'craftedsupply' ) );
  }
}

/**
* The main function. Use like a global variable, except no need to declare the global.
*
* @return object The one true Remove_Customizer Instance
*/
function Remove_Customizer() {
  return Remove_Customizer::instance();
}

// GO!
Remove_Customizer();
