<?php

/**
 * Theme initialization
 */
class CraftedSupply {

  /**
   * Includes Timber and custom PHP files.
   *
   * @return void
   */
  public function __construct() {

    // Add util functions
    include_once( dirname(__FILE__) . '/lib/utils.php' );

    // Initialize Timber.
    Timber\Timber::init();

    // Sets the directories (inside your theme) to find .twig files
    Timber::$dirname = ['src'];

    // By default, Timber does NOT autoescape values. This enables it.
    Timber::$autoescape = FALSE;

    // Register custom post types
    $post_type_includes = $this->get_php_files( dirname( __FILE__ ) . '/lib/post-types' );
    foreach ( $post_type_includes as $post_type_include ) {
      require_once $post_type_include;
    }

    // // Register custom taxonomies
    $taxonomy_includes = $this->get_php_files( dirname( __FILE__ ) . '/lib/taxonomies' );
    foreach ( $taxonomy_includes as $taxonomy_include ) {
      require_once $taxonomy_include;
    }
    
    
    // add_action( 'after_theme_setup', [ $this, 'theme_setup' ], 10, 0 );
    $this->theme_setup(); // not sure why this needs to be earlier in the traditional WP setup...
	}

  /** Custom theme setup
   *
   * @return void
  */
  public function theme_setup(): void {
    // Add module includes
    $module_includes = $this->get_php_files(dirname(__FILE__) . '/lib/modules');
    foreach ( $module_includes as $module_include ) {
      require_once $module_include;
    }


    // Add component includes
    $component_includes = $this->get_php_files( dirname( __FILE__ ) . '/src/components' );
    foreach ( $component_includes as $component_include ) {
      require_once $component_include;
    }
  }

  /**
   * Recursively get all PHP files in a directory
   *
   * @param string $path
   *
   * @return \RecursiveIteratorIterator
   */
  public function get_php_files( string $path ): \RecursiveIteratorIterator {
    $php_files = new \RecursiveCallbackFilterIterator(
      new \RecursiveDirectoryIterator( $path ),
      function ( $current, $key, $iterator ) {
        // Allow recursion
        if ( $iterator->hasChildren() ) {
          return TRUE;
        }

        // Check for PHP file
        if ( $current->isFile() && $current->getExtension() === 'php' ) {
          return TRUE;
        }

        return FALSE;
      }
    );

    return new \RecursiveIteratorIterator( $php_files );
  }


  /**
   * Recursively get all block.json files lying in a directory or anywhere under its subdirectory
   * tree
   *
   * @param string $path
   *
   * @return \RecursiveIteratorIterator
   */
  public static function get_block_json_files( string $path ): \RecursiveIteratorIterator {
    $block_json_files = new \RecursiveCallbackFilterIterator(
      new \RecursiveDirectoryIterator( $path ),
      function ( $current, $key, $iterator ) {
        // Allow recursion
        if ( $iterator->hasChildren() ) {
          return TRUE;
        }

        // Check for block.json file
        if ( $current->isFile() && $current->getFilename() === 'block.json' ) {
          return TRUE;
        }

        return FALSE;
      }
    );

    return new \RecursiveIteratorIterator( $block_json_files );
  }
}



new CraftedSupply();
