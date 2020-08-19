<?php
/*
 * Plugin Name: WP Pagination Form
 * Description: Very simple pagination based on a form
 * Version: 1.0.1
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

define('WP_PAGINATION_FORM_RENDER_LINKS', false);

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

add_action('init', function () {
  $pagination_placement = get_option('pagination_placement', 'off');

  $above = $pagination_placement === 'above_content' || $pagination_placement === 'above_and_below_content';
  $below = $pagination_placement === 'below_content' || $pagination_placement === 'above_and_below_content';

  $with_links = defined('WP_PAGINATION_FORM_RENDER_LINKS') && WP_PAGINATION_FORM_RENDER_LINKS;

  $render_pagination = function () use ($with_links) {
    if ($with_links) {
      wp_pagination_form_with_links();
    } else {
      wp_pagination_form();
    }
  };

  if ($above) {
    add_action('loop_start', $render_pagination);
  }

  if ($below) {
    add_action('loop_end', $render_pagination);
  }
});

add_action('plugins_loaded', function () {
  load_plugin_textdomain('wp-pagination-form', FALSE, basename(dirname(__FILE__)) . '/languages/');
});

function wp_pagination_form_with_links ($previous_posts_link_label = NULL, $next_posts_link_label = NULL) {
  if (is_singular()) {
    return;
  }

  previous_posts_link($previous_posts_link_label);
  wp_pagination_form();
  next_posts_link($next_posts_link_label);
}

function wp_pagination_form () {
  if (is_singular()) {
    return;
  }

  list($current_page, $total_pages) = get_paging_details();

  if ($total_pages < 1) {
    return;
  }

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

add_action('admin_init', function () {
  add_settings_section(
    'wp-pagination-form-placement',
    __('Pagination display', 'wp-pagination-form'),
    function () {
      _e('Select where to display the pagination', 'wp-pagination-form');
    },
    'wp-pagination-form-options'
  );

  add_settings_field(
    'pagination_placement',
    '<label for="pagination_placement">' . __('Pagination placement', 'wp-pagination-form') . '</label>',
    function () {
      $options = [
        // value => translation key
        'off' => 'No pagination',
        'below_content' => 'Below content',
        'above_content' => 'Above content',
        'above_and_below_content' => 'Above & below content'
      ];
      $default_option = 'off';
      $selected_option = get_option('pagination_placement', $default_option);

      ?><select id="pagination_placement" name="pagination_placement"><?php
      foreach ($options as $key => $label) :
        ?><option value="<?=esc_attr($key)?>" <?php selected($key, $selected_option) ?>>
          <?=esc_html__($label)?>
        </option><?php
      endforeach;
    },
    'wp-pagination-form-options',
    'wp-pagination-form-placement'
  );

  register_setting(
    'wp-pagination-form-options',
    'pagination_placement'
  );
});

add_action('admin_menu', function () {
  add_options_page(__('Pagination', 'wp-pagination-form'), __('Pagination', 'wp-pagination-form'), 'manage_options', 'wp-pagination-form-options', function () {
    ?>
    <div class="wrap">
      <h1><?php _e('Pagination', 'wp-pagination-form') ?></h1>
      <form method="post" action="options.php">
        <?php
          settings_fields('wp-pagination-form-options');
          do_settings_sections('wp-pagination-form-options');
        ?>
        <div><?php submit_button(); ?></div>
      </form>
    </div>
    <?php
  });
});
?>
