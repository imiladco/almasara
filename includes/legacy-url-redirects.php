<?php
/**
 * 301 ریدایرکت لینک‌های قدیمی (قبل از تغییر ساختار سایت) به آدرس جدید.
 *
 * دو لایه‌ست:
 *
 * ۱) نقشهٔ صریح (one-off) برای صفحاتی که فقط خودشان تکی جابه‌جا/تغییر اسلاگ
 *    داده‌اند و قاعدهٔ کلی‌ای ندارند (مثلاً صفحات دانلود).
 *
 * ۲) ریدایرکت ساختاری (pattern-based) برای بخش‌هایی که کل الگوی پرمالینک‌شان
 *    عوض شده — وبلاگ (category/...) و فروشگاه (product/... و
 *    product-category/...). این بخش هیچ اسلاگی را دستی نگه نمی‌دارد؛ به‌جایش
 *    آبجکت (پست/محصول/دسته) را از روی اسلاگ قدیمی در دیتابیس پیدا می‌کند و به
 *    پرمالینک واقعی و فعلی‌اش (get_permalink / get_term_link) ریدایرکت می‌کند.
 *    یعنی همهٔ نوشته‌ها و محصولات قدیمی و جدید را، بدون افزودن دستی هر کدام،
 *    پوشش می‌دهد و اگر باز هم ساختار عوض شد نیازی به تغییر این فایل نیست.
 *
 * فقط روی درخواست‌های 404 اجرا می‌شود تا هیچ تداخلی با روتینگ عادی نداشته باشد.
 * ریدایرکت‌ها نسبت به home_url() ساخته می‌شوند، پس مستقل از دامنه (استیج/پروداکشن) کار می‌کنند.
 */

add_action( 'template_redirect', 'almasara_legacy_redirects', 0 );

function almasara_legacy_redirects(): void {
	if ( ! is_404() ) {
		return;
	}

	$request_uri = $_SERVER['REQUEST_URI'] ?? '';
	if ( '' === $request_uri ) {
		return;
	}

	$path_only = parse_url( $request_uri, PHP_URL_PATH );
	if ( ! $path_only ) {
		return;
	}

	// نرمال‌سازی: دیکد یونیکد + بدون اسلش ابتدا/انتها، برای مقایسهٔ ساده.
	$path = trim( rawurldecode( $path_only ), '/' );
	if ( '' === $path ) {
		return;
	}

	// -----------------------------------------------------------------
	// ۱) نقشهٔ صریح صفحات تکی (اسلاگ قدیم => مسیر جدید)
	// -----------------------------------------------------------------
	$explicit_map = array(
		'دانلود'                                   => '/downloads/',
		'دانلود-نرم-افزار-میلینگ-ماشین-2'          => '/downloads/milling-machine-software/',
		'دانلود-نرم-افزار-پرینتر-سه-بعدی'          => '/downloads/3d-printer-software/',
		'دانلود-نرم-افزارهای-پشتیبانی-کدکم'        => '/downloads/utility-software/',
		// توجه: صفحهٔ قدیمی «دانلود-نرم-افزار-اورال-اسکنر» در فایل ورودی بدون
		// مقصد بود؛ عمداً این‌جا نگاشت نشده تا خودش 404 عادی بماند. اگر مقصدش
		// مشخص شد همین‌جا یک سطر مثل بقیه اضافه کنید.
	);

	if ( isset( $explicit_map[ $path ] ) ) {
		almasara_redirect_to( $explicit_map[ $path ] );
	}

	// -----------------------------------------------------------------
	// ۲) ریدایرکت ساختاری
	// -----------------------------------------------------------------

	// ۲.الف) وبلاگ: /category/{دسته}/  یا  /category/{دسته}/{نوشته}/
	if ( preg_match( '#^category/([^/]+)(?:/([^/]+))?/?$#u', $path, $m ) ) {
		$post_slug = $m[2] ?? '';

		if ( '' !== $post_slug ) {
			$post = get_page_by_path( $post_slug, OBJECT, 'post' );
			if ( $post && 'publish' === $post->post_status ) {
				almasara_redirect_to( get_permalink( $post ) );
			}
		} else {
			$term = get_term_by( 'slug', $m[1], 'category' );
			if ( $term && ! is_wp_error( $term ) ) {
				almasara_redirect_to( get_term_link( $term ) );
			}
		}
	}

	// ۲.ب) دسته‌بندی محصولات: /product-category/{...اسلاگ‌های تودرتو...}/
	if ( preg_match( '#^product-category/(.+?)/?$#u', $path, $m ) ) {
		$segments  = explode( '/', $m[1] );
		$last_slug = end( $segments );

		$term = get_term_by( 'slug', $last_slug, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			almasara_redirect_to( get_term_link( $term ) );
		}
	}

	// ۲.ج) محصولات: /product/{اسلاگ}/  ->  پرمالینک فعلی همان محصول (زیر /shop/{دسته}/...)
	if ( preg_match( '#^product/([^/]+)/?$#u', $path, $m ) ) {
		$product = get_page_by_path( $m[1], OBJECT, 'product' );
		if ( $product && 'publish' === $product->post_status ) {
			almasara_redirect_to( get_permalink( $product ) );
		}
	}
}

/**
 * ریدایرکت 301 به مسیر/آدرس داده‌شده و خروج.
 *
 * @param string $target مسیر نسبی (با / شروع می‌شود) یا URL کامل.
 */
function almasara_redirect_to( string $target ): void {
	if ( 0 !== strpos( $target, 'http://' ) && 0 !== strpos( $target, 'https://' ) ) {
		$target = home_url( $target );
	}

	wp_safe_redirect( $target, 301 );
	exit;
}
