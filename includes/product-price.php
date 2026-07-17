<?

function display_custom_product_price()
{
    global $product;

    if (! is_a($product, 'WC_Product')) {
        $product = wc_get_product(get_the_ID());
    }

    if (! $product) return '';

    $price_html = '';
    $is_on_sale = $product->is_on_sale();
    $is_variable = $product->is_type('variable');
    $is_in_stock = $product->is_in_stock();

    if (!$is_in_stock) {
        // اگر محصول موجود نیست
        $price_html = '<span class="out-of-stock">ناموجود</span>';
    } else {
        $has_price = false;

        if ($is_on_sale) {
            if ($is_variable) {
                $prices = [];
                foreach ($product->get_available_variations() as $variation) {
                    $variation_product = wc_get_product($variation['variation_id']);
                    if ($variation_product && $variation_product->is_on_sale()) {
                        $prices[] = [
                            'regular' => $variation_product->get_regular_price(),
                            'sale' => $variation_product->get_sale_price()
                        ];
                    }
                }

                if (! empty($prices)) {
                    $lowest_price = min(array_column($prices, 'sale'));
                    $corresponding_regular_price = $prices[array_search($lowest_price, array_column($prices, 'sale'))]['regular'];

                    if ($corresponding_regular_price > 0 && $lowest_price > 0) {
                        $discount_percentage = round((($corresponding_regular_price - $lowest_price) / $corresponding_regular_price) * 100);
                        $price_html = '<span class="price">' . wc_price($lowest_price);
                        $price_html .= ' <span class="ProductPrice-offer">' . $discount_percentage . '%</span></span>';
                        $has_price = true;
                    }
                }
            } else {
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();

                if ($regular_price > 0 && $sale_price > 0 && $regular_price > $sale_price) {
                    $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                    $price_html = '<span class="price">' . wc_price($sale_price);
                    $price_html .= ' <span class="ProductPrice-offer">' . $discount_percentage . '%</span></span>';
                    $has_price = true;
                } else if ($sale_price > 0) {
                    $price_html = '<span class="price">' . wc_price($sale_price) . '</span>';
                    $has_price = true;
                }
            }
        } else {
            if ($is_variable) {
                $prices = array();

                foreach ($product->get_available_variations() as $variation) {
                    $prices[] = $variation['display_price'];
                }

                if (! empty($prices)) {
                    $min_price = min($prices);
                    if ($min_price > 0) {
                        $price_html = '<span class="price">' . wc_price($min_price) . '</span>';
                        $has_price = true;
                    }
                }
            } else {
                $product_price_html = $product->get_price_html();
                if (!empty($product_price_html)) {
                    $price_html = '<span class="price">' . $product_price_html . '</span>';
                    $has_price = true;
                }
            }
        }

        // اگر قیمت وجود نداشت
        if (!$has_price) {
            $price_html = '<span class="contact-for-price">تماس بگیرید</span>';
        }
    }

    return $price_html;
}

add_shortcode('custom_product_price', 'display_custom_product_price');