/**
 * Cart quantity update script
 * Handles increase/decrease/remove buttons for cart items with click locking and stock management
 */
jQuery(function($) {
    let isProcessing = false; // Lock to prevent multiple simultaneous clicks

    // Handle quantity increase/decrease
    $('.qty-decrease, .qty-increase').on('click', function(e) {
        e.preventDefault(); // Prevent any keyboard or form-related behavior
        const $button = $(this);

        // Ignore clicks if button is disabled
        if ($button.prop('disabled') || $button.hasClass('disabled-max')) {
            return;
        }

        if (isProcessing) {
            return; // Ignore clicks while processing
        }

        isProcessing = true; // Set lock

        const $container = $button.closest('.quantity-control');
        const $display = $container.find('.qty-display');
        const $price = $container.closest('.cart-price').find('.price span');
        const $item = $container.closest('.cart-item');
        const cartKey = $item.data('cart-key');
        const stockQuantity = parseInt($item.data('stock-quantity')) || 100;
        let qty = parseInt($display.text()) || 1;
        const originalQty = qty; // Store original quantity for revert

        // Check stock limit in client-side for increase
        if ($button.hasClass('qty-increase') && qty >= stockQuantity) {
            $button.addClass('disabled-max').prop('disabled', true);
            $display.html(qty + '<span class="max-label text-caption font-medium" style="line-height: 18px;">حداکثر</span>');
            isProcessing = false;
            return;
        }

        // Disable all quantity buttons across all items
        $('.qty-decrease, .qty-increase, .qty-remove').prop('disabled', true);

        // Update quantity
        if ($button.hasClass('qty-increase')) {
            qty++;
        } else {
            qty = qty > 1 ? qty - 1 : 1; // Decrease by 1, ensure not less than 1
        }

        // Update display immediately
        $display.text(qty);

        // AJAX request
        $.ajax({
            url: cartAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'custom_cart_update_qty',
                nonce: cartAjax.nonce,
                key: cartKey,
                qty: qty
            },
            success: function(response) {
                if (response.success) {
                    $display.html((response.data.is_max_stock ? '<span class="max-label text-caption font-medium" style="line-height: 18px;">حداکثر</span>' : '') + response.data.quantity);
                    $price.text(parseFloat(response.data.price));
                    $('.cart-total-price').text(parseFloat(response.data.cart_total)); // Update total price
                    $('.cart-header span').text('سبد خرید شما ' + response.data.cart_count + ' عدد کالا'); // Update cart count
                    // Update decrease/remove button
                    if (response.data.quantity === 1) {
                        $container.find('.qty-decrease').replaceWith('<button class="qty-remove flex items-center justify-center p-16 rounded-md bg-general-01" tabindex="-1" aria-label="حذف محصول" style="width: 40px; height: 40px; box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .1);"><img src="/wp-content/themes/dks/assets/img/menu/fragment/trash.svg" alt="trash"></button>');
                    } else {
                        $container.find('.qty-remove').replaceWith('<button class="qty-decrease flex items-center justify-center p-16 rounded-md bg-general-01" style="width: 40px; height: 40px; box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .1);" tabindex="-1" aria-label="کاهش تعداد">-</button>');
                    }
                    // Update increase button based on stock
                    const $increase = $container.find('.qty-increase');
                    if (response.data.is_max_stock) {
                        $increase.addClass('disabled-max').prop('disabled', true);
                    } else {
                        $increase.removeClass('disabled-max').prop('disabled', false);
                    }
                    $item.data('stock-quantity', response.data.stock_quantity);
                } else {
                    alert(response.data.message);
                    $display.text(originalQty); // Revert on error
                }
            },
            error: function() {
                alert('خطا در ارتباط با سرور.');
                $display.text(originalQty); // Revert on error
            },
            complete: function() {
                // Re-enable all quantity buttons
                $('.qty-decrease, .qty-increase, .qty-remove').prop('disabled', false);
                isProcessing = false; // Release lock
            }
        });
    });

    // Handle item removal
    $(document).on('click', '.qty-remove', function(e) {
        e.preventDefault(); // Prevent any keyboard or form-related behavior
        if (isProcessing) {
            return; // Ignore clicks while processing
        }

        isProcessing = true; // Set lock

        const $button = $(this);
        const $container = $button.closest('.quantity-control');
        const $item = $container.closest('.cart-item');
        const cartKey = $item.data('cart-key');

        // Disable all quantity buttons across all items
        $('.qty-decrease, .qty-increase, .qty-remove').prop('disabled', true);

        // AJAX request to remove item
        $.ajax({
            url: cartAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'custom_cart_remove_item',
                nonce: cartAjax.nonce,
                key: cartKey
            },
            success: function(response) {
                if (response.success) {
                    $item.remove(); // Remove item from DOM
                    // Update divider visibility
                    $('.cart-item').each(function(index, element) {
                        const $nextHr = $(element).next('hr.divider');
                        if (index === $('.cart-item').length - 1) {
                            $nextHr.remove(); // Remove last divider
                        } else {
                            $nextHr.show(); // Ensure others are visible
                        }
                    });
                    if ($('.cart-item').length === 0) {
                        $('.cart-item').parent().html('<p>سبد خرید خالی است.</p>');
                    }
                    $('.cart-total-price').text(parseFloat(response.data.cart_total)); // Update total price
                    $('.cart-header span').text('سبد خرید شما ' + response.data.cart_count + ' عدد کالا'); // Update cart count
                    // Re-check stock for remaining items
                    $('.cart-item').each(function() {
                        const $item = $(this);
                        const key = $item.data('cart-key');
                        const $display = $item.find('.qty-display');
                        const qty = parseInt($display.text()) || 1;
                        $.ajax({
                            url: cartAjax.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'custom_cart_update_qty',
                                nonce: cartAjax.nonce,
                                key: key,
                                qty: qty
                            },
                            success: function(res) {
                                if (res.success) {
                                    $display.html((res.data.is_max_stock ? '<span class="max-label text-caption font-medium" style="line-height: 18px;">حداکثر</span>' : '') + res.data.quantity);
                                    const $increase = $item.find('.qty-increase');
                                    if (res.data.is_max_stock) {
                                        $increase.addClass('disabled-max').prop('disabled', true);
                                    } else {
                                        $increase.removeClass('disabled-max').prop('disabled', false);
                                    }
                                    $item.data('stock-quantity', res.data.stock_quantity);
                                }
                            }
                        });
                    });
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('خطا در ارتباط با سرور.');
            },
            complete: function() {
                // Re-enable all quantity buttons
                $('.qty-decrease, .qty-increase, .qty-remove').prop('disabled', false);
                isProcessing = false; // Release lock
            }
        });
    });
});