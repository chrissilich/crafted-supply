<?php

namespace CraftedSupply\Modules\BlockAudit;

use CraftedSupply\Modules\Utils;
use \WP_List_Table;
use \WP_Query;
use \WP_User_Query;
use \WP_Block_Type_Registry;



/*
* When a post is saved, scrape it's post content object for block names
* and save them as post meta. This is used to determine which blocks are
* in use on the site, and which ones are not.
*/
function save_post_block_audit_meta( $post_id, $post, $update ) {

  // Find all instances of wp: followed by a block name with its namespace
  // e.g. wp:paragraph
  // or wp:craftedsupply/fifty-fifty
  preg_match_all('/wp:([a-zA-Z0-9\/-]+)/', $post->post_content, $matches);

  // Extract the matched strings
  $wp_blocks = json_encode($matches[1]);

  update_post_meta( $post_id, 'is_block_audit', $wp_blocks );

}

add_action( 'save_post', __NAMESPACE__ . '\\save_post_block_audit_meta', 10,3 );




/**
 * Add the admin page
 */
add_action( 'init', function () {
  Block_Audit_Admin_Page::get_instance();
} );


/**
 * Block Audit Admin Page
 */
class Block_Audit_Admin_Page {

  /** class instance */
  static $instance;

  /** customer WP_List_Table object */
  public $block_audit_table;

  /** Singleton enforcer */
  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /** Class constructor */
  public function __construct() {
    add_action( 'admin_menu', [ $this, 'add_block_audit_admin_menu' ] );
    add_action( 'screen_settings', [ $this, 'build_screen_options'] );
    add_filter( 'set-screen-option', [ $this, 'set_screen_option' ], 10, 3 );
  }

  /**
   * Add the admin menu item
   *
   * @return void
   */
  function add_block_audit_admin_menu() {
    add_submenu_page(
      'tools.php',
      'Block Audit',
      'Block Audit',
      'manage_options',
      'id-block-audit',
      [ $this, 'render_block_audit_admin_menu' ],
      1
    );
    $this->block_audit_table = new Block_Audit_Table();
  }

  /**
   * Screen options
   *
   * @return void
   */
  public function build_screen_options() {
    $option = 'per_page';
    $args = [
      'label'   => 'Blocks Per Page',
      'option'  => 'blocks_per_page',
      'default' => 10,
    ];
    add_screen_option( $option, $args );
  }

  /**
   * Set screen options
   *
   * @param mixed $sreen_option
   * @param string $option
   * @param int $value
   * @return int
   */
  public function set_screen_option( $screen_option, $option, $value ) {
    if ( 'blocks_per_page' === $option ) {
      return $value;
    }
  }


  /**
   * Render the admin menu page
   *
   * @return void
   */
  function render_block_audit_admin_menu() {
    ?>
    <style>
      .wp-list-table .column-title {
        width: 20%;
      }
      .wp-list-table .column-name {
        width: 25%;
      }
      .wp-list-table .column-used_by {
        width: 55%;
      }
      .wp-list-table .shy-link {
        visibility: hidden;
      }
      .wp-list-table tr:hover .shy-link {
        visibility: visible;
      }
      .wp-list-table .subtle {
        opacity: 0.5;
        font-size: 0.8em;
      }
    </style>
    <div class="wrap">
      <h2>Block Audit</h2>

      <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-4">
          <div id="post-body-content">
            <div class="meta-box-sortables ui-sortable">
              <form method="post">
                <?php
                  $this->block_audit_table->prepare_items();
                  $this->block_audit_table->search_box('Search', 's');
                  $this->block_audit_table->display();
                ?>
              </form>
            </div>
          </div>
        </div>
        <br class="clear">
      </div>
    </div>
    <?php
  }
}


/**
 * Block List Table
 *
 * @extends WP_List_Table
 * @link https://developer.wordpress.org/reference/classes/wp_list_table/
 */
class Block_Audit_Table extends WP_List_Table {

  /** Cache of blocks found by get_blocks */
  public $blocks      = [];
  public $blocks_page = [];


  /** Class constructor */
  public function __construct() {
    parent::__construct([
      'singular' => 'Block', // singular name of the listed records
      'plural'   => 'Blocks', // plural name of the listed records
      'ajax'     => FALSE // should this table support ajax?
    ]);
  }

  /**
   * Handles data query and filter, sorting, and pagination.
   *
   * @return void
   */
  public function prepare_items(): void {
    $this->_column_headers = $this->get_column_info();

    $per_page     = $this->get_items_per_page( 'blocks_per_page', 10 );
    $current_page = $this->get_pagenum();
    $search_term  = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : FALSE;
    $this->items  = $this->get_blocks( $per_page, $current_page, $search_term );
    $total_items  = count( $this->blocks );

    $this->set_pagination_args( [
      'total_items' => $total_items,
      'per_page'    => $per_page
    ] );
  }



