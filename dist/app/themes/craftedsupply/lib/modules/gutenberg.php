<?php

namespace CraftedSupply\Modules\Gutenberg;
use CraftedSupply;

/*
 * Note:
 * 
 * Enabling/disabling of Rich Text Formats (bold, italic, etc) and
 * adding/removing of Block "Styles" (e.g. "Large", "Blue", etc)
 * from Gutnenberg blocks are handled in admin.js (because Gutenberg
 * is JS based, duh)
 * 
 * Adding and removing formatting options from the ACF WYSIWYG block
 * is still done in editor-customizations.php
 */




// Register blocks from src folder
$block_json_files = CraftedSupply::get_block_json_files( get_template_directory() . '/src/blocks' );
foreach ($block_json_files as $block_json_file) {
  register_block_type(rtrim($block_json_file, "block.json"));
}
// if you'd prefer to manually register each block, uncomment the following line and duplicate it for each one
// register_block_type( dirname( __FILE__ ) . '/src/blocks/example-block' );




/**
 * Function to make built in blocks disabled by default, and allowlist the ones we want.
 *
 * @param object $block_editor_context The block editor context.
 * @param object $editor_context The editor context.
 * @return array
 */
function allowlist_built_in_block_types( $block_editor_context, $editor_context ) {

  // Get current post type, id, slug, and template
  $post_type = $editor_context->post->post_type;
  $post_id = $editor_context->post->ID;
  $post_slug = $editor_context->post->post_name;
  $post_template = get_page_template_slug($post_id);
  
  // Figure out if the current page is the front or posts page
  $post_special = null;
  if ($post_id == get_option('page_on_front')) {
    $post_special = 'front';
  } else if ($post_id == get_option('page_for_posts')) {
    $post_special = 'posts';
  }

  // These will be allowed for all post types
  $enabled_blocks = [
    'core/paragraph',
    'core/heading',
    'core/list',
    'core/list-item',
    // 'core/image', // replaced with our own version that is simpler, and customized for our wysiwyg
    // 'core/quote',
    // 'core/buttons', // replaced with our own version that can do icons and stuff
    // 'core/button',
    'core/separator',
    // 'core/shortcode',
    // 'core/spacer',
    // 'core/pullquote',
    // 'gravityforms/form',
  ];

  // To enable core blocks for specific post types, use this logic:
  // if ($post_type === 'post') {
  //   array_push($enabled_blocks, 'core/paragraph');
  // }


  // Custom blocks are enabled for all post types by default
  // Unless they have an "allowed_for_post_types" property in their block.json
  $block_json_files = CraftedSupply::get_block_json_files( get_template_directory() . '/src/blocks' );
  foreach ($block_json_files as $block_json_file) {
    $block = json_decode(file_get_contents($block_json_file));

    $enabled = true;
    if (isset($block->craftedsupply->enabled)) {
      $enabled = $block->craftedsupply->enabled;
    }
    
    $allow = false;

    // If the block an "allowed" property, check if any of this post/page's properties match and allow the block
    if ($block->craftedsupply->allowed ?? false) {
      
      $allowed_post_types = $block->craftedsupply->allowed->post_type ?? [];
      $allowed_ids = $block->craftedsupply->allowed->id ?? [];
      $allowed_slugs = $block->craftedsupply->allowed->slug ?? [];
      $allowed_templates = $block->craftedsupply->allowed->template ?? [];
      $allowed_special = $block->craftedsupply->allowed->special ?? [];

      if (in_array($post_type, $allowed_post_types) || 
          in_array($post_id, $allowed_ids) || 
          in_array($post_slug, $allowed_slugs) || 
          in_array($post_template, $allowed_templates) || 
          in_array($post_special, $allowed_special)) {
        $allow = true;
      }

    // If the block doesn't have specific allow properties, allow it everywhere
    } else {
      $allow = true;
    }

    // If the block has a "disallowed" property, check if any of this post/page's properties match and disallow the block
    if ($block->craftedsupply->disallowed ?? false) {
      
      $disallowed_post_types = $block->craftedsupply->disallowed->post_type ?? [];
      $disallowed_ids = $block->craftedsupply->disallowed->id ?? [];
      $disallowed_slugs = $block->craftedsupply->disallowed->slug ?? [];
      $disallowed_templates = $block->craftedsupply->disallowed->template ?? [];
      $disallowed_special = $block->craftedsupply->disallowed->special ?? [];

      if (in_array($post_type, $disallowed_post_types) || 
          in_array($post_id, $disallowed_ids) || 
          in_array($post_slug, $disallowed_slugs) || 
          in_array($post_template, $disallowed_templates) || 
          in_array($post_special, $disallowed_special)) {
        $allow = false;
      }

    }

    // Based on the above logic, add the block to the enabled blocks list if it's allowed
    if ($allow && $enabled) {
      array_push($enabled_blocks, $block->name);
    }
  }
	return $enabled_blocks;
}
add_filter( 'allowed_block_types_all', __NAMESPACE__ . '\\allowlist_built_in_block_types', 10, 2 );




/*
* Disabled code editor and block locking for non-admins
*/
add_filter( 'block_editor_settings_all', 
	function( $settings, $context ) {
		if ( $context->post) {
			$settings['canLockBlocks'] = current_user_can( 'administrator' );
			$settings['codeEditingEnabled'] = current_user_can( 'administrator' );
		}
		return $settings;
	}, 10, 2
);


/* 
* Add custom block categories
*/
add_filter( 'block_categories_all' , function( $categories ) {
// Adding a new category.
  // $categories[] = array(
  //   'slug'  => 'heroes',
  //   'title' => 'Heroes'
  // );
  $categories[] = array(
    'slug'  => 'crafted-supply',
    'title' => 'Crafted Supply'
  );

  // find the design category and rename it Layout and move it to the top
  foreach ($categories as $key => $category) {
    if ($category['slug'] == 'design') {
      $categories[$key]['title'] = 'Layout';
      $categories = array_merge(array($categories[$key]), array_diff_key($categories, array($key => '')));
    }
  }


  return $categories;
});