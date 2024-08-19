<?php

namespace CraftedSupply\Modules\AdminMenuOrder;




add_action( 'admin_init', __NAMESPACE__ . '\\alter_admin_menu', 5, 1 );
function alter_admin_menu() {
	global $menu;
	
	// Make some extra separators
	$menu["100.1"] = array( '', 'custom-separator-1', 'custom-separator-1', '', 'wp-menu-separator custom-separator' );
	$menu["100.2"] = array( '', 'custom-separator-2', 'custom-separator-2', '', 'wp-menu-separator custom-separator' );
	$menu["100.3"] = array( '', 'custom-separator-3', 'custom-separator-3', '', 'wp-menu-separator custom-separator' );

	// remove some menu items if you want
	remove_menu_page('index.php');

	$menu = array_values($menu);
	// dump($menu);die;
	// foreach ( $menu as $key => $menu_item ) {
	// 	echo $menu_item[2] . '<br>';
	// }
	// die;


	if ( current_user_can( 'administrator' ) ) {
		$custom_menu_order = array(
			'index.php', 								// Dashboard
			'site-settings', 							// Global Options
			'separator1', 							// ---- Separator
			'edit.php?post_type=page', 					// Pages
			'edit.php?post_type=gallery', 				// Regions
			'edit.php?post_type=product', 				// Products
			'edit.php?post_type=person', 				// People
			'separator2', 							// ---- Separator
			'upload.php', 								// Media
			'filebird-settings', 						// FileBird
			'custom-separator-1', 					// ---- Separator
			'wpforms-overview', 							//  Forms
			'edit.php?post_type=acf-field-group', 		// ACF
			'wpseo_dashboard', 							// Yoast SEO
			'custom-separator-2', 					// ---- Separator
			'themes.php', 								// Appearance
			'users.php', 								// Users
			'plugins.php', 								// Plugins
			'tools.php',		 						// Tools
			'options-general.php', 						// Settings
			'custom-separator-3', 					// ---- Separator
			'separator-last', 						// ---- Separator
		);
	} else if (current_user_can('client_admin')) {
		$custom_menu_order = array(
			'index.php', 								// Dashboard
			'site-settings', 							// Global Options
			'separator1', 							// ---- Separator
			'edit.php?post_type=page', 					// Pages
			'edit.php?post_type=gallery', 				// Regions
			'edit.php?post_type=product', 				// Products
			'edit.php?post_type=person', 				// People
			'separator2', 							// ---- Separator
			'upload.php', 								// Media
			'custom-separator-1', 					// ---- Separator
			'wpseo_dashboard', 							// Yoast SEO
			'profile.php', 								// Users
		);
	}

	
	if (isset($custom_menu_order)) {
		// Reorder the menu
		$ordered_menu = array();
		foreach ( $custom_menu_order as $menu_item ) {
			$found = array_search( $menu_item, array_column( $menu, 2 ) );
			if ($found !== false) {
				$ordered_menu[] = $menu[ $found ];
			}
		}

		// put the items back in that didn't get sorted
		foreach ( $menu as $menu_item ) {
			if ( ! in_array( $menu_item, $ordered_menu ) ) {
				$ordered_menu[] = $menu_item;
			}
		}

		$menu = $ordered_menu;
	}
}
