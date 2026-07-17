jQuery(function($) {
    // Update quantity in cart via AJAX
    $('body').on('change', '.woocommerce-cart-form .qty', function() {
        var $this = $(this);
        var cart_item_key = $this.closest('tr').data('cart_item_key');
        var quantity = $this.val();

        // Update cart with AJAX
        $.ajax({
            type: 'POST',
            url: wc_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'update_cart_action'),
            data: {
                action: 'update_cart_action',
                cart_item_key: cart_item_key,
                quantity: quantity,
                security: wc_cart_params.update_cart_nonce
            },
            success: function(response) {
                if (!response) return;

                // Replace cart contents with new data
                $('.woocommerce-cart-form').html($(response.data.cart_html).find('.woocommerce-cart-form').html());

                // Update fragments (cart totals, etc.)
                if (response.data.fragments) {
                    $.each(response.data.fragments, function(key, value) {
                        $(key).replaceWith(value);
                    });
                }
            }
        });
    });

    // Remove item from cart with AJAX
    $('body').on('click', '.woocommerce-cart-form .remove', function(e) {
        e.preventDefault();

        var $this = $(this);
        var cart_item_key = $this.closest('tr').data('cart_item_key');

        $.ajax({
            type: 'POST',
            url: wc_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_cart_item'),
            data: {
                action: 'remove_cart_item',
                cart_item_key: cart_item_key,
                security: wc_cart_params.remove_cart_item_nonce
            },
            success: function(response) {
                if (!response) return;

                // Replace cart contents with new data
                $('.woocommerce-cart-form').html($(response.data.cart_html).find('.woocommerce-cart-form').html());

                // Update fragments (cart totals, etc.)
                if (response.data.fragments) {
                    $.each(response.data.fragments, function(key, value) {
                        $(key).replaceWith(value);
                    });
                }
            }
        });
    });
});
