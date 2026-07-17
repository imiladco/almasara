<?php
// هدر موبایل رو فقط در موبایل لود کن، بقیه جاها هدر معمولی
add_action('wp', function() {
    if (wp_is_mobile()) {
        add_filter('template_include', function($template) {
            if (is_page() || is_single() || is_archive() || is_home()) {
                return get_stylesheet_directory() . '/header-mobile.php';
            }
            return $template;
        });
    }
});
?>