<?php

namespace CraftedSupply\Modules\JsToFooter;

/**
 * Moves all scripts to wp_footer action
 *
 * @return void
 */
function js_to_footer(): void {
  remove_action( 'wp_head', 'wp_print_scripts' );
  remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
  remove_action( 'wp_head', 'wp_enqueue_scripts', 1 );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\js_to_footer' );
