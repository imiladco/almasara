<?php
/**
 * Plugin Name: Almasara Theme Pin
 * Description: خواندن نام پوسته را در برابر خطای کش آبجکت هاست مقاوم می‌کند. کش ناپایدار گاهی گزینه template را خالی برمی‌گرداند و پوسته مادر (hello-elementor) هنگام بوت با مسیر themes// فیتال می‌دهد. این فایل مقدار درست را مستقیم برمی‌گرداند.
 *
 * نصب: این فایل باید داخل wp-content/mu-plugins/ باشد (اگر پوشه نبود، بسازش).
 * نکته: تا وقتی این فایل هست، پوسته فعال روی همین دو مقدار قفل است؛ برای
 * عوض‌کردن پوسته از پیشخوان، اول این فایل را موقتاً بردار.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('pre_option_template', static function ($pre) {
    return is_dir(WP_CONTENT_DIR . '/themes/hello-elementor') ? 'hello-elementor' : $pre;
});

add_filter('pre_option_stylesheet', static function ($pre) {
    return is_dir(WP_CONTENT_DIR . '/themes/almasara') ? 'almasara' : $pre;
});
