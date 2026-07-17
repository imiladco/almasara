<?


add_shortcode('whatsapp_buy_button', 'custom_whatsapp_buy_button');

function custom_whatsapp_buy_button()
{
    global $product;

    // Check if product exists and is in category ID 1794
    if (!is_a($product, 'WC_Product') || !has_term(1794, 'product_cat', $product->get_id())) {
        return '';
    }

    // Start output buffering
    ob_start();

    $button_text = __('Buy via WhatsApp', 'woocommerce');
    $product_id = esc_attr($product->get_id());
    $product_name = html_entity_decode($product->get_name(), ENT_QUOTES, 'UTF-8');
    $product_link = 'https://dksalamat.com/product/shafagostar-set/'; // You can replace this with get_permalink($product->get_id()) to get the actual product link

    // Generate button and logic
    if ($product->is_type('variable')) {
        // Variable product
    ?>
        <div class="whatsapp-buy-button variable-product">
            <a href="#"
                class="button whatsapp-button variable-whatsapp-button"
                data-product-id="<?php echo $product_id; ?>"
                data-product-name="<?php echo htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8'); ?>"
                data-product-link="<?php echo htmlspecialchars($product_link, ENT_QUOTES, 'UTF-8'); ?>">
                سفارش از طریق واتساپ
                <img alt="سفارش از طریق واتساپ" src="/wp-content/uploads/2024/10/bag-2.svg">
            </a>
        </div>
    <?php
    } else {
        // Simple product
        $quantity = 1;
        $message = "سلام،\n";
        $message .= "قصد سفارش این محصول رو دارم:\n";
        $message .= "> *" . esc_html($product->get_name()) . "*\n"; // Bold product name in blockquote
        $message .= "> تعداد: {$quantity} عدد\n"; // Quantity in blockquote
        $message .= "لطفاً قیمت نهایی، نحوه پرداخت و شرایط ارسال رو اطلاع بدید.\n\n";
        $message .= "`" . $product_link . "`"; // Inline code for product link

        $whatsapp_url = "https://wa.me/+989961007436?text=" . rawurlencode($message);
    ?>
        <div class="whatsapp-buy-button simple-product">
            <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" class="button whatsapp-button"><?php echo esc_html($button_text); ?></a>
        </div>


    <?php
    }

    // CSS
    ?>

<?php

    // Get buffered content and clean buffer
    $output = ob_get_clean();
    return wp_kses_post($output);
}