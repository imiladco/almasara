<?

add_shortcode('product_gallery_modal', 'pgm_display_product_gallery');

function pgm_display_product_gallery()
{
    global $post;

    if (!is_singular('product')) {
        return;
    }

    $product = wc_get_product($post->ID);

    if (!$product) {
        return;
    }

    $attachment_ids = $product->get_gallery_image_ids();

    if (!$attachment_ids) {
        return;
    }

    ob_start();
?>
    <div class="product-gallery">
        <div class="main-image">
            <?php echo wp_get_attachment_image($product->get_image_id(), 'large'); ?>
        </div>
        <div class="gallery-thumbnails">
            <?php
            $counter = 1;
            foreach ($attachment_ids as $attachment_id) {
                if ($counter > 5) break;
                echo '
        <div class="thumbnail thumb-' . $counter . '" data-img-id="' . $attachment_id . '">';
                echo wp_get_attachment_image($attachment_id, 'thumbnail');
                echo '
        </div>
        ';
                $counter++;
            }
            ?>
        </div>
    </div>

    <div class="Modal-Overlay" style="display: none;">
        <div class="Modal-header">
            <div class="text-white-GalleryHeader">
                <span>رسمی</span>
                <span>تصاویر محصول</span>
            </div>
            <div class="modal-close"><img src="/wp-content/uploads/2024/06/🦆-icon-_cancel_.svg" alt="Close"></div>
        </div>
        <div class="modal-body">
            <div class="modal-slider">
                <?php
                foreach ($attachment_ids as $attachment_id) {
                    echo '<img class="modal-image" src="' . wp_get_attachment_image_url($attachment_id, 'large') . '" data-img-id="' . $attachment_id . '" style="display: none;">
                ';
                }
                ?>
                <button class="prev-btn">«</button>
                <button class="next-btn">»</button>
            </div>
        </div>
        <div class="modal-footer">
            <div class="all-img">
                <img src="/wp-content/uploads/2024/06/category.svg">
                <span>همه تصاویر</span>
            </div>
            <div class="footer-thumb-box">
                <?php
                foreach ($attachment_ids as $index => $attachment_id) {
                    $classes = 'footer-thumb';
                    if ($index == 0) {
                        $classes .= ' start';
                    }
                    if ($index == count($attachment_ids) - 1) {
                        $classes .= ' end';
                    }
                    echo '<img class="' . $classes . '"
                           src="' . wp_get_attachment_image_url($attachment_id, 'thumbnail') . '"
                           data-img-id="' . $attachment_id . '" alt="Thumb">';
                }
                ?>
            </div>
        </div>
    </div>
    <style>

    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalOverlay = document.querySelector('.Modal-Overlay');
            const modalImages = document.querySelectorAll('.modal-image');
            const thumbnails = document.querySelectorAll('.gallery-thumbnails .thumbnail');
            const footerThumbs = document.querySelectorAll('.Modal-footer .footer-thumb');
            let currentImageIndex = 0;

            function updateActiveClasses(index) {
                // به روز رسانی کلاس active برای modal-image
                modalImages.forEach((img, i) => {
                    img.style.display = (i === index) ? 'block' : 'none';
                    img.classList.toggle('active', i === index);
                });

                // پیدا کردن id تصویر فعال
                const activeImgId = modalImages[index].dataset.imgId;

                // به روز رسانی کلاس active برای footer-thumb
                footerThumbs.forEach(thumb => {
                    thumb.classList.toggle('active', thumb.dataset.imgId === activeImgId);
                });
            }

            // نمایش تصویر اولیه و کلاس فعال
            updateActiveClasses(currentImageIndex);

            thumbnails.forEach((thumb, index) => {
                thumb.addEventListener('click', () => {
                    currentImageIndex = index;
                    updateActiveClasses(currentImageIndex);
                    modalOverlay.style.display = 'flex';
                });
            });

            document.querySelector('.modal-close').addEventListener('click', () => {
                modalOverlay.style.display = 'none';
            });

            document.querySelector('.prev-btn').addEventListener('click', () => {
                currentImageIndex = (currentImageIndex > 0) ? currentImageIndex - 1 : modalImages.length - 1;
                updateActiveClasses(currentImageIndex);
            });

            document.querySelector('.next-btn').addEventListener('click', () => {
                currentImageIndex = (currentImageIndex < modalImages.length - 1) ? currentImageIndex + 1 : 0;
                updateActiveClasses(currentImageIndex);
            });

            footerThumbs.forEach((thumb, index) => {
                thumb.addEventListener('click', () => {
                    currentImageIndex = index;
                    updateActiveClasses(currentImageIndex);
                });
            });
        });
        document.addEventListener('DOMContentLoaded', () => {
            // گرفتن دکمه‌های prev و next
            const prevButton = document.querySelector('.prev-btn');
            const nextButton = document.querySelector('.next-btn');

            // تابع برای همگام‌سازی کلاس active
            const syncActiveClass = () => {
                // گرفتن تصویر فعالی که کلاس active دارد
                const activeModalImage = document.querySelector('.modal-image.active');

                if (activeModalImage) {
                    // گرفتن آیدی تصویر فعال
                    const activeImgId = activeModalImage.getAttribute('data-img-id');

                    // حذف کلاس active از تمامی تصاویر پیش‌نمایش
                    const footerThumbs = document.querySelectorAll('.footer-thumb');
                    footerThumbs.forEach(thumb => thumb.classList.remove('active'));

                    // پیدا کردن و اضافه کردن کلاس active به تصویر پیش‌نمایش مربوطه
                    const activeFooterThumb = document.querySelector(`.footer-thumb[data-img-id="${activeImgId}"]`);
                    if (activeFooterThumb) {
                        activeFooterThumb.classList.add('active');
                    }
                }
            };

            // اضافه کردن event listener به دکمه‌های prev و next
            if (prevButton) {
                prevButton.addEventListener('click', syncActiveClass);
            }
            if (nextButton) {
                nextButton.addEventListener('click', syncActiveClass);
            }

            // فراخوانی اولیه یک‌باره برای همگام‌سازی کلاس active
            syncActiveClass();
        });
    </script>
