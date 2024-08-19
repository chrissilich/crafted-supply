<?php

namespace CraftedSupply\Modules\UserRoles;
use CraftedSupply\Modules\Utils;

/**
 * Create client user role and rename Administrator
 *
 * @return void
 */
function client_user_role() {
  global $wp_roles;

  // Rename Administrator role
  $wp_roles->roles['administrator']['name'] = 'Crafted Supply Admin';
  $wp_roles->role_names['administrator'] = 'Crafted Supply Admin';

  // TODO: define user role below for clients

  // Create Client Admin role if not created
  if ( get_option( 'client_admin_role_created' ) === FALSE ) {
    add_role( 'client_admin', 'Crafted Supply Admin', [
  
      // core
      'read'                   => TRUE,
      'edit_theme_options'     => TRUE,
      'create_users'           => TRUE,
      'delete_users'           => TRUE,
      'edit_users'             => TRUE,
      'list_users'             => TRUE,
      'promote_users'          => TRUE,
      'remove_users'           => TRUE,
      'manage_categories'      => TRUE,
      'edit_others_posts'      => TRUE,
      'edit_pages'             => TRUE,
      'edit_others_pages'      => TRUE,
      'edit_published_pages'   => TRUE,
      'publish_pages'          => TRUE,
      'delete_pages'           => TRUE,
      'delete_others_pages'    => TRUE,
      'delete_published_pages' => TRUE,
      'delete_others_posts'    => TRUE,
      'delete_private_posts'   => TRUE,
      'edit_private_posts'     => TRUE,
      'read_private_posts'     => TRUE,
      'delete_private_pages'   => TRUE,
      'edit_private_pages'     => TRUE,
      'read_private_pages'     => TRUE,
      'edit_published_posts'   => TRUE,
      'upload_files'           => TRUE,
      'publish_posts'          => TRUE,
      'delete_published_posts' => TRUE,
      'edit_posts'             => TRUE,
      'delete_posts'           => TRUE,
      'unfiltered_html'        => TRUE,
  
      // gravity forms
      'gravityforms_view_entries'     => TRUE,
      'gravityforms_edit_entries'     => TRUE,
      'gravityforms_delete_entries'   => TRUE,
      'gravityforms_export_entries'   => TRUE,
      'gravityforms_view_entry_notes' => TRUE,
      'gravityforms_edit_entry_notes' => TRUE,
  
      // yoast
      'wpseo_manage_options'   => TRUE,
      'wpseo_manage_redirects' => TRUE,
    ] );
    update_option( 'client_admin_role_created', TRUE );
  }
}
add_action( 'admin_init', __NAMESPACE__ . '\\client_user_role' );


// TODO: remove this if not using the Redirection plugin
/**
 * Change user capability required for the redirection plugin
 *
 * @param	string $capability Default is 'manage_options'
 * @return string
 */
add_filter( 'redirection_role', function( string $capability ): string {
  return 'create_users';
} );


/**
 * Disable menu items for client
 *
 * @return void
 */
function disable_menus_for_client(): void {
  $role = Utils\get_user_role();
  if ( $role !== 'administrator' ) {

    // TODO: remove this if not using the Redirection plugin
    add_menu_page( 'Redirects', 'Redirects', 'create_users', 'tools.php?page=redirection.php', '', 'dashicons-migrate', 60 );

    remove_menu_page( 'edit.php?post_type=acf-field-group' );
    remove_menu_page( 'tools.php' );
    remove_menu_page( 'themes.php' );
    add_menu_page( 'Menus', 'Menus', 'create_users', 'nav-menus.php', '', 'dashicons-menu', 60 );
    remove_submenu_page( 'gf_edit_forms','gf_help' );
  }
}
add_action( 'admin_menu', __NAMESPACE__ . '\\disable_menus_for_client', 9999 );
