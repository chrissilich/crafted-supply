<?php
/**
 * Configuration overrides for WP_ENVIRONMENT_TYPE === 'local'
 */

use Roots\WPConfig\Config;

Config::define( 'SAVEQUERIES', TRUE );
Config::define( 'WP_DEBUG', TRUE );
Config::define( 'WP_DEBUG_DISPLAY', TRUE );
Config::define( 'WP_DISABLE_FATAL_ERROR_HANDLER', TRUE );
Config::define( 'SCRIPT_DEBUG', TRUE );

ini_set( 'display_errors', 1 );

// Enable plugin and theme updates and installation from the admin
Config::define( 'DISALLOW_FILE_MODS', FALSE );
