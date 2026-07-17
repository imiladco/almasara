<?


function get_product_min_price()
{
    if (! isset($_POST['product_id'])) {
        wp_send_json_error('No product ID found');
        return;
    }

    $product_id = intval($_POST['product_id']);
    $product = wc_get_product($product_id);

    if (! $product) {
        wp_send_json_error('Invalid product ID');
        return;
    }

    if ($product->is_type('variable')) {
        $available_variations = $product->get_available_variations();
        $variation_prices = array();
        foreach ($available_variations as $variation) {
            $variation_prices[] = $variation['display_price'];
        }
        $min_price = min($variation_prices);
        $price_html = wc_price($min_price);
    } else {
        $price_html = $product->get_price_html();
    }

    wp_send_json_success(array('price_html' => $price_html));
}

add_filter('jet-engine/query-builder/query/query-args', 'jetengine_custom_sale_products_query', 10, 3);

function jetengine_custom_sale_products_query($query_args, $query_id, $query)
{
    if ('sale_products_query' === $query_id) {
        // گرفتن شناسه تمامی محصولات تخفیف‌خورده
        $product_ids_on_sale = wc_get_product_ids_on_sale();

        // لیستی برای شناسه محصولات والد که دارای واریانت تخفیف‌خورده هستند
        $parent_product_ids = array();

        // بررسی واریانت‌ها و افزودن شناسه والد آنها به لیست
        foreach ($product_ids_on_sale as $product_id) {
            $product = wc_get_product($product_id);
            if ($product->is_type('variation')) {
                $parent_id = wp_get_post_parent_id($product_id);
                if ($parent_id) {
                    $parent_product_ids[] = $parent_id;
                }
            }
        }

        // ادغام شناسه‌های محصولات والد با لیست اصلی
        $all_product_ids = array_merge($product_ids_on_sale, $parent_product_ids);
        $all_product_ids = array_unique($all_product_ids);

        // تنظیم شناسه‌ها در کوئری
        if (!empty($all_product_ids)) {
            $query_args['post__in'] = $all_product_ids;
            $query_args['orderby'] = 'modified';
            $query_args['order'] = 'DESC';
        } else {
            $query_args['post__in'] = array(0); // نمایش هیچ محصولی اگر محصول تخفیف‌خورده وجود نداشته باشد
        }
    }

    return $query_args;
}