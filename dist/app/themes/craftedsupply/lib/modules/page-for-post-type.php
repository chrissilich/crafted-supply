<?php

namespace CraftedSupply\Modules\PageForPostType;

use CraftedSupply\Modules\Utils;

class Page_For_Post_Type {

  const FASTLY_ARCHIVE_TYPE_KEY_PREFIX = 'cpt-';

  protected array $original_slugs = [];

  /**
   * Initializes props.
   *
   * @param	void
   * @return void
   */
  public function __construct() {

    // add settings
    add_action( 'admin_init', [ $this, 'add_settings' ] );

    // update post type objects
    add_action( 'registered_post_type', [ $this, 'update_post_type' ], 11, 2 );

    // menu classes
    add_filter( 'wp_nav_menu_objects', [ $this, 'filter_wp_nav_menu_objects' ], 1, 2 );

    // yoast titles
    add_filter( 'wpseo_title', [ $this, 'filter_wpseo_title' ], 10, 1 );

    // yoast breadcrumbs single links
    add_filter( 'wpseo_breadcrumb_single_link', [ $this, 'filter_wpseo_breadcrumb_single_link' ], 10, 2 );

    // yoast breadcrumbs links
    add_filter( 'wpseo_breadcrumb_links', [ $this, 'filter_wpseo_breadcrumb_links' ], 10, 1 );

    // "All POSTTYPE" view
    add_filter( 'display_post_states', [ $this, 'add_admin_column_label' ], 100, 2 );

    // Outputs a notice when editing a post-type landing page (internal use only).
    add_action( 'edit_form_after_title', [ $this, 'add_edit_message' ], 100, 1 );

    // post status changes / deletion
    add_action( 'transition_post_status', [ $this, 'action_transition_post_status' ], 10, 3 );
    add_action( 'deleted_post', [ $this, 'action_deleted_post' ], 10 );

    // Fastly integration.
    add_filter( 'purgely_pre_send_keys', [ $this, 'purgely_pre_send_keys' ] );
    add_filter( 'purgely_related_keys', [ $this, 'purgely_related_keys' ], 10, 2 );

  }


  /**
   * Add Settings Fields.
   *
   * @param	void
   * @return void
   */
  public function add_settings(): void {

    $cpts = get_post_types( [
      'public'      => TRUE,
      'has_archive' => TRUE,
    ], 'objects' );

    if ( count( $cpts ) ) {
      add_settings_section( 'page_for_post_type', 'Post Type Landing Pages', '__return_false', 'reading' );

      foreach ( $cpts as $cpt ) {
        $id    = "page_for_{$cpt->name}";
        $value = get_option( $id );

        // flush rewrite rules when the option is changed
        register_setting( 'reading', $id, [ $this, 'validate_field' ] );

        add_settings_field( $id, $cpt->labels->name, [ $this, 'create_field' ], 'reading', 'page_for_post_type', [
          'name'      => $id,
          'post_type' => $cpt,
          'value'     => $value
        ] );
      }
    }
  }


  /**
   * Create field.
   *
   * @param	array $args Field arguments.
   * @return void
   */
  public function create_field( array $args ): void {

    $value = intval( $args['value'] );
    $default = $args['post_type']->name;

    if ( isset( $this->original_slugs[ $args['post_type']->name ] ) ) {
      $default = $this->original_slugs[ $args['post_type']->name ];
    }

    wp_dropdown_pages( [
      'name'             => esc_attr( $args['name'] ),
      'id'               => esc_attr( $args['name'] . '_dropdown' ),
      'selected'         => $value,
      'show_option_none' => sprintf( 'Default (/%s/)', $default ),
    ] );
  }


