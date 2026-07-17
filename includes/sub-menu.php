<?php


function sub_menu_almasara() {
	ob_start();
	$cats = [
		99 => ['/wp-content/uploads/2025/03/ابزار.jpg', 'tools'],
		105 => ['/wp-content/uploads/2025/03/ارتودنسی.jpg', 'orthodontics'],
		106 => ['/wp-content/uploads/2025/03/ضد-عفونی.jpg', 'sterilization & disinfectants'],
		337 => ['/wp-content/uploads/2025/03/کودک.jpg', 'children'],
		102 => ['/wp-content/uploads/2025/03/اندو.jpg', 'endo'],
		97 => ['/wp-content/uploads/2025/03/ایمپلنت.jpg', 'implant & surgery'],
		107 => ['/wp-content/uploads/2025/03/بهداشت.jpg', 'hygiene'],
		101 => ['/wp-content/uploads/2025/03/بی‌حسی.jpg', 'ptc'],
		104 => ['/wp-content/uploads/2025/03/پروتز.jpg', 'prosthesis'],
		129 => ['/wp-content/uploads/2025/03/پالیش-و-پرداخت-2.jpg', 'polishing & finishing'],
		100 => ['/wp-content/uploads/2025/03/تجهیزات-و-ملزومات-جانبی.jpg', 'equipment & accessories'],
		103 => ['/wp-content/uploads/2025/03/ترمیم-و-زیبایی.jpg', 'restoration & beauty'],
		128 => ['/wp-content/uploads/2025/03/فرز-دندانپزشکی.jpg', 'dental burs'],
		277 => ['/wp-content/uploads/2025/03/روکش-و-بریج.jpg', 'crown & bridge'],
		98 => ['/wp-content/uploads/2025/03/سرساکشن-یک-بار-مصرف.jpg', 'consumables & disposables'],
		176 => ['/wp-content/uploads/2025/03/قالب.jpg', 'materials & impression materials'],
		71 => ['/wp-content/uploads/2025/03/سایر.jpg', ''],
	];

	$items = [
		[
			'id' => '0',
			'url' => '/shop/',
			'title' => 'مشاهده همه اقلام',
			'img' => '/wp-content/uploads/2025/03/همه.jpg',
			'span' => '',
		]
	];

	foreach ($cats as $cat_id => $cat_data) {
		$term = get_term_by('id', $cat_id, 'product_cat');
		if (! $term || is_wp_error($term)) {
			continue;
		}

		$items[] = [
			'id' => 'cat-' . $cat_id,
			'url' => get_term_link($term, 'product_cat'),
			'img' => $cat_data[0] ?? '',
			'title' => $term->name,
			'span' => $cat_data[1] ?? '',
		];
	}

	$items = array_slice($items, 0, 19);
	$chunks = array_chunk($items, 6);

echo '<div class="dks-sub-menu invisible opacity-0 grid grid-cols-3 gap-12 bg-general-01 p-16 rounded-xl absolute" style="box-shadow: 0 0 #0000, 0 0 #0000, 0 1px 3px 0 #0000001a, 0 1px 2px -1px #0000001a !important; z-index:10000;right: 0; margin-top: 16px; width: 65%;" id="sub-menu-category">';

foreach ($chunks as $chunk) {
    echo '<div class="dks-menu-column flex flex-col gap-12">';
    foreach ($chunk as $item) {
        echo '<a href="' . esc_url($item['url']) . '" id="' . esc_attr($item['id']) . '">';
        echo '<div class="p-8 rounded-lg flex flex-row gap-10 items-center justify-between" style="background: #ecedff; transition-property: color, background-color, border-color, text-decoration-color, fill, stroke !important; transition-timing-function: cubic-bezier(.4,0,.2,1) !important; transition-duration: .15s !important;">';

        echo '<div class="rounded-lg flex flex-row gap-10 items-center">';
        echo '<img class="rounded-full object-cover" src="' . esc_url($item['img']) . '" alt="' . esc_attr($item['title']) . '" width="34" height="34">';
        echo '<h4 class="font-medium text-caption webkit-box line-clamp-1">' . esc_html($item['title']) . '</h4>';
        echo '</div>';
        echo '<span class="webkit-box line-clamp-1 font-normal text-left" style="width: 30%; font-size: 10px; direction: ltr;">' . esc_html($item['span']) . '</span>';
        echo '</div>'; // بسته شدن dks-menu-item
        echo '</a>';
    }
    echo '</div>'; // بسته شدن dks-menu-column
}

echo '</div>'; // بسته شدن dks-sub-menu

	return ob_get_clean();
}

add_shortcode('sub_menu_almasara', 'sub_menu_almasara');