<?php
    return ob_get_clean();
}





add_shortcode('product_gallery_modal2', 'pgm_display_product_gallery2');

function pgm_display_product_gallery2()
{
    global $post;

    if (!is_singular('product')) {
        return;
    }

    $product = wc_get_product($post->ID);

    if (!$product) {
        return;
    }

    $attachment_ids = $product->get_gallery_image_ids();
    $main_image_id = $product->get_image_id();
    $has_gallery = !empty($attachment_ids);

    ob_start();
?>
    <div class="product-gallery">
        <div class="main-image">
            <?php echo wp_get_attachment_image($main_image_id, 'large'); ?>
        </div>
        <?php if ($has_gallery): ?>
            <div class="gallery-thumbnails">
                <?php
                $counter = 1;
                foreach ($attachment_ids as $attachment_id) {
                    if ($counter > 5) break;
                    echo '<div class="thumbnail thumb-' . esc_attr($counter) . '" data-img-id="' . esc_attr($attachment_id) . '">';
                    echo wp_get_attachment_image($attachment_id, 'thumbnail');
                    echo '</div>';
                    $counter++;
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($has_gallery): ?>
        <div class="Modal-Overlay" style="display: none;">
            <div class="Modal-header">
                <div class="text-white-GalleryHeader">رسمی</div>
                <div class="modal-close">X</div>
            </div>
            <div class="modal-body">
                <div class="modal-slider">
                    <?php
                    foreach ($attachment_ids as $attachment_id) {
                        echo '<img class="modal-image" src="' . esc_url(wp_get_attachment_image_url($attachment_id, 'large')) . '" data-img-id="' . esc_attr($attachment_id) . '" style="display: none;">';
                    }
                    ?>
                    <button class="prev-btn">«</button>
                    <button class="next-btn">»</button>
                </div>
            </div>
            <div class="modal-footer">
                <?php
                foreach ($attachment_ids as $index => $attachment_id) {
                    $classes = 'footer-thumb';
                    if ($index == 0) {
                        $classes .= ' start';
                    }
                    if ($index == count($attachment_ids) - 1) {
                        $classes .= ' end';
                    }
                    echo '<img class="' . esc_attr($classes) . '" src="' . esc_url(wp_get_attachment_image_url($attachment_id, 'thumbnail')) . '" data-img-id="' . esc_attr($attachment_id) . '" alt="Thumb">';
                }
                ?>
            </div>
        </div>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalOverlay = document.querySelector('.Modal-Overlay');
            const modalImages = document.querySelectorAll('.modal-image');
            const thumbnails = document.querySelectorAll('.gallery-thumbnails .thumbnail');
            const footerThumbs = document.querySelectorAll('.modal-footer .footer-thumb');
            let currentImageIndex = 0;

            const showModalImage = (index) => {
                modalImages.forEach((img, i) => {
                    img.style.display = (i === index) ? 'block' : 'none';
                });
            }

            thumbnails.forEach((thumb, index) => {
                thumb.addEventListener('click', () => {
                    currentImageIndex = index;
                    showModalImage(currentImageIndex);
                    modalOverlay.style.display = 'flex';
                });
            });

            document.querySelector('.modal-close').addEventListener('click', () => {
                modalOverlay.style.display = 'none';
            });

            document.querySelector('.prev-btn').addEventListener('click', () => {
                currentImageIndex = (currentImageIndex > 0) ? currentImageIndex - 1 : modalImages.length - 1;
                showModalImage(currentImageIndex);
            });

            document.querySelector('.next-btn').addEventListener('click', () => {
                currentImageIndex = (currentImageIndex < modalImages.length - 1) ? currentImageIndex + 1 : 0;
                showModalImage(currentImageIndex);
            });

            footerThumbs.forEach((thumb, index) => {
                thumb.addEventListener('click', () => {
                    currentImageIndex = index;
                    showModalImage(currentImageIndex);
                });
            });
        });
    </script>
<?php
    return ob_get_clean();
}

