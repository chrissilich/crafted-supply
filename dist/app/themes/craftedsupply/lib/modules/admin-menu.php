<?php

namespace CraftedSupply\Modules\AdminMenuOrder;




// add_action( 'admin_init', __NAMESPACE__ . '\\alter_admin_menu', 5, 1 );
function alter_admin_menu() {
	global $menu;
	
	// Make some extra separators
	$menu["100.1"] = array( '', 'custom-separator-1', 'custom-separator-1', '', 'wp-menu-separator custom-separator' );
	$menu["100.2"] = array( '', 'custom-separator-2', 'custom-separator-2', '', 'wp-menu-separator custom-separator' );
	$menu["100.3"] = array( '', 'custom-separator-3', 'custom-separator-3', '', 'wp-menu-separator custom-separator' );

	$menu = array_values($menu);

	if ( current_user_can( 'administrator' ) ) {
		$custom_menu_order = array(
			'index.php', 								// Dashboard
			'separator1', 							// ---- Separator
			'global-options', 							// Global Options
			'edit.php?post_type=page', 					// Pages
			'edit.php', 								// Posts
			'edit.php?post_type=resource', 				// Resources
			'edit.php?post_type=region', 				// Regions
			'edit.php?post_type=person', 				// People
			'separator2', 							// ---- Separator
			'upload.php', 								// Media
			'filebird-settings', 						// FileBird
			'custom-separator-1', 					// ---- Separator
			'wpforms-overview', 							//  Forms
			'custom-separator-2', 					// ---- Separator
			'edit.php?post_type=acf-field-group', 		// ACF
			'wpseo_dashboard', 							// Yoast SEO
			'custom-separator-3', 					// ---- Separator
			'themes.php', 								// Appearance
			'users.php', 								// Users
			'plugins.php', 								// Plugins
			'tools.php',		 						// Tools
			'options-general.php', 						// Settings
			'separator-last', 						// ---- Separator
		);
	} else if (current_user_can('client_admin')) {
		$custom_menu_order = array(
			'index.php', 								// Dashboard
			'separator1', 							// ---- Separator
			'global-options', 							// Global Options
			'edit.php?post_type=page', 					// Pages
			'edit.php', 								// Posts
			'edit.php?post_type=resource', 				// Resources
			'edit.php?post_type=region', 				// Regions
			'edit.php?post_type=person', 				// People
			'separator2', 							// ---- Separator
			'admin.php?page=wpforms-overview', 			//  Forms
			'upload.php', 								// Media
			'wpseo_dashboard', 							// Yoast SEO
			'profile.php', 								// Users
			'separator3', 							// ---- Separator
			'separator-last', 						// ---- Separator
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
