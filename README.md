# WP Pagination Form
WordPress pagination plugin with simple form.
It allows the user to jump to any selected page by typing the page number into small text field.

## Installation
Extract the plugin archive in your plugins directory and activate the plugin in admin panel.
Next, in your theme, find spot where pagination should be displayed. Call the `wp_pagination_form` function.

```php
<?php
if (function_exists('wp_pagination_form')) {
  wp_pagination_form();
} else {
  // fallback
}
?>
```

It will print the pagination in form
```
Page 1️⃣ of 10
```

Alternatively, the plugin offers the `wp_pagination_form_with_links` function that prints the form surrounded by links.

```
« Previous page    Page 1️⃣ of 10    Next page »
```

```php
if (function_exists('wp_pagination_form_with_links')) {
  wp_pagination_form_with_links('« Previous page', 'Next page »');
}
```
