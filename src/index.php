<?php
/*
Plugin Name: WP Pagination Form
Description: Very simple pagination plugin based on form
Version: 1.0
Author: Rafał Kukawski
Author URI: https://kukawski.net
Text Domain: wp-pagination-form
*/

function wp_pagination_form () {
  global $wp_query;
  $posts_per_page = intval(get_query_var('posts_per_page'));
  $pages_count = absint($wp_query->max_num_pages);
  $current_page = max(1, absint(get_query_var('paged')));

  previous_posts_link();
  ?><form method="get" action="">
    Page <input name="paged" value="<?=$current_page?>" size="<?=floor(log10(abs($pages_count))) + 1?>"> of <?=$pages_count?>
    </form><?php
  next_posts_link();
}
?>