  /**
   * Add an indicator to show if a page is set as a post type archive.
   *
   * @param array $post_states An array of post states to display after the post title.
   * @param WP_Post $post The current post object.
   * @return array
   */
  public function add_admin_column_label( array $post_states, \WP_Post $post ): array {

    if ( ! isset( $post ) ) {
      return $post_states;
    }

    $post_type = $post->post_type;
    $cpts      = get_post_types( [ 'public' => TRUE, 'has_archive' => TRUE ], 'objects' );

    if ( 'page' === $post_type ) {
      if ( in_array( $post->ID, $this->get_page_ids() ) ) {
        $cpt = array_search( $post->ID, $this->get_page_ids() );
        $post_states["page_for_{$post_type}"] = sprintf( esc_html( '%1$s Landing Page' ), $cpts[ $cpt ]->labels->name );
      }
    }

    return $post_states;
  }


  /**
   * Outputs a notice when editing a post-type landing page (internal use only).
   *
   * @param	\WP_Post $post
   * @return void
   */
  public function add_edit_message( \WP_Post $post ): void {
    $post_type = $post->post_type;
    $cpts      = get_post_types( [ 'public' => TRUE, 'has_archive' => TRUE ], 'objects' );

    if ( 'page' === $post_type && in_array( $post->ID, $this->get_page_ids() ) ) {
      $cpt = array_search( $post->ID, $this->get_page_ids() );
        printf(
          '<div class="notice notice-warning inline"><p>You are currently editing the %s landing page.</p></div>',
          $cpts[ $cpt ]->labels->name
        );
    }
  }


  /**
   * Flush rewrites and checks if the ID has been used already on this save
   *
   * @param $new_value
   * @return int
   */
  public function validate_field( $new_value ): int {
    flush_rewrite_rules();
    return intval( $new_value );
  }


  /**
   * Delete the setting for the corresponding post type if the page status
   * is transitioned to anything other than published
   *
   * @param string $new_status
   * @param string $old_status
   * @param WP_Post $post
   * @return void
   */
  public function action_transition_post_status( string $new_status, string $old_status, \WP_Post $post ): void {

    if ( 'publish' !== $new_status ) {
      $post_type = array_search( $post->ID, $this->get_page_ids() );
      if ( $post_type ) {
        delete_option( "page_for_{$post_type}" );
        flush_rewrite_rules();
      }
    }
  }


  /**
   * Delete relevant option if a page for the archive is deleted
   *
   * @param int $post_id
   * @return void
   */
  public function action_deleted_post( int $post_id ): void {
    $post_type = array_search( $post_id, $this->get_page_ids() );
    if ( $post_type ) {
      delete_option( "page_for_{$post_type}" );
      flush_rewrite_rules();
    }
  }


