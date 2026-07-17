<?php
remove_action('shutdown', 'wp_ob_end_flush_all', 1);
add_action('shutdown', function() {
    while (@ob_end_flush());
});



/*
      - Function definition 
    <--  Function definition files -->
***** ** **** ** **** ** **** ** **** ** **** ** **** ** **** ** **** **/
function almasara_includes(): void {
    $includes_dir = get_stylesheet_directory() . '/includes/';

    if (!is_dir($includes_dir)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($includes_dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    $files = [];
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }

    sort($files, SORT_STRING);

    foreach ($files as $file) {
        if (is_readable($file)) {
            require_once $file;
        }
    }
}
almasara_includes();

/*
      - Function definition 
    <--  Enqueue script and styles for child theme -->
***** ** **** ** **** ** **** ** **** ** **** ** **** ** **** ** **** **/
function almasara_assets() {
    // مسیرهای اصلی
    $child_theme_uri = get_stylesheet_directory_uri();
    $child_theme_css = $child_theme_uri . '/assets/css/';
    $child_theme_js  = $child_theme_uri . '/assets/js/';
    $version = wp_get_theme()->get('Version');
    

    // ==================== CSS موبایل / دسکتاپ ====================
    if ( wp_is_mobile() ) {
        wp_enqueue_style( 'header-mobile', $child_theme_css . 'header-mobile.css', [], $version );
        wp_enqueue_style( 'footer-mobile', $child_theme_css . 'footer-mobile.css', [], $version );
    } else {
        wp_enqueue_style( 'header-desktop', $child_theme_css . 'header-desktop.css', [], $version );
        wp_enqueue_style( 'footer-desktop', $child_theme_css . 'footer-desktop.css', [], $version );
    }

    // ==================== CSS های مشترک ====================
    wp_enqueue_style( 'child-style', $child_theme_uri . '/style.css', ['hello-elementor'], $version );
    wp_enqueue_style( 'kit', $child_theme_css . 'kit.css', [], $version );
    wp_enqueue_style( 'custom-style', $child_theme_css . 'custom.css', [], $version );
    wp_enqueue_style( 'font', $child_theme_uri . '/assets/font.css', [], $version );

    // ==================== JS های مشترک ====================
    wp_enqueue_script( 'header', $child_theme_js . 'header.js', ['jquery'], $version, true );
    wp_enqueue_script( 'cart-fragment', $child_theme_js . 'cart-fragment.js', ['jquery'], $version, true );

    // localize برای cart-fragment
    wp_localize_script( 'cart-fragment', 'cartAjax', [
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'cart_nonce' ),
    ] );
    
    // ==================== فایل های اسلایدر ====================
    if ( is_front_page() ) {
        wp_enqueue_style( 'slider-css', $child_theme_css . 'slider.css', [], $version );
        wp_enqueue_script( 'slider-js', $child_theme_js . 'slider.js', [], $version, true );
    }

    // custom.js فقط خارج از سبد و تسویه‌حساب
    if ( ! is_checkout() && ! is_cart() ) {
        wp_enqueue_script( 'custom-js', $child_theme_js . 'custom.js', ['jquery'], $version, true );
    }

    // ==================== صفحه تک محصول ====================
    if ( is_product() ) {
        wp_enqueue_style( 'custom-product-css', $child_theme_css . 'single-product.css', [], $version );
        
        wp_enqueue_script( 'faq', $child_theme_js . 'faq.js', ['jquery'], $version, true );

        // اگر محصول در دسته‌بندی خاص (ID: 1794) بود، استایل واتساپ لود بشه
        if ( has_term( 1794, 'product_cat', get_the_ID() ) ) {
            wp_enqueue_style( 'whatsapp', $child_theme_css . 'whatsapp.css', [], $version );
        }
    }

    // ==================== صفحه سبد خرید ====================
    if ( is_cart() ) {
        wp_enqueue_style( 'custom-cart', $child_theme_css . 'custom-cart.css', [], $version );
        
        wp_enqueue_script( 'custom-cart', $child_theme_js . 'custom-cart.js', ['jquery', 'wc-cart-fragments'], $version, true );
        
        wp_localize_script( 'custom-cart', 'wc_cart_params', [
            'wc_ajax_url'          => WC_AJAX::get_endpoint( '%%endpoint%%' ),
            'update_cart_nonce'    => wp_create_nonce( 'woocommerce-cart' ),
            'remove_cart_item_nonce' => wp_create_nonce( 'woocommerce-cart' ),
        ] );
    }

    // ==================== صفحه تسویه حساب ====================
    if ( is_checkout() ) {
        wp_enqueue_style( 'custom-checkout', $child_theme_css . 'custom-checkout-style.css', [], $version );
    }
}
add_action( 'wp_enqueue_scripts', 'almasara_assets', 10010 );