  /**
  * Retrieve blocks from WP_Block_Type_Registry
  *
  * @param int $per_page
  * @param int $page_number
  * @param mixed $search_term
  * @return mixed
  */
  public function get_blocks( int $per_page = 5, int $page_number = 1, mixed $search_term = false ): mixed {

    $this->blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

    if ($search_term) {
      $this->blocks = array_filter($this->blocks, function($block) use ($search_term) {
        $search_string = strtolower($block->title . $block->name);
        $search_term = strtolower($search_term);
        return strpos($search_string, $search_term) !== false;
      });
    }

    $this->blocks_page = array_slice( $this->blocks, ( ( $page_number - 1 ) * $per_page ), $per_page );
    return $this->blocks_page;
  }

  /**
  * Method for name column
  *
  * @param array $item an array of DB data
  * @return string
  * @link https://developer.wordpress.org/reference/classes/wp_list_table/#extended-methods
  */
  function column_display_name( array $item ): string {
    $title = '<strong>' . $item['display_name'] . '</strong>';
    return $title . $this->row_actions( $actions );
  }

  /**
  * Method for name column
  *
  * @param array $item an array of DB data
  * @return string
  * @link https://developer.wordpress.org/reference/classes/wp_list_table/#extended-methods
  */
  function column_name( $item ) {
    return $item->name;
  }

  /**
  * Method for title column
  *
  * @param array $item an array of DB data
  * @return string
  * @link https://developer.wordpress.org/reference/classes/wp_list_table/#extended-methods
  */
  function column_title( $item ) {
    return $item->title;
  }

  /**
  * Method for calculate column
  *
  * @param array $item an array of DB data
  * @return string
  * @link https://developer.wordpress.org/reference/classes/wp_list_table/#extended-methods
  */
  function column_used_by( $item ) {
    // return "<button class='calculate-block-usage' data-id='".$item->name."'>Calculate</button>";
    $limit = 10;
    if ( array_key_exists('showall', $_GET) &&  $_GET['showall'] === $item->name) {
      $limit = 250;
    }

    // WP_Query arguments
    $args = array(
      'post_type' => 'any',
      'posts_per_page' => -1,
      'post_status' => array( 'publish', 'pending', 'draft', 'future', 'private' ),
      'meta_query' => array(
        array(
          'key'     => 'is_block_audit',
          'value'   => $item->name,
          'compare' => 'RLIKE',
        ),
      ),
    );

    $query = new WP_Query( $args );

    $links = [];
    if ( $query->have_posts() ) {
      while ( $query->have_posts() ) {
        $query->the_post();
        $title = get_the_title() ? substr(get_the_title(), 0, 25) : "Untitled";
        $links[] = "<a href='".get_edit_post_link()."'>".$title." <em class='subtle'>(".get_post_type().")</em></a>";
      }
    } else {
      return "<em style='opacity: 0.5'>unused</em>";
    }
    wp_reset_postdata();

    $link_total = count($links);
    if ($link_total > $limit) {
      $links = array_slice($links, 0, $limit);
      $str = implode(", ", $links);
      $str .= " and " . ($link_total - $limit) . " more. ";
      $str .= "<a href='".Utils\modify_admin_url(['showall' => $item->name])."'>Show all?</a>";
      return $str;
    }
    return implode(", ", $links);

  }

  /**
  * Render a column when no column specific method exists.
  *
  * @param object|array $item
  * @param string $column_name
  * @return mixed
  * @link https://developer.wordpress.org/reference/classes/wp_list_table/column_default/
  */
  public function column_default( $item, $column_name ) {
    switch ( $column_name ) {
      // case 'city':
      //   return $item[ $column_name ];
      default:
        return print_r( $item, TRUE ); // Show the whole array for troubleshooting purposes
    }
  }

  /**
  * Associative array of columns
  *
  * @return array
  * @link https://developer.wordpress.org/reference/classes/wp_list_table/get_columns/
  */
  function get_columns(): array {
    $columns = [
      'title'      => 'Title',
      'name'      => 'Block Slug',
      'used_by' => 'Used by...',
    ];

    return $columns;
  }

  /**
  * Columns to make sortable.
  *
  * @return array
  */
  public function get_sortable_columns(): array {
    $sortable_columns = [
      // 'name'        => 'name',
    ];
    return $sortable_columns;
  }

}
