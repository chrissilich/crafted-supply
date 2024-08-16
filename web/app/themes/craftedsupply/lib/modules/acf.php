<?php

namespace CraftedSupply\Modules\Acf;
use Roots\WPConfig\Config;


/**
 * Add Global Options Page
 */
function add_global_options_page() {
  acf_add_options_page( [
    'page_title' 	=> 'Global Options',
    'menu_title'	=> 'Global Options',
    'menu_slug' 	=> 'global-options',
    'capability'	=> 'editor'
  ]);
}
add_action( 'init', __NAMESPACE__ . '\\add_global_options_page' );


/**
 * Hide ACF field group menu item if not in development
 *
 * @return FALSE
 */
$environment_type = WP_ENVIRONMENT_TYPE;
if ( $environment_type !== 'local' && $environment_type !== 'development' ) {
  add_filter( 'acf/settings/show_admin', '__return_false' );
}


/**
 * Disable ACF layout field settings turning into tabs
 *
 * @return TRUE
 */
add_filter( 'acf/field_group/disable_field_settings_tabs', '__return_true' );


/**
 * Prevent new field groups from automatically adding a field
 *
 * @return FALSE
 */
add_filter( 'acf/field_group/auto_add_first_field', '__return_false' );


/**
 * Hide drafts from relationship and post object fields
 *
 * @param array $args
 * @param array $field
 * @param int|string $post_id
 * @return array
 */
function filter_drafts_from_reference_fields( array $args, array $field, int|string $post_id ): array {
  $args['post_status'] = ['publish'];
  return $args;
}
add_filter( 'acf/fields/relationship/query', __NAMESPACE__ . '\\filter_drafts_from_reference_fields', 10, 3 );
add_filter( 'acf/fields/post_object/query', __NAMESPACE__ . '\\filter_drafts_from_reference_fields', 10, 3 );


/**
 * Show image sizes in instructions for image crop fields
 *
 * @param array $field
 * @return array
 */
function filter_image_crop_fields( array $field ): array {
  if ( empty( $field['instructions'] ) && isset($field['aspect_ratio_width']) && isset($field['aspect_ratio_height']) ) {
    $field['instructions'] = 'Image Size: ' . $field['aspect_ratio_width'] . 'x' . $field['aspect_ratio_height'];
  }
  return $field;
}
add_filter( 'acf/load_field/type=image_aspect_ratio_crop', __NAMESPACE__ . '\\filter_image_crop_fields', 10, 3 );


/**
 * Prevent Email Subscribe except in Global Options field
 *
 * @param mixed $valid
 * @param mixed $value
 * @param array $field
 * @param string $input_name
 * @return mixed
 */
function validate_forms_field( mixed $valid, mixed $value, array $field, string $input_name ): mixed {

  // Bail early if value is already invalid.
  if ( $valid !== true || $field['key'] === 'field_60ae86f2cd98f' ) {
    return $valid;
  }

  $email_subscribe_form = get_field( 'email_subscribe', 'option' )['form'];

  if ( is_string( $value ) ) {
    $value = intval( $value );
    if ( $value === $email_subscribe_form ) {
      return __( 'Please select a form other than Email Subscribe', 'craftedsupply' );
    }
  }

  return $valid;
}
add_action( 'acf/validate_value/type=forms',  __NAMESPACE__ . '\\validate_forms_field', 10, 4 );


