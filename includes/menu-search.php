<?


function custom_woocommerce_search_shortcode()
{
    ob_start();
?>
    <div id="custom-search-container">
        <div id="search-icon-container">
            <span id="search-icon"></span>
        </div>
        <div id="search-input-container">

            <form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="search" id="custom-search-field" class="search-field" placeholder="جستجو در محصولات…" value="" name="s" autocomplete="off">
                <button type="submit" class="search-submit"><img src="/wp-content/uploads/2024/07/search-status-1.svg"></button>
                <span id="close-search"><img src="/wp-content/uploads/2024/07/Group-1000002050.svg"></span>
                <input type="hidden" name="post_type" value="product">
            </form>
        </div>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('custom_search', 'custom_woocommerce_search_shortcode');