<?php
# Database Configuration
define( 'DB_NAME', 'database-name' );
define( 'DB_USER', 'mysql' );
define( 'DB_PASSWORD', 'mysql' );
define( 'DB_HOST', 'localhost' );
define( 'DB_HOST_SLAVE', 'localhost' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'abc_';

# Security Salts, Keys, Etc
define('AUTH_KEY',         '################################################################');
define('SECURE_AUTH_KEY',  '################################################################');
define('LOGGED_IN_KEY',    '################################################################');
define('NONCE_KEY',        '################################################################');
define('AUTH_SALT',        '################################################################');
define('SECURE_AUTH_SALT', '################################################################');
define('LOGGED_IN_SALT',   '################################################################');
define('NONCE_SALT',       '################################################################');



require_once __DIR__ . '/vendor/autoload.php';
use Roots\WPConfig\Config;

Config::define( 'WP_ENVIRONMENT_TYPE', 'local' );
Config::define( 'WP_HOME', 'https://local.domain.com' );
Config::define( 'WP_SITEURL', 'https://local.domain.com' );
Config::define( 'WP_DEBUG', true );
Config::define( 'WP_LOGIN_WALL', true );


// Keys
Config::define( 'ACF_PRO_KEY', '' );


// Other various settings
Config::define( 'AUTOMATIC_UPDATER_DISABLED', TRUE );
Config::define( 'ALLOW_MAIL_ON_LOCAL', FALSE );

Config::apply();



# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');
