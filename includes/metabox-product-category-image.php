<?



// افزودن متاباکس به صفحه ویرایش دسته‌بندی محصولات
function add_product_cat_image_field()
{
    ?>
    <div class="form-field">
        <label for="product_cat_image"><?php _e('نماد دسته بندی', 'textdomain'); ?></label>
        <input type="button" id="upload_product_cat_image_button" class="button" value="<?php _e('آپلود تصویر', 'textdomain'); ?>" />
        <input type="hidden" id="product_cat_image" name="product_cat_image" value="" />
        <div id="product_cat_image_preview" style="margin-top: 10px;">
            <!-- پیش نمایش تصویر در اینجا نمایش داده می‌شود -->
        </div>
    </div>
    <script>
        jQuery(document).ready(function($) {
            var frame;
            $('#upload_product_cat_image_button').on('click', function(e) {
                e.preventDefault();
                if (frame) {
                    frame.open();
                    return;
                }
                frame = wp.media({
                    title: '<?php _e("انتخاب یا آپلود تصویر", "textdomain"); ?>',
                    button: {
                        text: '<?php _e("استفاده به عنوان نماد", "textdomain"); ?>'
                    },
                    multiple: false
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#product_cat_image').val(attachment.id);
                    $('#product_cat_image_preview').html('<img src="' + attachment.url + '" style="max-width:100%;" />');
                });
                frame.open();
            });
        });
    </script>
<?php
}
add_action('product_cat_add_form_fields', 'add_product_cat_image_field', 10, 2);

// ویرایش دسته‌بندی‌ها
function edit_product_cat_image_field($term)
{
    $image_id = get_term_meta($term->term_id, 'product_cat_image', true);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="product_cat_image"><?php _e('نماد دسته بندی', 'textdomain'); ?></label>
        </th>
        <td>
            <input type="button" id="upload_product_cat_image_button" class="button" value="<?php _e('آپلود تصویر', 'textdomain'); ?>" />
            <input type="hidden" id="product_cat_image" name="product_cat_image" value="<?php echo esc_attr($image_id); ?>" />
            <div id="product_cat_image_preview" style="margin-top: 10px;">
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" style="max-width:100%;" />
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <script>
        jQuery(document).ready(function($) {
            var frame;
            $('#upload_product_cat_image_button').on('click', function(e) {
                e.preventDefault();
                if (frame) {
                    frame.open();
                    return;
                }
                frame = wp.media({
                    title: '<?php _e("انتخاب یا آپلود تصویر", "textdomain"); ?>',
                    button: {
                        text: '<?php _e("استفاده به عنوان نماد", "textdomain"); ?>'
                    },
                    multiple: false
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#product_cat_image').val(attachment.id);
                    $('#product_cat_image_preview').html('<img src="' + attachment.url + '" style="max-width:100%;" />');
                });
                frame.open();
            });
        });
    </script>
<?php
}
add_action('product_cat_edit_form_fields', 'edit_product_cat_image_field', 10, 2);
function save_product_cat_image_field($term_id)
{
    if (isset($_POST['product_cat_image'])) {
        update_term_meta($term_id, 'product_cat_image', absint($_POST['product_cat_image']));
    }
}
add_action('created_product_cat', 'save_product_cat_image_field', 10, 2);
add_action('edited_product_cat', 'save_product_cat_image_field', 10, 2);
// نمایش تصویر نماد دسته‌بندی در جدول دسته‌بندی‌ها
function add_product_cat_image_column($columns)
{
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key == 'name') {
            $new_columns['product_cat_image'] = __('نماد', 'textdomain');
        }
    }
    return $new_columns;
}
add_filter('manage_edit-product_cat_columns', 'add_product_cat_image_column');

function display_product_cat_image_column($content, $column_name, $term_id)
{
    if ($column_name == 'product_cat_image') {
        $image_id = get_term_meta($term_id, 'product_cat_image', true);
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
        if ($image_url) {
            $content = '<img src="' . esc_url($image_url) . '" style="max-width:100%;" />';
        }
    }
    return $content;
}
add_filter('manage_product_cat_custom_column', 'display_product_cat_image_column', 10, 3);