  /**
   * Modifies the post type object to update the permastructure based
   * on the page chosen
   *
   * @param string $post_type
   * @param object $args
   * @return void
   */
  public function update_post_type( string $post_type, object $args ): void {
    global $wp_post_types, $wp_rewrite;

    $post_type_page = get_option( "page_for_{$post_type}" );

    if ( ! $post_type_page ) {
      return;
    }

    // make sure we don't create rules for an unpublished page preview URL
    if ( 'publish' !== get_post_status( $post_type_page ) ) {
      return;
    }

    // get the old slug
    $args->rewrite = (array) $args->rewrite;
    $old_slug      = isset( $args->rewrite['slug'] ) ? $args->rewrite['slug'] : $post_type;

    // store this for our options page
    $this->original_slugs[ $post_type ] = $old_slug;

    // get page slug
    $slug = get_permalink( $post_type_page );
    $slug = str_replace( home_url(), '', $slug );
    $slug = trim( $slug, '/' );

    $args->rewrite     = wp_parse_args( [ 'slug' => $slug ], $args->rewrite );
    $args->has_archive = $slug;

    // rebuild rewrite rules
    if ( is_admin() || '' !== get_option( 'permalink_structure' ) ) {

      if ( $args->has_archive && $args->rewrite['slug'] ) {
        $archive_slug = $args->has_archive === TRUE ? $args->rewrite['slug'] : $args->has_archive;

        if ( isset($args->rewrite['with_front']) && $args->rewrite['with_front'] ) {
          $archive_slug = substr( $wp_rewrite->front, 1 ) . $archive_slug;
        } else {
          $archive_slug = $wp_rewrite->root . $archive_slug;
        }

        add_rewrite_rule( "{$archive_slug}/?$", "index.php?post_type=$post_type", 'top' );
        if ( isset($args->rewrite['feeds']) && $args->rewrite['feeds'] && $wp_rewrite->feeds ) {
          $feeds = '(' . trim( implode( '|', $wp_rewrite->feeds ) ) . ')';
          add_rewrite_rule( "{$archive_slug}/feed/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
          add_rewrite_rule( "{$archive_slug}/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
        }
        if ( isset($args->rewrite['pages']) && $args->rewrite['pages'] ) {
          add_rewrite_rule( "{$archive_slug}/{$wp_rewrite->pagination_base}/([0-9]{1,})/?$", "index.php?post_type=$post_type" . '&paged=$matches[1]', 'top' );
        }
      }

      $permastruct_args         = $args->rewrite;
      if (isset($permastruct_args['feeds'])) {
        $permastruct_args['feed'] = $permastruct_args['feeds'];
      }

      // support plugins that enable 'permastruct' option
      if ( isset( $args->rewrite['permastruct'] ) ) {
        $permastruct = str_replace( $old_slug, $slug, $args->rewrite['permastruct'] );
      } else {
        $permastruct = "{$args->rewrite['slug']}/%$post_type%";
      }

      add_permastruct( $post_type, $permastruct, $permastruct_args );
    }

    // update the global
    $wp_post_types[ $post_type ] = $args;
  }


  /**
   * Make sure menu items for our pages get the correct classes assigned
   *
   * @param array $sorted_items
   * @param object $args
   * @return array
   */
  public function filter_wp_nav_menu_objects( array $sorted_items, object $args ): array {
    global $wp_query;

    $queried_object = get_queried_object();

    if ( ! $queried_object ) {
      return $sorted_items;
    }

    $object_post_type = FALSE;

    if ( is_singular() ) {
      $object_post_type = $queried_object->post_type;
    }

    if ( is_post_type_archive() ) {
      $object_post_type = $queried_object->name;
    }

    if ( is_archive() && is_string( $wp_query->get( 'post_type' ) ) ) {
      $query_post_type  = $wp_query->get( 'post_type' );
      $object_post_type = $query_post_type ?: 'post';
    }

    if ( ! $object_post_type ) {
      return $sorted_items;
    }

    // get page ID array
    $page_ids = $this->get_page_ids();

    if ( ! isset( $page_ids[ $object_post_type ] ) ) {
      return $sorted_items;
    }

    foreach ( $sorted_items as &$item ) {
      if ( $item->type === 'post_type' && $item->object === 'page' && intval( $item->object_id ) === intval( $page_ids[ $object_post_type ] ) ) {
        if ( is_singular( $object_post_type ) ) {
          $item->classes[]                      = 'current-post-type-landing-page';
          $item->current_post_type_landing_page = TRUE;
          $sorted_items                         = $this->add_ancestor_class( $item, $sorted_items );
        }
        if ( is_post_type_archive( $object_post_type ) ) {
          $item->classes[] = 'current-menu-item';
          $item->current   = TRUE;
          $sorted_items    = $this->add_ancestor_class( $item, $sorted_items );
        }
        if ( is_archive() && $object_post_type === $wp_query->get( 'post_type' ) ) {
          $sorted_items = $this->add_ancestor_class( $item, $sorted_items );
        }
      }
    }

    return $sorted_items;
  }


  /**
   * Filter the Yoast SEO title for archive pages
   *
   * @param string $title
   * @return string
   */
  public function filter_wpseo_title( string $title ): string {
    $queried_object = $this->get_archive_post_type();

    if ( $queried_object ) {
      $post_type_label = $queried_object->label;
      $archive         = \Timber::get_post( get_option( "page_for_$queried_object->name" ) );
      $title           = str_replace( "$post_type_label Archive", $archive->post_title, $title );
      return $title;
    }

    if ( is_tag() || is_category() ) {
      $queried_object = get_queried_object();
      $title          = str_replace( "$queried_object->name Archives", $queried_object->name, $title );
    }

    return $title;
  }


  /**
   * Filter the Yoast SEO breadcrumb single links
   *
   * @param string $link_output
   * @param array $link
   * @return string
   */
  public function filter_wpseo_breadcrumb_single_link( string $link_output, array $link ): string {

    $archive_pages = Utils\get_archive_page_details();

    if ( isset( $link['url'] ) ) {
      $link_output = str_replace( $link['url'], Utils\root_relative_url( $link['url'] ), $link_output );
    }

    // Resources Landing Page
    if ( isset( $link['id'] ) && isset( $archive_pages['post']['id'] ) && $link['id'] === intval( $archive_pages['post']['id'] ) ) {
      $link_output = str_replace( $link['text'], $archive_pages['post']['title'], $link_output );

      // CPT Landing Page
    } elseif ( isset( $link['ptarchive'] ) && isset( $archive_pages[ $link['ptarchive'] ] ) ) {
      $post_type   = $link['ptarchive'];
      $link_output = str_replace( $link['text'], $archive_pages[ $post_type ]['title'], $link_output );
    }

    return $link_output;
  }


  /**
   * Filter the Yoast SEO breadcrumb links
   *
   * @param array $links
   * @return array
   */
  public function filter_wpseo_breadcrumb_links( array $links ): array {

    // Loop through each link
    foreach ( $links as $index => $link ) {

      // Make URLs relative
      if ( isset( $link['url'] ) ) {
        $link['url'] = Utils\root_relative_url( $link['url'] );
      }

      // Check if link is for a custom post type archive
      if ( isset( $link['ptarchive'] ) ) {

        // Get all ancestors for the archive
        $post_type   = $link['ptarchive'];
        $ancestors   = get_post_ancestors( get_option( "page_for_{$post_type}" ) );

        // If the archive has ancestors
        if ( count( $ancestors ) ) {

          // Create an array of breadcrumbs links to insert into the breadcrumbs
          $ancestor_links = [];

          // Loop through all ancesters
          foreach ( $ancestors as $id ) {
            $ancestor = \Timber::get_post( $id );

            // Add ancestors as breadcrumb links
            $ancestor_links[] = [
              'url'  => Utils\root_relative_url( $ancestor->link() ),
              'text' => $ancestor->title(),
              'id'   => $ancestor->ID,
            ];
          }

          // Insert ancestors at position 1 (after home)
          Utils\array_insert( $links, 1, $ancestor_links );
        }
      }
    }

    return $links;
  }


  /**
   * Callback to add the post type template surrogate key for pages handled by
   * this class.
   *
   * @param \Purgely_Surrogate_Keys_Header $keys_header
   *   Fastly plugin's surrogate keys header object.
   */
  public function purgely_pre_send_keys( \Purgely_Surrogate_Keys_Header $keys_header ): void {
    $queried_post_type = $this->get_archive_post_type();
    if ( $queried_post_type ) {
      $keys_header->add_key( self::FASTLY_ARCHIVE_TYPE_KEY_PREFIX . $queried_post_type->name );
    }
  }


  /**
   * Callback to add cpt-based key for purging to posts processed by Fastly.
   *
   * @param array $collection
   *   The keys to purge.
   * @param \WP_Post|bool $post
   *   The post being processed, or boolean false if Fastly could not find a
   *   post.
   */
  public function purgely_related_keys( array $collection, \WP_Post|bool $post ): array {
    if ( ! $post ) {
      return $collection;
    }

    $collection[] = self::FASTLY_ARCHIVE_TYPE_KEY_PREFIX . $post->post_type;
    return $collection;
  }


  /**
   * ========================================
   * Protected methods
   * ========================================
   */


  /**
   * Returns the post type that the current page is an archive for.
   *
   * @return \WP_Post_Type|null
   *   The post type object for the current archive, or NULL if this is not a
   *   post type archive page.
   */
  protected function get_archive_post_type(): \WP_Post_Type|null {
    $queried_object = get_queried_object();
    if (
      $queried_object instanceof \WP_Post_Type &&
      is_post_type_archive( $queried_object->name )
    ) {
      return $queried_object;
    }

    return NULL;
  }


  /**
   * Gets an array with post types as keys and corresponding page IDs as values
   *
   * @return array
   */
  protected function get_page_ids(): array {

    $page_ids = [];

    $cpts = get_post_types( [
      'public'      => TRUE,
      'has_archive' => TRUE,
    ], 'objects' );

    foreach ( $cpts as $post_type ) {

      if ( 'post' === $post_type->name ) {
        $page_id = get_option( 'page_for_posts' );
      } else {
        $page_id = get_option( "page_for_{$post_type->name}" );
      }

      if ( ! $page_id ) {
        continue;
      }

      $page_ids[ $post_type->name ] = $page_id;
    }

    return $page_ids;
  }


  /**
   * Recursively set the ancestor class
   *
   * @param object $child
   * @param array $items
   * @return array
   */
  protected function add_ancestor_class( object $child, array $items ): array {

    if ( ! intval( $child->menu_item_parent ) ) {
      return $items;
    }

    foreach ( $items as $item ) {
      if ( intval( $item->ID ) === intval( $child->menu_item_parent ) ) {
        $item->classes[]                      = 'current-post-type-landing-page';
        $item->current_post_type_landing_page = TRUE;
        if ( intval( $item->menu_item_parent ) ) {
          $items = $this->add_ancestor_class( $item, $items );
        }
        break;
      }
    }

    return $items;
  }
}

new Page_For_Post_Type();


/**
 * Add new location rule for content type landing pages
 */
class ACF_Location_Page_for_Posts extends \ACF_Location {

  /**
   * Initializes props.
   *
   * @param	void
   * @return void
   */
  public function initialize(): void {
    $this->name           = 'page_for_post_types';
    $this->label          = __( 'Post Type Landing Page', 'craftedsupply' );
    $this->category       = 'page';
    $this->object_type    = 'post';
    $this->object_subtype = 'page';
  }


  /**
   * Matches the provided rule against the screen args returning a bool result.
   *
   * @param	array $rule The location rule.
   * @param	array $screen The screen args.
   * @param	array $field_group The field group settings.
   * @return bool
   */
  public function match( $rule, $screen, $field_group ): bool {

    $result = false;

    // Check screen args.
    if ( isset( $screen['post_id'] ) && isset( $screen['post_type'] ) && $screen['post_type'] === 'page' ) {
      $post_id = $screen['post_id'];
    } else {
      return FALSE;
    }

    $cpts = array_keys( get_post_types( [
      'public'      => TRUE,
      'has_archive' => TRUE,
    ], 'names' ) );

    foreach ( $cpts as $cpt ) {

      if ( $rule['value'] !== "page_for_$cpt" ) {
        continue;
      }

      $page   = (int) get_option( "page_for_$cpt" );
      $result = ( $page === $post_id );
    }

    // Reverse result for "!=" operator.
    if ( $rule['operator'] === '!=' ) {
      return ! $result;
    }
    return $result;
  }


  /**
   * Returns an array of possible values for this rule type.
   *
   * @param	array $rule A location rule.
   * @return array
   */
  public function get_values( $rule ): array {

    $choices = [];

    $cpts = get_post_types( [
      'public'      => TRUE,
      'has_archive' => TRUE,
    ], 'objects' );

    foreach ( $cpts as $cpt ) {
      $choices[ "page_for_{$cpt->name}" ] = "{$cpt->labels->name} Landing Page";
    }

    return $choices;
  }
}


/**
 * Register the custom location rule
 *
 * @return void
 */
function custom_acf_location_rules(): void {
  acf_register_location_type( __NAMESPACE__ . '\\ACF_Location_Page_for_Posts' );
}
add_action('acf/init', __NAMESPACE__ . '\\custom_acf_location_rules');
