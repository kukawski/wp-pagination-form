<?php
/*
 * Plugin Name: WP Pagination Form
 * Description: Very simple pagination based on a form
 * Version: 1.0
 * Author: Rafał Kukawski
 * Author URI: https://kukawski.net
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 3.9
 * Requires PHP: 5.3
 * Text Domain: wp-pagination-form
 * Domain Path: /languages
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

add_action('plugins_loaded', function () {
  load_plugin_textdomain('wp-pagination-form', FALSE, basename(dirname(__FILE__)) . '/languages/');
});

function wp_pagination_form ($previous_posts_link_label = NULL, $next_posts_link_label = NULL) {
  global $wp_query;

  $posts_per_page = intval(get_query_var('posts_per_page'));
  $pages_count = absint($wp_query->max_num_pages);
  $current_page = min(max(1, absint(get_query_var('paged', 1))), $pages_count);

  previous_posts_link($previous_posts_link_label);
  ?><form method="post" action="">
    <?php
      $size = max(1, floor(log10($pages_count)));
      printf(
        __('Page %1$s of %2$s', 'wp-pagination-form'),
        "<input name=\"go_to_page\" value=\"$current_page\" size=\"$size\">",
        $pages_count
      );
    ?>
    </form><?php
  next_posts_link($next_posts_link_label);
}
?>
