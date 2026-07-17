<?


function custom_product_meta_box()
{
    add_meta_box(
        'custom_product_meta',    // ID متاباکس
        'اطلاعات اضافی محصول',     // عنوان متاباکس
        'custom_product_meta_box_callback',  // تابع کال‌بک برای نمایش فیلدها
        'product',                 // نوع پست (در اینجا محصول ووکامرس)
        'normal',                  // محل نمایش
        'high'                     // اولویت نمایش
    );
}
add_action('add_meta_boxes', 'custom_product_meta_box');

// تابع کال‌بک برای نمایش فیلدهای متاباکس
function custom_product_meta_box_callback($post)
{
    // بازیابی اطلاعات متاباکس (در صورت وجود)
    $expiry_date = get_post_meta($post->ID, '_expiry_date', true);
    $pack_quantity = get_post_meta($post->ID, '_pack_quantity', true);
    $country_of_origin = get_post_meta($post->ID, '_country_of_origin', true);

    // نمایش فیلدهای متاباکس
?>
    <p>
        <label for="expiry_date">تاریخ انقضا:</label>
        <input type="text" id="expiry_date" name="expiry_date" value="<?php echo esc_attr($expiry_date); ?>" />
    </p>
    <p>
        <label for="pack_quantity">تعداد در بسته:</label>
        <input type="number" id="pack_quantity" name="pack_quantity" value="<?php echo esc_attr($pack_quantity); ?>" />
    </p>
    <p>
        <label for="country_of_origin">کشور سازنده:</label>
        <input type="text" id="country_of_origin" name="country_of_origin" value="<?php echo esc_attr($country_of_origin); ?>" />
    </p>
<?php
}

// ذخیره‌سازی اطلاعات متاباکس
function save_custom_product_meta($post_id)
{
    // بررسی عدم ذخیره خودکار و عدم داشتن دسترسی مناسب
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // بررسی اینکه کاربر مجوز ویرایش پست را دارد یا خیر
    if (isset($_POST['post_type']) && 'product' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // ذخیره اطلاعات متاباکس
    if (isset($_POST['expiry_date'])) {
        update_post_meta($post_id, '_expiry_date', sanitize_text_field($_POST['expiry_date']));
    }

    if (isset($_POST['pack_quantity'])) {
        update_post_meta($post_id, '_pack_quantity', intval($_POST['pack_quantity']));
    }

    if (isset($_POST['country_of_origin'])) {
        update_post_meta($post_id, '_country_of_origin', sanitize_text_field($_POST['country_of_origin']));
    }
}
add_action('save_post', 'save_custom_product_meta');

function display_product_meta_shortcode($atts)
{
    global $post;

    ob_start();
    // دریافت متاها
    $expiry_date = get_post_meta($post->ID, '_expiry_date', true);
    $pack_quantity = get_post_meta($post->ID, '_pack_quantity', true);
    $country_of_origin = get_post_meta($post->ID, '_country_of_origin', true);
    $sku = get_post_meta($post->ID, '_sku', true);
    $stock_quantity = get_post_meta($post->ID, '_stock', true);
    $package_contents = get_post_meta($post->ID, '_package_contents', true);

    // چک کردن اینکه آیا همه داده‌ها (متاها و محتویات بسته) خالی هستن
    $has_meta = !empty($sku) || !empty($country_of_origin) || !empty($expiry_date) || !empty($pack_quantity) || !empty($stock_quantity);
    $has_package_contents = !empty($package_contents) && is_array($package_contents);
    if (!$has_meta && !$has_package_contents) {
        return '';
    }
?>

    <span class="Feature">ویژگی</span>
    <?php if ($has_package_contents) : ?>
        <div class="prd_meta_item">
            <p class="title">محتویات بسته</p>
            <p class="description">
                <?php
                $item_count = count($package_contents);
                $use_separator = $item_count > 2;
                $index = 0;
                foreach ($package_contents as $item) : ?>
                    <span class="package-item"><?php echo esc_html($item); ?></span><?php
                                                                                    if ($use_separator && $index < $item_count - 1) {
                                                                                        echo ', ';
                                                                                    }
                                                                                    $index++;
                                                                                endforeach; ?>
            </p>
        </div>
    <?php endif; ?>
    <div class="product_meta_container">
        <?php if (!empty($sku)) : ?>
            <div class="prd_meta_item">
                <p class="title">شناسه محصول</p>
                <p class="description"><?php echo esc_html($sku); ?></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($country_of_origin)) : ?>
            <div class="prd_meta_item">
                <p class="title">کشور سازنده</p>
                <p class="description"><?php echo esc_html($country_of_origin); ?></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($expiry_date)) : ?>
            <div class="prd_meta_item">
                <p class="title">تاریخ انقضا</p>
                <p class="description"><?php echo esc_html($expiry_date); ?></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($pack_quantity)) : ?>
            <div class="prd_meta_item">
                <p class="title">تعداد در بسته</p>
                <p class="description"><?php echo esc_html($pack_quantity); ?> عدد</p>
            </div>
        <?php endif; ?>
        <?php if (!empty($stock_quantity)) : ?>
            <div class="prd_meta_item">
                <p class="title">موجودی انبار</p>
                <p class="description"><?php echo esc_html($stock_quantity); ?> عدد</p>
            </div>
        <?php endif; ?>
    </div>
    <span class="information"><a href="">مشاهده سایر اطلاعات محصول</a></span>

<?php
    return ob_get_clean();
}

add_shortcode('product_meta', 'display_product_meta_shortcode');