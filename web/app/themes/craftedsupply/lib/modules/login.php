<?php

namespace CraftedSupply\Modules\Login;

/**
 * Add login wall if enabled on an environment
 *
 * @return void
 */
function login_wall(): void {
  if ( defined('WP_LOGIN_WALL') && WP_LOGIN_WALL && ! is_user_logged_in() ) {
    auth_redirect();
  }
}
add_action( 'template_redirect', __NAMESPACE__ . '\\login_wall' );

/**
 * Customize the login css
 *
 * @return void
 */
function customize_login(): void { ?>
  <style type="text/css">
    body.login div#login h1 a {
      pointer-events: none;
      position: relative;
      width: 140px;
      height: 140px;
      background-image: url('/app/themes/craftedsupply/src/admin/.svg');
      background-size: cover;
    }
  </style>
<?php }
add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\\customize_login' );
