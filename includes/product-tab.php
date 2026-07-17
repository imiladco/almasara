<?


function product_tabs_shortcode($atts, $content = null)
{
    // بررسی موجود بودن ووکامرس
    if (! function_exists('wc_get_product')) {
        return '';
    }

    // بازیابی محصول
    global $post;
    $product_id = isset($post) ? $post->ID : 0;
    $product = wc_get_product($product_id);
    if (!$product) {
        return '';
    }

    // دریافت ویژگی‌های محصول
    $attributes = $product->get_attributes();
    $has_specifications = !empty($attributes);
    $has_description = !empty($product->get_description());
    $has_reviews = $product_id ? comments_open($product_id) || get_comments_number($product_id) : false;
    $has_faq = (new WP_Query(['cat' => 1790, 'posts_per_page' => 1]))->have_posts();

    // تعیین اولین تب موجود
    $first_tab = $has_description  || $has_faq  ? '#tab-introduction' : ($has_specifications ? '#tab-specifications' : '#tab-reviews');

    ob_start();
?>
    <div class="product-tabs">
        <ul class="tabs">
            <?php if ($has_description || $has_faq) : ?>
                <li class="<?php echo ($first_tab == '#tab-introduction') ? 'active' : ''; ?>">
                    <a href="#tab-introduction"><img src="/wp-content/uploads/2024/06/glass.svg"><?php esc_html_e('معرفی', 'your-textdomain'); ?></a>
                    <div class="astyles_Tab__border"></div>
                </li>
            <?php endif; ?>
            <?php if ($has_specifications) : ?>
                <li class="<?php echo ($first_tab == '#tab-specifications') ? 'active' : ''; ?>">
                    <a href="#tab-specifications"><img src="/wp-content/uploads/2024/06/setting-3.svg"><?php esc_html_e('مشخصات', 'your-textdomain'); ?></a>
                    <div class="astyles_Tab__border"></div>
                </li>
            <?php endif; ?>
            <?php if ($has_reviews) : ?>
                <li class="<?php echo ($first_tab == '#tab-reviews') ? 'active' : ''; ?>">
                    <a href="#tab-reviews"><img src="/wp-content/uploads/2024/06/glass.svg"><?php esc_html_e('نظرات', 'your-textdomain'); ?></a>
                    <div class="astyles_Tab__border"></div>
                </li>
            <?php endif; ?>
        </ul>
        <div class="tab-content">
            <?php if ($has_description || $has_faq) : ?>
                <div id="tab-introduction" class="tab-pane<?php echo ($first_tab == '#tab-introduction') ? ' active' : ''; ?>">
                    <?php if ($has_description) : ?>
                        <h2><?php esc_html_e('معرفی', 'your-textdomain'); ?></h2>
                        <p><?php echo wp_kses_post(apply_filters('the_content', $product->get_description())); ?></p>
                    <?php endif; ?>

                    <?php if ($has_faq) : ?>
                        <?php echo do_shortcode('[wocommerce_faq]'); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($has_specifications) : ?>
                <div id="tab-specifications" class="tab-pane<?php echo ($first_tab == '#tab-specifications') ? ' active' : ''; ?>">
                    <h2><?php esc_html_e('مشخصات', 'your-textdomain'); ?></h2>
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
            <?php if ($has_reviews) : ?>
                <div id="tab-reviews" class="tab-pane<?php echo !$has_description && !$has_specifications ? ' active' : ''; ?>">
                    <h2><?php esc_html_e('نظرات', 'your-textdomain'); ?></h2>
                    <?php comments_template(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <style>

    </style>
    <script>
        (function($) {
            $(document).ready(function() {
                $('.product-tabs .tabs li a').on('click', function(e) {
                    e.preventDefault();
                    var target = $(this).attr('href');
                    $('.product-tabs .tabs li').removeClass('active');
                    $(this).parent().addClass('active');
                    $('.product-tabs .tab-pane').removeClass('active');
                    $(target).addClass('active');
                });

                // نمایش تب فعال هنگام بارگذاری صفحه
                var activeTab = $('.product-tabs .tabs li.active a').attr('href');
                $(activeTab).addClass('active');
            });
        })(jQuery);
    </script>
<?php
    return ob_get_clean();
}
add_shortcode('product_tabs', 'product_tabs_shortcode');