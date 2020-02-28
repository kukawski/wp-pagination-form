<?php
/*
Plugin Name: WP Pagination Form
Description: Very simple pagination plugin based on form
Version: 1.0
Author: Rafał Kukawski
Author URI: https://kukawski.net
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.9
Requires PHP: 5.3
Text Domain: wp-pagination-form
*/

// exit if not called as part of WordPress
if (!defined('ABSPATH')) {
  exit;
}

add_action('init', function () {
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['go_to_page'])) {
    wp_redirect(get_pagenum_link($_POST['go_to_page']));
    exit;
  }
});

function wp_pagination_form () {
  global $wp_query;

  $posts_per_page = intval(get_query_var('posts_per_page'));
  $pages_count = absint($wp_query->max_num_pages);
  $current_page = min(max(1, absint(get_query_var('paged', 1))), $pages_count);

  previous_posts_link();
  ?><form method="post" action="">
    Page <input name="go_to_page" value="<?=$current_page?>" size="<?=floor(log10(abs($pages_count))) + 1?>"> of <?=$pages_count?>
    </form><?php
  next_posts_link();
}
?>
