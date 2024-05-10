<?php

namespace CraftedSupply\Modules\Media;

use CraftedSupply\Modules\Utils;


/*
 * Register custom image sizes
 *
 * TODO: Add your WYSIWYG image sizes here before uploading any images
 */
// add_image_size( 'full-width', 950 );
// add_image_size( 'left-right', 400 );
add_image_size( '300w', 300, 0, false );
add_image_size( '600w', 600, 0, false );
add_image_size( '900w', 900, 0, false );
add_image_size( '1200w', 1200, 0, false);
add_image_size( '1280w', 1280, 0, false);
add_image_size( '1440w', 1440, 0, false);
add_image_size( '1680w', 1680, 0, false);
add_image_size( '1920w', 1920, 0, false);

// output the images registered in wordpress
// add_action( 'after_setup_theme', function() {
//   global $_wp_additional_image_sizes;
//   echo '<pre>';
//   print_r($_wp_additional_image_sizes);
//   die;
// } );




/**
 * Cap uploads at 20 MB by default.
 *
 * We use the Fastly CDN on most our sites. Fastly cannot serve files larger than 20 MB unless segmented caching is
 * enabled for the file's URL.
 *
 * While we can enable serving WP uploads via segmented caching, this introduces possible problems CMS editors need to
 * be aware of. For instance, it becomes bad practice to replace an existing file (so that the same URL now serves a
 * different file). This can mess up users' downloads if that file is segmented and only some segments end up being
 * served from cache, since nothing done in the WordPress UI will invalidate Fastly's caching of uploaded files.
 *
 * (Further, segmented caching cannot be limited to only files that happen to be over the 20 MB threshold. It must be
 * based on the URL in some way (path and/or file extension typically). Therefore we stay under the threshold by
 * default, so that we do not have to enable segmented caching by default.
 *
 * Only raise/remove this limit if Fastly is not used, or if you have had discussions with the appropriate devops &
 * content teams to ensure that segmented caching is turned on and all site editors understand the consequences.
 *
 * @see https://docs.fastly.com/en/guides/segmented-caching
 *
 * @param int $size
 * @return int
 */
function upload_size_limit( int $size ): int {
  return min( $size, 20971520 );
}
add_filter( 'upload_size_limit', __NAMESPACE__ . '\\upload_size_limit', 500 );


/**
 * Set image quality
 *
 * @return int
 */
function set_quality(): int {
  return 70;
}
add_filter( 'jpeg_quality',  __NAMESPACE__ . '\\set_quality' );
add_filter( 'wp_editor_set_quality',  __NAMESPACE__ . '\\set_quality' );


/**
 * Limit the maximum width of uploaded images. This forces the user to reduce dimensions on their end if they want to
 * upload an image that is larger.
 *
 * This is done in addition to big_image_size_threshold() because some cropping code, such as in the ACF Image Aspect
 * Ratio Crop plugin, still uses the original uploaded image to generate derivative thumbnails. If the original image is
 * large, this can stress or even exceed server resources.
 *
 * @param array $file
 * @return array
 */
function reject_large_images( array $file ): array {
  // Max width in pixels.
  $max_width = 4112;

  if ( empty( $file['tmp_name'] ) ) {
    $file['error'] = 'No temporary file available!';
    return $file;
  }

  // $file['type'] is direct from the client's POST and unverified. Get WP's opinion on the actual file type.
  $wp_filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );
  if ( $wp_filetype['type'] && substr( $wp_filetype['type'], 0, 5 ) === 'image' && substr( $wp_filetype['type'], 0, 9 ) !== 'image/svg' ) {
    $info = wp_getimagesize( $file['tmp_name'] );

    if ( ! isset( $info[0] ) ) {
      $file['error'] = 'Could not read image dimensions!';
    } elseif ( $info[0] > $max_width ) {
      $file['error'] = "Uploaded images must be no more than {$max_width} pixels wide.";
    }
  }

  return $file;
}
add_filter( 'wp_handle_upload_prefilter', __NAMESPACE__ . '\\reject_large_images' );


/**
 * Filters the “BIG image” threshold value.
 *
 * This threshold value is used by WordPress to potentially alter media library images as soon as they are uploaded to
 * smaller, more manageable dimensions.
 *
 * Here we only threshold the width. This is to ensure we do not shrink portrait-style images down to where they are
 * unusable in wide locations.
 *
 * @param int|bool $threshold
 * @param array $imagesize
 * @return mixed
 */
function big_image_size_threshold( int|bool $threshold, array $imagesize ): mixed {
  $width_threshold = 2056;

  if ( !empty( $imagesize[0] ) && !empty( $imagesize[1] ) ) {
    if ( $imagesize[0] > $width_threshold ) {
      // The threshold will be applied to the larger side. So if the image is taller than it is wide, we must calculate
      // a dynamic threshold that will result in the width ending up at the target threshold width.
      if ( $imagesize[1] > $imagesize[0] ) {
        return (int) round( $imagesize[1] * $width_threshold / $imagesize[0] );
      }
      return $width_threshold;
    }
  }

  // If we're missing dimension data, err on the side of caution and don't auto-scale.
  return false;
}
add_filter( 'big_image_size_threshold', __NAMESPACE__ . '\\big_image_size_threshold', 999, 2 );


/**
 * Set image links to "None"
 *
 * @return void
 */
function default_attachment_display_settings(): void {
  update_option( 'image_default_link_type', 'none' );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\default_attachment_display_settings' );




/**
 * Remove srcset and stuff on images
 *
 * @return []
 */
add_filter( 'wp_calculate_image_srcset_meta', '__return_empty_array' );


/**
 * Remove default image sizes
 *
 * @param array $sizes
 * @param array $metadata
 * @return array
 */
function remove_default_image_sizes( array $sizes, array $metadata ): array {
  $disabled_sizes = [
    'thumbnail',
    'medium',
    'medium_large',
    'large',
    '1536x1536',
    '2048x2048'
  ];

  // unset disabled sizes
  foreach ( $disabled_sizes as $size ) {
    if ( ! isset( $sizes[ $size ] ) ) {
      continue;
    }
    unset( $sizes[ $size ] );
  }
  return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced',  __NAMESPACE__ . '\\remove_default_image_sizes', 10, 2 );

