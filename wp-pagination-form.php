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

function get_paging_details () {
  global $wp_query;

  $total_pages = absint($wp_query->max_num_pages);
  $current_page = min(max(1, absint(get_query_var('paged', 1))), $total_pages);

  return [ $current_page, $total_pages ];
}

// getting paging details doesn't work in earlier hooks like init or wp_loaded
add_action('wp', function () {
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['go_to_page'])) {
    list($current_page, $total_pages) = get_paging_details();
    $goto_page = max(1, min($_POST['go_to_page'], $total_pages));

    wp_redirect(get_pagenum_link($goto_page));
    exit;
  }
});

add_action('init', function () {
  wp_register_style('wp-pagination-form', plugins_url('wp-pagination-form.css', __FILE__));
});

add_action('plugins_loaded', function () {
  load_plugin_textdomain('wp-pagination-form', FALSE, basename(dirname(__FILE__)) . '/languages/');
});

function wp_pagination_form_with_links ($previous_posts_link_label = NULL, $next_posts_link_label = NULL) {
  previous_posts_link($previous_posts_link_label);
  wp_pagination_form();
  next_posts_link($next_posts_link_label);
}

function wp_pagination_form () {
  list($current_page, $total_pages) = get_paging_details();

  wp_enqueue_style('wp-pagination-form');

  ?><form method="post" action="" class="wp-pagination-form">
      <label>
      <?php
        $size = max(1, floor(log10($total_pages)));
        printf(
          __('Page %1$s of %2$s', 'wp-pagination-form'),
          "<input name=\"go_to_page\" value=\"$current_page\" size=\"$size\">",
          $total_pages
        );
      ?>
      </label>
    </form><?php
}
?>
