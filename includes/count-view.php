<?


// شمارش بازدیدها
function add_product_view_count()
{
    if (is_singular('product')) {
        global $post;
        $view_count = get_post_meta($post->ID, 'product_view_count', true);
        $view_count = $view_count ? $view_count + 1 : 1;
        update_post_meta($post->ID, 'product_view_count', $view_count);
    }
}
add_action('wp_head', 'add_product_view_count');

// شورت‌کد برای نمایش تعداد بازدیدها
function display_product_view_count($atts)
{
    global $post;
    if ($post && $post->post_type == 'product') {
        $view_count = get_post_meta($post->ID, 'product_view_count', true);
        return $view_count ? $view_count : '0';
    }
    return 'این شورت‌کد فقط در صفحات محصولات کاربرد دارد.';
}
add_shortcode('product_view_count', 'display_product_view_count');