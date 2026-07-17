<?php
/**
 * WooCommerce Custom Cart Shortcode
 *
 * Displays cart items with product details, variations, sale badge, and quantity controls.
 * Supports accurate stock management, AJAX updates/removal, and Persian variation encoding fixes.
 * Single-language support (Persian only).
 *
 * @package ChildTheme
 */

/**
 * Register custom cart wrapper shortcode
 *
 * @return string HTML output of cart wrapper
 */
function custom_cart_wrapper_shortcode() {
    if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
        return '<p>سبد خرید خالی است.</p>';
    }

    ob_start();
    $cart_count = WC()->cart->get_cart_contents_count();
    $cart_total = WC()->cart->get_cart_subtotal(); // Use get_cart_subtotal for numeric value
    ?>
    <div class="cart-wrapper invisible opacity-0 flex flex-col rounded-md bg-general-01 absolute" style="z-index:100000;box-shadow: 0 0 0px #0000, 0 0 0px #0000, 0px 6px 60px 0px rgba(0, 0, 0, .2);left:0;margin-top:20px;width:360px" id="sub-menu-fragment">
        <div class="cart-header flex flex-row justify-between items-center gap-8 px-6 py-4 text-body-2 font-medium" style="background-color: rgb(248, 250, 251);border-top-left-radius: 8px;border-top-right-radius: 8px; height: fit-content;">
            <span class="flex flex-row gap-8 items-center" style="color: rgb(119, 119, 119);">
                <img src="/wp-content/themes/almasara/assets/img/menu/profile/cart-count.svg" alt="cart-count">
                سبد خرید شما <?php echo esc_html( $cart_count ); ?> عدد کالا
                </span>
            <a href="/cart/" class="font-normal flex flex-row items-center py-05 px-1 rounded-md" style="color: #0077db;">
                سبد خرید
                <img src="/wp-content/themes/almasara/assets/img/menu/profile/arrow-left.svg" alt="show-cart">
            </a>
        </div>
        <div class="cart-items px-8 py-6" style="max-height: 400px; overflow: hidden; overflow-y: auto; scrollbar-width: thin; direction: ltr;">
            <?php echo do_shortcode( '[custom_cart]' ); ?>
        </div>
        <div class="cart-footer border-t-1 border-solid border-general-07 px-6 py-4">
            <a href="/checkout/" class="cart-checkout flex flex-row gap-32 justify-between px-8 py-4 text-body-2 font-medium general-01 rounded-lg" style="background-color: rgb(34, 60, 120)">
                <span class="checkout-button font-bold">ثبت سفارش</span>
                <hr class="divider bg-general-01 m-unset rounded-3xl" style="min-height:100%;width:1px" aria-hidden="true">
                <div class="cart-total flex flex-row gap-4 items-center hidden">
                    <p class="font-normal text-caption">جمع کل:</p>
                    <div class="total-price flex flex-row gap-4">
                        <span class="cart-total-price"><?php echo esc_html( $cart_total ); ?></span>
                        <p class="font-normal text-caption">تومان</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'custom_cart_wrapper', 'custom_cart_wrapper_shortcode' );

/**
 * Register custom cart shortcode
 *
 * @return string HTML output of cart items
 */
