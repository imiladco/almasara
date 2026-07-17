<?php

function display_product_discount($atts)
{
    global $product;

    if (! is_product()) {
        return '';
    }

    $product_id = $product->get_id();
    $output = '';

    // بررسی محصول متغیر
    if ($product->is_type('variable')) {
        $available_variations = $product->get_available_variations();
        $discounts = array();

        foreach ($available_variations as $variation) {
            $variation_obj = new WC_Product_Variation($variation['variation_id']);
            $regular_price = $variation_obj->get_regular_price();
            $sale_price = $variation_obj->get_sale_price();

            if ($sale_price && $regular_price && $regular_price > $sale_price) {
                $discount = (($regular_price - $sale_price) / $regular_price) * 100;
                $discounts[] = round($discount);
            }
        }

        if (! empty($discounts)) {
            $max_discount = max($discounts);
            $output = '<div class="product-discount">' . $max_discount . '% تخفیف </div>';
        }
    } else {
        // بررسی محصول ساده
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();

        if ($sale_price && $regular_price && $regular_price > $sale_price) {
            $discount = (($regular_price - $sale_price) / $regular_price) * 100;
            $output = '<div class="product-discount">' . round($discount) . '%</div>';
        }
    }

    return $output;
}

add_shortcode('product_discount', 'display_product_discount');


function display_discount_info()
{
    global $product;

    if (!$product) return '';

    $output = '';

    function calculate_discount($regular_price, $sale_price)
    {
        $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
        $discount_amount = $regular_price - $sale_price;
        return [
            'percentage' => $discount_percentage,
            'amount' => $discount_amount
        ];
    }

    // برای محصول ساده
    if ($product->is_type('simple')) {
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();

        if ($sale_price && $regular_price && $sale_price < $regular_price) {
            $discount = calculate_discount($regular_price, $sale_price);

            $output .= '<div class="box-offer">';
            $output .= '<div class="offer-darsad">' . $discount['percentage'] . '% تخفیف</div>';
            $output .= '<div class="offer-mablag">' . number_format($discount['amount']) . ' تومان تخفیف</div>';
            $output .= '</div>';
        }
    }
    // برای محصول متغیر
    elseif ($product->is_type('variable')) {
        $variations = $product->get_available_variations();
        $has_discount = false;
        $discount_data = [];

        // بررسی هر متغیر برای تخفیف
        foreach ($variations as $variation) {
            $variation_product = wc_get_product($variation['variation_id']);
            $regular_price = $variation_product->get_regular_price();
            $sale_price = $variation_product->get_sale_price();

            if ($sale_price && $regular_price && $sale_price < $regular_price) {
                $has_discount = true;
                $discount = calculate_discount($regular_price, $sale_price);
                $discount_data[$variation['variation_id']] = [
                    'percentage' => $discount['percentage'],
                    'amount' => $discount['amount']
                ];
            }
        }

        // اگه هیچ متغیری تخفیف نداشت، خروجی خالی برگردون
        if (!$has_discount) {
            return '';
        }

        // تولید div مخفی برای هر متغیر تخفیف‌دار + جاوااسکریپت برای نمایش پویا
        $output .= '<div id="variable-offers" class="box-offer" style="display: none;">';
        $output .= '<div class="offer-darsad"></div>';
        $output .= '<div class="offer-mablag"></div>';
        $output .= '</div>';

        // جاوااسکریپت برای نمایش پویا
        $output .= '<script>';
        $output .= '(function($) {';
        $output .= '  $(document).ready(function() {';
        $output .= '    var discountData = ' . json_encode($discount_data) . ';';
        $output .= '    $("form.variations_form").on("show_variation", function(event, variation) {';
        $output .= '      var variationId = variation.variation_id;';
        $output .= '      var $offerBox = $("#variable-offers");';
        $output .= '      if (discountData[variationId]) {';
        $output .= '        $offerBox.find(".offer-darsad").text(discountData[variationId].percentage + "% تخفیف");';
        $output .= '        $offerBox.find(".offer-mablag").text(new Intl.NumberFormat("fa-IR").format(discountData[variationId].amount) + " تومان تخفیف");';
        $output .= '        $offerBox.show();';
        $output .= '      } else {';
        $output .= '        $offerBox.hide();';
        $output .= '      }';
        $output .= '    });';
        $output .= '    $("form.variations_form").on("reset_data", function() {';
        $output .= '      $("#variable-offers").hide();';
        $output .= '    });';
        $output .= '  });';
        $output .= '})(jQuery);';
        $output .= '</script>';
    }

    // اگه هیچ خروجی‌ای تولید نشد، رشته خالی برگردون
    if (empty($output)) {
        return '';
    }

    return $output;
}

add_shortcode('discount_info', 'display_discount_info');













function discounted_price_row_shortcode($atts)
{
    global $product;

    // فقط همین شرط (امن)
    if ( ! $product || ! is_a( $product, 'WC_Product' ) || ! $product->is_on_sale() ) {
        return '';
    }

    $regular_price = $product->get_regular_price(); // قیمت اصلی
    $sale_price    = $product->get_sale_price();    // قیمت تخفیف‌دار
    $price_html    = $product->get_price_html();    // قیمت فعلی (معمولاً با خط خوردن روی قیمت اصلی)
    
    // محاسبه درصد تخفیف
    if ($regular_price > 0) {
        $discount_percent = round((($regular_price - $sale_price) / $regular_price) * 100);
    } else {
        $discount_percent = 0;
    }

    ob_start(); ?>

    <div class="shortcode-price-row justify-end gap-2">
        <div class="flex flex-row gap-6 align-center justify-end">
            <span class="original-price">
                    <?php echo number_format_i18n($regular_price); ?>
            </span>
            <span class="discount-badge flex flex-row gap-2 align-center justify-end">
                <?php echo $discount_percent; ?>
                <img src="<?php echo get_theme_file_uri('/assets/img/woocommerce/Discount-01.svg'); ?>" alt="<?php echo esc_attr('تخفیف ' . $discount_percent . '% محصول ' . $product->get_name()); ?>">
            </span>

        </div>
        <span class="current-price flex flex-row gap-6 align-center justify-end">
                <?php echo number_format_i18n($sale_price); ?>
                <span class="currencySymbol">تومانءءء</span>
        </span>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('discounted_price_row', 'discounted_price_row_shortcode');

