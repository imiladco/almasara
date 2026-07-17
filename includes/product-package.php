<?

function add_package_contents_metabox()
{
    add_meta_box(
        'package_contents_metabox',
        'محتویات بسته',
        'render_package_contents_metabox',
        'product',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_package_contents_metabox');

// رندر کردن متاباکس
function render_package_contents_metabox($post)
{
    // دریافت داده‌های فعلی
    $package_contents = get_post_meta($post->ID, '_package_contents', true);
    if (!is_array($package_contents)) {
        $package_contents = [];
    }

    // اضافه کردن nonce برای امنیت
    wp_nonce_field('package_contents_nonce_action', 'package_contents_nonce');
?>
    <div class="package-contents-metabox">
        <div id="package-items">
            <?php foreach ($package_contents as $index => $item) : ?>
                <div class="package-item">
                    <input type="text" name="package_contents[<?php echo $index; ?>]" value="<?php echo esc_attr($item); ?>" placeholder="نام آیتم (مثلاً: شان جراحی)" />
                    <button type="button" class="button remove-item">حذف</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-package-item" class="button">اضافه کردن آیتم جدید</button>
    </div>

    <style>
        .package-contents-metabox {
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
        }

        .package-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .package-item input[type="text"] {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .package-item .remove-item {
            background: #d63638;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .package-item .remove-item:hover {
            background: #b32d2e;
        }

        #add-package-item {
            background: #2ea2cc;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        #add-package-item:hover {
            background: #1b7aa6;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            // اضافه کردن آیتم جدید
            $('#add-package-item').on('click', function() {
                var index = $('#package-items .package-item').length;
                var newItem = '<div class="package-item">' +
                    '<input type="text" name="package_contents[' + index + ']" placeholder="نام آیتم (مثلاً: شان جراحی)" />' +
                    '<button type="button" class="button remove-item">حذف</button>' +
                    '</div>';
                $('#package-items').append(newItem);
            });

            // حذف آیتم
            $('#package-items').on('click', '.remove-item', function() {
                $(this).closest('.package-item').remove();
            });
        });
    </script>
    <?php
}

// ذخیره داده‌های متاباکس
function save_package_contents_metabox($post_id)
{
    // چک کردن nonce برای امنیت
    if (!isset($_POST['package_contents_nonce']) || !wp_verify_nonce($_POST['package_contents_nonce'], 'package_contents_nonce_action')) {
        return;
    }

    // چک کردن دسترسی کاربر
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // چک کردن اینکه درخواست از autosave نباشه
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // ذخیره داده‌ها
    if (isset($_POST['package_contents']) && is_array($_POST['package_contents'])) {
        $package_contents = array_map('sanitize_text_field', $_POST['package_contents']);
        // حذف موارد خالی
        $package_contents = array_filter($package_contents, function ($item) {
            return !empty(trim($item));
        });
        // بازنشانی کلیدهای آرایه
        $package_contents = array_values($package_contents);
        update_post_meta($post_id, '_package_contents', $package_contents);
    } else {
        delete_post_meta($post_id, '_package_contents');
    }
}
add_action('save_post', 'save_package_contents_metabox');