function custom_cart_shortcode() {
    if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart || WC()->cart->is_empty() ) {
        return '<p>سبد خرید خالی است.</p>';
    }

    ob_start();
    $cart = WC()->cart->get_cart();
    $index = 0;
    $total_items = count( $cart );

    foreach ( $cart as $cart_item_key => $cart_item ) {
        $product = $cart_item['data'];
        if ( ! $product instanceof WC_Product ) {
            continue;
        }

        $quantity = absint( $cart_item['quantity'] );
        $is_on_sale = $product->is_on_sale();
        $variations = $cart_item['variation'] ?? [];
        $stock_quantity = $product->managing_stock() ? max( 0, $product->get_stock_quantity() ) : 100;
        $is_max_stock = $quantity >= $stock_quantity || ! $product->is_in_stock();
        ?>
        <div class="cart-item flex flex-col gap-24" data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>" data-stock-quantity="<?php echo esc_attr( $stock_quantity ); ?>">
            <div class="cart-details flex flex-row gap-16">
                <div class="cart-info flex flex-col gap-8">
                    <h2 class="text-body-2 font-bold" style="line-height: 18px;"><?php echo esc_html( $product->get_name() ); ?></h2>
                    <?php if ( ! empty( $variations ) ) { ?>
                        <ul class="variations font-normal flex flex-col gap-4 m-unset p-unset">
                            <?php
                            foreach ( $variations as $key => $value ) {
                                $attribute_key = str_replace( 'attribute_', '', $key );
                                $attribute_label = wc_attribute_label( $attribute_key );
                                $attribute_value = $value;
                                ?>
                                <li class="flex flex-row gap-16 items-center" style="color: #4B5259;">
                                    <img decoding="async" src="/wp-content/themes/almasara/assets/img/menu/fragment/attribute.svg" alt="آیکون متغیر">
                                    <?php echo esc_html( rawurldecode( $attribute_label . ': ' . $attribute_value ) ); ?>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
                <div class="cart-image flex flex-col gap-8">
                    <?php
                    $image_id = $product->get_image_id();
                    if ( $image_id ) {
                        echo wp_get_attachment_image( $image_id, 'thumbnail', false, [ 'class' => 'rounded-md', 'style' => 'width: 114px; height: 114px;' ] );
                    } else {
                        echo '<img src="/wp-content/themes/almasara/assets/img/placeholder.png" class="rounded-md" style="width: 114px; height: 114px;" alt="بدون تصویر">';
                    }
                    ?>
                    <?php if ( $is_on_sale ) { ?>
                        <p class="sale font-normal text-caption" style="color: #E6123D;">فروش ویژه</p>
                    <?php } ?>
                </div>
            </div>
            <div class="cart-price flex flex-row gap-16 justify-between">
                <div class="quantity-control flex flex-row items-center gap-18">
                    <?php if ( $quantity === 1 ) { ?>
                        <button class="qty-remove flex items-center justify-center p-16 rounded-md bg-general-01" tabindex="-1" aria-label="حذف محصول" style="width: 40px; height: 40px; box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .1);">
                            <img src="/wp-content/themes/almasara/assets/img/menu/fragment/trash.svg" alt="trash">
                        </button>
                    <?php } else { ?>
                        <button class="qty-decrease flex items-center justify-center p-16 rounded-md bg-general-01" style="width: 40px; height: 40px; box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .1);" tabindex="-1" aria-label="کاهش تعداد">-</button>
                    <?php } ?>
                    <span class="qty-display flex flex-col text-2xl font-bold items-center" style="line-height: 32px;">
                        <?php if ( $is_max_stock ) { ?>
                            <span class="max-label text-caption font-medium" style="line-height: 18px;">حداکثر</span>
                        <?php } ?>
                        <?php echo esc_html( $quantity ); ?>
                    </span>
                    <button class="qty-increase <?php echo $is_max_stock ? 'disabled-max' : ''; ?> flex items-center justify-center p-16 rounded-md bg-general-01" style="width: 40px; height: 40px; box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .1);" tabindex="-1" aria-label="افزایش تعداد" <?php echo $is_max_stock ? 'disabled' : ''; ?>>+</button>
                </div>
                <div class="price flex items-center flex-row gap-4 font-semibold text-h6" style="color: rgb(35, 37, 78);">
                    <span><?php echo esc_html( $product->get_price() * $quantity ); ?></span>
                    <p class="font-normal text-caption">تومان</p>
                </div>
            </div>
        </div>
        <?php if ( ++$index < $total_items ) { ?>
            <hr class="divider" style="min-width:100%;height:1px;background:#0000001f; margin-block: 20px;" aria-hidden="true">
        <?php } ?>
        <?php
    }

    return ob_get_clean();
}
add_shortcode( 'custom_cart', 'custom_cart_shortcode' );

/**
 * AJAX handler for updating cart item quantity
 */
function custom_cart_update_qty() {
    check_ajax_referer( 'cart_nonce', 'nonce' );

    $cart_item_key = sanitize_text_field( wp_unslash( $_POST['key'] ?? '' ) );
    $quantity = absint( $_POST['qty'] ?? 0 );

    if ( $quantity < 1 || ! WC()->cart->find_product_in_cart( $cart_item_key ) ) {
        wp_send_json_error( [
            'message' => 'خطا در به‌روزرسانی تعداد.',
        ], 400 );
    }

    $cart_item = WC()->cart->get_cart()[ $cart_item_key ] ?? null;
    if ( ! $cart_item || ! $cart_item['data'] instanceof WC_Product ) {
        wp_send_json_error( [
            'message' => 'محصول یافت نشد.',
        ], 404 );
    }

    $product = $cart_item['data'];
    $stock_quantity = $product->managing_stock() ? max( 0, $product->get_stock_quantity() ) : 100;

    // Check stock limit
    if ( $quantity > $stock_quantity || ! $product->is_in_stock() ) {
        wp_send_json_error( [
            'message' => sprintf( 'حداکثر تعداد موجود %d است.', $stock_quantity ),
        ], 400 );
    }

    // Limit maximum quantity to 100
    if ( $quantity > 100 ) {
        wp_send_json_error( [
            'message' => 'حداکثر تعداد مجاز ۱۰۰ است.',
        ], 400 );
    }

    WC()->cart->set_quantity( $cart_item_key, $quantity );

    wp_send_json_success( [
        'price' => floatval( $product->get_price() * $quantity ),
        'quantity' => $quantity,
        'is_remove' => false,
        'is_max_stock' => $quantity >= $stock_quantity || ! $product->is_in_stock(),
        'stock_quantity' => $stock_quantity,
        'cart_total' => floatval( WC()->cart->get_cart_subtotal() ),
        'cart_count' => WC()->cart->get_cart_contents_count(),
    ] );
}
add_action( 'wp_ajax_custom_cart_update_qty', 'custom_cart_update_qty' );
add_action( 'wp_ajax_nopriv_custom_cart_update_qty', 'custom_cart_update_qty' );

/**
 * AJAX handler for removing cart item
 */
function custom_cart_remove_item() {
    check_ajax_referer( 'cart_nonce', 'nonce' );

    $cart_item_key = sanitize_text_field( wp_unslash( $_POST['key'] ?? '' ) );

    if ( ! WC()->cart->find_product_in_cart( $cart_item_key ) ) {
        wp_send_json_error( [
            'message' => 'محصول یافت نشد.',
        ], 404 );
    }

    $cart_item = WC()->cart->get_cart()[ $cart_item_key ] ?? null;
    if ( $cart_item && $cart_item['data'] instanceof WC_Product ) {
        $product = $cart_item['data'];
        $stock_quantity = $product->managing_stock() ? max( 0, $product->get_stock_quantity() ) : 100;
        WC()->cart->remove_cart_item( $cart_item_key );

        wp_send_json_success( [
            'message' => 'محصول با موفقیت حذف شد.',
            'is_remove' => true,
            'stock_quantity' => $stock_quantity,
            'cart_total' => floatval( WC()->cart->get_cart_subtotal() ),
            'cart_count' => WC()->cart->get_cart_contents_count(),
        ] );
    }

    wp_send_json_error( [
        'message' => 'محصول یافت نشد.',
    ], 404 );
}
add_action( 'wp_ajax_custom_cart_remove_item', 'custom_cart_remove_item' );
add_action( 'wp_ajax_nopriv_custom_cart_remove_item', 'custom_cart_remove_item' );

/**
 * Filter to decode Persian variation attributes
 */
function custom_decode_variation_attributes( $attributes, $product ) {
    if ( ! empty( $attributes ) ) {
        foreach ( $attributes as $key => $value ) {
            $attributes[ $key ] = rawurldecode( $value );
        }
    }
    return $attributes;
}
add_filter( 'woocommerce_product_variation_get_attributes', 'custom_decode_variation_attributes', 10, 2 );

/**
 * Disable WooCommerce cart fragments during custom AJAX requests
 */
function disable_cart_fragments_during_custom_ajax() {
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ( isset( $_POST['action'] ) && in_array( $_POST['action'], [ 'custom_cart_update_qty', 'custom_cart_remove_item' ] ) ) ) {
        remove_action( 'wp_ajax_woocommerce_get_refreshed_fragments', 'woocommerce_get_refreshed_fragments' );
        remove_action( 'wp_ajax_nopriv_woocommerce_get_refreshed_fragments', 'woocommerce_get_refreshed_fragments' );
    }
}
add_action( 'init', 'disable_cart_fragments_during_custom_ajax', 5 );
?>