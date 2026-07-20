<?php
/**
 * Mini-cart fragments for the header dropdown (#sub-menu-fragment).
 *
 * The dropdown is server-rendered once via [custom_cart_wrapper]; without
 * fragments it never reflects AJAX cart changes. This registers the three
 * inner blocks (header count / items / footer total) as selector => HTML
 * pairs on BOTH channels:
 *   - woocommerce_add_to_cart_fragments: core wc-ajax add_to_cart (archive
 *     buttons) and wc-cart-fragments refresh/sessionStorage replay.
 *   - amfc_fragments: Almasara Fast Cart plugin endpoints (widget add/update).
 *
 * Only inner blocks are replaced — never #sub-menu-fragment itself, because
 * header.js binds hover listeners to that element at DOMContentLoaded and
 * replacing it would orphan them.
 *
 * @package ChildTheme
 */

/**
 * Build selector => HTML pairs for the mini-cart dropdown.
 *
 * @param array $fragments Existing fragments.
 * @return array
 */
function almasara_mini_cart_fragments( $fragments ) {
    if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart || ! function_exists( 'almasara_cart_fragment_items' ) ) {
        return $fragments;
    }

    $fragments['#sub-menu-fragment .cart-header'] = almasara_cart_fragment_header();
    $fragments['#sub-menu-fragment .cart-items']  = almasara_cart_fragment_items();
    $fragments['#sub-menu-fragment .cart-footer'] = almasara_cart_fragment_footer();

    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'almasara_mini_cart_fragments' );
add_filter( 'amfc_fragments', 'almasara_mini_cart_fragments' );
