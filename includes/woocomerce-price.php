<?

add_filter('woocommerce_currency_symbol', 'custom_toman_symbol', 10, 2);
function custom_toman_symbol($currency_symbol, $currency) {
    if ($currency === 'IRT' || $currency === 'TOMAN') {  // بسته به کد ارزی که استفاده می‌کنی (معمولاً IRT برای تومان)
        $currency_symbol = 'تومانءءء';
    }
    return $currency_symbol;
}