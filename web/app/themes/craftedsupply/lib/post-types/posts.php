<?php

namespace CraftedSupply\PostTypes\Posts;

function hide_posts_from_admin() { 
   remove_menu_page('edit.php');
}

add_action('admin_menu', __NAMESPACE__ . '\\hide_posts_from_admin');