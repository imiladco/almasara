<?php



function product_description_shortcode($atts, $content = null)
{
    // بررسی وجود ووکامرس
    if (!function_exists('wc_get_product')) {
        return '';
    }

    // بازیابی محصول
    global $post;
    $product_id = isset($post) ? $post->ID : 0;
    $product = wc_get_product($product_id);
    if (!$product) {
        return '';
    }

    // بررسی وجود ویژگی‌ها، توضیحات و FAQ
    $attributes = $product->get_attributes();
    $has_specifications = !empty($attributes);
    $has_description = !empty($product->get_description());
    $has_faq = (new WP_Query(['cat' => 1790, 'posts_per_page' => 1]))->have_posts();

    ob_start();
?>

    <div class="product-description-section">
        <?php if ($has_description) : ?>
            <div class="prd-ds introduction">
                <h2>
                    <img alt="" src="/wp-content/uploads/2024/06/glass.svg">
                    معرفی محصول <?php echo esc_html($product->get_name()); ?>
                </h2>
                <p><?php echo wp_kses_post(apply_filters('the_content', $product->get_description())); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($has_specifications) : ?>
            <div class="prd-ds specifications">
                <h2>
                    <img alt="" src="/wp-content/uploads/2024/06/setting-3.svg">
                    مشخصات <?php echo esc_html($product->get_name()); ?>
                </h2>
                <table class="custom-attributes-table">
                    <?php foreach ($attributes as $attribute) : ?>
                        <tr class="custom-attribute-row">
                            <th class="custom-attribute-name"><?php echo esc_html(wc_attribute_label($attribute->get_name())); ?></th>
                            <td class="custom-attribute-value">
                                <?php
                                $values = array();
                                if ($attribute->is_taxonomy()) {
                                    $attribute_values = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names'));
                                    $values = apply_filters('woocommerce_attribute', $attribute_values, $attribute, $product);
                                } else {
                                    $values = array_map('trim', $attribute->get_options());
                                }
                                echo esc_html(implode(', ', $values));
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>

        <?php if ($has_faq) : ?>
            <div class="prd-ds faq">
                <?php echo do_shortcode('[wocommerce_faq]'); ?>
            </div>
        <?php endif; ?>
    </div>

<?php
    return ob_get_clean();
}
add_shortcode('product_description', 'product_description_shortcode');



add_shortcode('product_full_attribute', 'product_full_attribute');
function product_full_attribute($atts)
{
    // بررسی وجود ووکامرس
    if (!function_exists('wc_get_product')) {
        return '';
    }

    // بازیابی محصول؛ اگر پست فعلی محصول نباشد خروجی خالی برگردان
    global $post;
    $product_id = isset($post) ? $post->ID : 0;
    $product = wc_get_product($product_id);
    if (!$product) {
        return '';
    }

    $attributes = $product->get_attributes();
    ob_start();
?>

    <?php foreach ($attributes as $attribute) : ?>
        <tr class="custom-attribute-row">
            <th class="custom-attribute-name"><?php echo esc_html(wc_attribute_label($attribute->get_name())); ?></th>
            <td class="custom-attribute-value">
                <?php
                $values = array();
                if ($attribute->is_taxonomy()) {
                    $attribute_values = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names'));
                    $values = apply_filters('woocommerce_attribute', $attribute_values, $attribute, $product);
                } else {
                    $values = array_map('trim', $attribute->get_options());
                }
                echo esc_html(implode(', ', $values));
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php
    return ob_get_clean();
}
