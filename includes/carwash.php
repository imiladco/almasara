<?php
/**
 * Car Wash Shortcode & AJAX Handlers
 * File: inc/carwash-form.php (در child theme)
 */

if (!defined('ABSPATH')) {
    exit;
}

// ════════════════════════════════════════════════════════════════
// SINGLE SOURCE OF TRUTH — تمام داده‌های ثابت فقط اینجا تعریف می‌شوند
// ════════════════════════════════════════════════════════════════

/**
 * تنظیمات کلی فرم
 * JS از طریق carwashConfig به این مقادیر دسترسی دارد
 */
function mdotcar_carwash_settings()
{
    return [
        'max_cars'  => 7,
        'labor_fee' => 100000,
    ];
}

/**
 * انواع خودرو — price فقط اینجا تعریف می‌شود
 */
function mdotcar_car_types()
{
    return [
        [
            'type'  => 'hatchback',
            'title' => 'هاچ بک (داخلی-خارجی)',
            'price' => 400000,
            'image' => '/wp-content/uploads/2026/02/atchback.svg',
        ],
        [
            'type'  => 'sedan',
            'title' => 'سدان',
            'price' => 500000,
            'image' => '/wp-content/uploads/2026/02/Container.svg',
        ],
        [
            'type'  => 'crossover',
            'title' => 'نیمه شاسی و کراس اور',
            'price' => 650000,
            'image' => '/wp-content/uploads/2026/02/crossover.svg',
        ],
        [
            'type'  => 'suv',
            'title' => 'شاسی بلند',
            'price' => 750000,
            'image' => '/wp-content/uploads/2026/02/suv.svg',
        ],
        [
            'type'  => 'van',
            'title' => 'ون',
            'price' => 1200000,
            'image' => '/wp-content/uploads/2026/02/van.png',
        ],
    ];
}

/**
 * خدمات تکمیلی — price فقط اینجا تعریف می‌شود
 */
function mdotcar_additional_services()
{
    return [
        ['value' => 'console', 'title' => 'واکس داشبورد و کنسول',  'price' => 70000,  'info' => 'شامل تمیزکاری و واکس کامل داشبورد و کنسول میانی'],
        ['value' => 'trunk',   'title' => 'نظافت صندوق',            'price' => 50000,  'info' => 'شستشو و نظافت کامل صندوق عقب خودرو'],
        ['value' => 'leather', 'title' => 'واکس مخصوص صندلی چرم',  'price' => 100000, 'info' => 'واکس و محافظت از صندلی‌های چرمی'],
        ['value' => 'tire',    'title' => 'واکس لاستیک',            'price' => 160000, 'info' => 'واکس و براق کردن لاستیک‌ها'],
        ['value' => 'door',    'title' => 'واکس رودری',             'price' => 80000,  'info' => 'تمیزکاری و واکس لاستیک‌های دور درب'],
        ['value' => 'wheels4', 'title' => 'شستشوی تکمیلی ۴ رینگ', 'price' => 150000, 'info' => 'شستشو و پولیش کامل ۴ رینگ خودرو'],
    ];
}

/**
 * گزینه‌های آب و برق
 */
function mdotcar_info_options()
{
    return [
        ['value' => 'true_true',  'title' => 'دسترسی به آب و برق وجود دارد',                   'label' => 'آب دارد / برق دارد'],
        ['value' => 'false_true', 'title' => 'دسترسی به آب وجود ندارد اما برق وجود دارد',      'label' => 'آب ندارد / برق دارد'],
        ['value' => 'false_false','title' => 'دسترسی به آب و برق وجود ندارد',                  'label' => 'آب ندارد / برق ندارد'],
        ['value' => 'true_false', 'title' => 'دسترسی به آب وجود دارد اما برق وجود ندارد',     'label' => 'آب دارد / برق ندارد'],
    ];
}

/**
 * سازمان‌ها — days, label, title فقط اینجا تعریف می‌شوند
 * days: روزهای مجاز هفته — 0=شنبه ... 6=جمعه (شمسی)
 */
function mdotcar_organizations()
{
    return [
        [
            'value' => 'ezam',
            'title' => 'گروه خودرویی عظام(منطقه ۲۲-اتوبان خرازی-پروژه هزارویک شهر)',
            'label' => 'گروه عظام',
            'days'  => [0, 2, 5],
        ],
        [
            'value' => 'laleh',
            'title' => 'بیمارستان لاله(شهرک غرب- خیابان سیمای ایران-نبش خیابان شجریان)',
            'label' => 'بیمارستان لاله',
            'days'  => [1, 3, 6],
        ],
        [
            'value' => 'day',
            'title' => 'بیمارستان دی(خیابان ولیعصر- خیابان توانیر- نبش خیابان عباسپور)',
            'label' => 'بیمارستان دی',
            'days'  => [1, 3, 6],
        ],
        [
            'value' => 'shahrak',
            'title' => 'شهرک آتی‌ساز',
            'label' => 'شهرک آتی‌ساز',
            'days'  => [0, 2, 5],
        ],
    ];
}

// ── Lookup helpers — بقیه توابع فقط از اینها می‌خوانند ──────────────────

/** @return array<string, string>  type => title */
function mdotcar_car_type_labels()
{
    return array_column(mdotcar_car_types(), 'title', 'type');
}

/** @return array<string, int>  type => price */
function mdotcar_car_type_prices()
{
    return array_column(mdotcar_car_types(), 'price', 'type');
}

/** @return array<string, string>  value => title */
function mdotcar_service_labels()
{
    return array_column(mdotcar_additional_services(), 'title', 'value');
}

/** @return array<string, int>  value => price */
function mdotcar_service_prices()
{
    return array_column(mdotcar_additional_services(), 'price', 'value');
}

/** @return array<string, string>  value => label (نمایش ادمین) */
function mdotcar_info_labels()
{
    return array_column(mdotcar_info_options(), 'label', 'value');
}

/** @return array<string, string>  value => label (نام کوتاه ادمین) */
function mdotcar_org_labels()
{
    return array_column(mdotcar_organizations(), 'label', 'value');
}

/** @return array<string, int[]>  value => days[] */
function mdotcar_org_days_map()
{
    $map = [];
    foreach (mdotcar_organizations() as $org) {
        $map[$org['value']] = $org['days'];
    }
    return $map;
}


// HELPER: inline SVG — fallback اگر تابع اصلی در فایل دیگری نباشد
if (!function_exists('mdotcar_inline_svg')) {
    function mdotcar_inline_svg($path)
    {
        $upload_dir = wp_upload_dir();
        $file_path  = $upload_dir['basedir'] . $path;
        if (file_exists($file_path)) {
            $svg = file_get_contents($file_path);
            if (preg_match('/<svg[\s\S]*<\/svg>/i', $svg, $matches)) {
                return $matches[0];
            }
        }
        return '<img src="' . esc_url($upload_dir['baseurl'] . $path) . '" alt="" loading="lazy">';
    }
}
// ════════════════════════════════════════════════════════════════
// SHORTCODE
// ════════════════════════════════════════════════════════════════

add_shortcode('mdotcar_carwash', 'mdotcar_carwash_from');

function mdotcar_carwash_from()
{
    $car_types            = mdotcar_car_types();
    $additional_services  = mdotcar_additional_services();
    $info_options         = mdotcar_info_options();
    $organization_options = mdotcar_organizations();
    $settings             = mdotcar_carwash_settings();

    ob_start();
?>
    <!-- کتابخانه‌ها -->
    <script src="/wp-content/themes/hello-theme-child-master/assets/js/cdn/vue.min.js"></script>
    <script src="/wp-content/themes/hello-theme-child-master/assets/js/cdn/moment.min.js"></script>
    <script src="/wp-content/themes/hello-theme-child-master/assets/js/cdn/jalaali.min.js"></script>
    <script src="/wp-content/themes/hello-theme-child-master/assets/js/cdn/moment-jalaali.js"></script>
    <script src="/wp-content/themes/hello-theme-child-master/assets/js/cdn/vue-persian-datetime-picker.umd.min.js"></script>

    <!-- Header -->
    <div class="carwash-header">
        <span>
            ثبت سرویس کارواش
            <strong>(فعال در تهران)</strong>
        </span>
        <button class="mdotcar-cancel">
            <?php echo mdotcar_inline_svg('/2025/04/close.svg'); ?>
        </button>
    </div>

    <!-- فرم اصلی -->
    <div class="mdotcar-form-container">

        <!-- نوار پیشرفت -->
        <div class="mdotcar-nav">
            <div>
                <span>انتخاب خودرو</span>
                <span>مرحله 1 از 5</span>
            </div>
            <div class="steps">
                <span class="mdotcar-nav-step" data-step="1"></span>
                <span class="mdotcar-nav-step" data-step="2"></span>
                <span class="mdotcar-nav-step" data-step="3"></span>
                <span class="mdotcar-nav-step" data-step="3-5"></span>
                <span class="mdotcar-nav-step" data-step="4"></span>
                <span class="mdotcar-nav-step" data-step="5"></span>
            </div>
            <div class="steps-completed"></div>
        </div>

        <!-- ═══════════════════════════════════════════════════ -->
        <!-- مرحله ۱: انتخاب خودرو                             -->
        <!-- ═══════════════════════════════════════════════════ -->
        <div class="mdotcar-step active" data-step="1">
            <h3>لطفا نوع و تعداد خودروهای خود را انتخاب کنید.</h3>
            <div class="mdotcar-car-items">
                <?php foreach ($car_types as $car) : ?>
                    <div class="mdotcar-car-item"
                         data-car-type="<?php echo esc_attr($car['type']); ?>"
                         data-car-price="<?php echo esc_attr($car['price']); ?>">
                        <div class="car-image">
                            <img src="<?php echo esc_url($car['image']); ?>"
                                 alt="<?php echo esc_attr($car['title']); ?>"
                                 loading="lazy">
                        </div>
                        <div class="car-details">
                            <div class="car-counter">
                                <button type="button" class="counter-btn counter-add" data-action="add">
                                    <img alt="add" src="/wp-content/uploads/2026/02/add-1.svg">
                                </button>
                                <div class="counter-controls" style="display: none;">
                                    <button type="button" class="counter-btn counter-plus" data-action="plus">
                                        <img alt="add" src="/wp-content/uploads/2026/02/add.svg">
                                    </button>
                                    <span class="counter-value">0</span>
                                    <button type="button" class="counter-btn counter-minus" data-action="minus">
                                        <img alt="minus" src="/wp-content/uploads/2026/02/trash.svg">
                                    </button>
                                </div>
                            </div>
                            <div class="car-info">
                                <span class="car-title"><?php echo esc_html($car['title']); ?></span>
                                <span class="car-price">
                                    <?php echo number_format($car['price']); ?>
                                    <strong>تومان</strong>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="car-count-error">
                <img src="/wp-content/uploads/2026/02/vuesax.svg" alt="error">
                امکان ثبت سرویس کارواش برای بیش از <?php echo esc_html($settings['max_cars']); ?> خودرو در یک روز امکان‌پذیر نمی‌باشد.
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════ -->
        <!-- مرحله ۲: خدمات تکمیلی                             -->
        <!-- ═══════════════════════════════════════════════════ -->
        <div class="mdotcar-step" data-step="2">
            <h3>لطفا نوع و تعداد خدمات تکمیلی خود را انتخاب کنید.</h3>
            <div class="mdotcar-service-items">
                <?php foreach ($additional_services as $service) : ?>
                    <div class="mdotcar-service-item"
                         data-service-type="<?php echo esc_attr($service['value']); ?>"
                         data-service-price="<?php echo esc_attr($service['price']); ?>">
                        <div class="service-info">
                            <div class="service-header">
                                <span class="service-title"><?php echo esc_html($service['title']); ?></span>
                                <?php if (!empty($service['info'])) : ?>
                                    <img alt="info"
                                         src="/wp-content/uploads/2026/02/info.svg"
                                         class="service-info-icon"
                                         title="<?php echo esc_attr($service['info']); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="service-footer">
                                <div class="service-counter">
                                    <button type="button" class="counter-btn counter-add" data-action="add">
                                        <img alt="add" src="/wp-content/uploads/2026/02/add-1.svg">
                                    </button>
                                    <div class="counter-controls" style="display: none;">
                                        <button type="button" class="counter-btn counter-plus" data-action="plus">
                                            <img alt="add" src="/wp-content/uploads/2026/02/add.svg">
                                        </button>
                                        <span class="counter-value">0</span>
                                        <button type="button" class="counter-btn counter-minus" data-action="minus">
                                            <img alt="minus" src="/wp-content/uploads/2026/02/trash.svg">
                                        </button>
                                    </div>
                                </div>
                                <span class="service-price">
                                    <?php echo number_format($service['price']); ?>
                                    <strong>تومان</strong>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════ -->
        <!-- مرحله ۳: اطلاعات تکمیلی (آب و برق)               -->
        <!-- ═══════════════════════════════════════════════════ -->
        <div class="mdotcar-step" data-step="3">
            <h3>لطفا اطلاعات تکمیلی خود را انتخاب کنید.</h3>
            <div class="mdotcar-service-items carwash-info-additional">
                <?php foreach ($info_options as $option) : ?>
                    <label>
                        <div>
                            <input type="radio"
                                   name="info_additional_type"
                                   value="<?php echo esc_attr($option['value']); ?>">
                            <span class="service-title"><?php echo esc_html($option['title']); ?></span>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════ -->
        <!-- مرحله ۳.۵: انتخاب سازمان                          -->
        <!-- ═══════════════════════════════════════════════════ -->
        <div class="mdotcar-step" data-step="step-3-5">
            <h3>لطفا سازمان خود را انتخاب کنید.</h3>
            <div class="mdotcar-service-items carwash-iorganization">
                <?php foreach ($organization_options as $option) : ?>
                    <label>
                        <div>
                            <input type="radio"
                                   name="organization_type"
                                   value="<?php echo esc_attr($option['value']); ?>"
                                   data-days="<?php echo esc_attr(implode(',', $option['days'])); ?>">
                            <span class="organization-title">
                                <?php echo esc_html($option['title']); ?>
                            </span>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
            <p class="organization-days-hint" style="display:none; margin-top:12px; color:#1a73e8; font-size:14px;"></p>
        </div>

        <!-- ═══════════════════════════════════════════════════ -->
        <!-- مرحله ۴: تاریخ و ساعت                             -->
        <!-- ═══════════════════════════════════════════════════ -->
        <div class="mdotcar-step" data-step="4">
            <h3>
                لطفا زمان انجام سرویس را انتخاب کنید.
                <?php echo mdotcar_inline_svg('/2025/04/info-circle.svg'); ?>
                <span class="notice-step-4">
                    پس از تکمیل درخواست، کارشناسان با شما تماس می‌گیرند و قیمت نهایی را اعلام خواهند کرد.
                </span>
            </h3>
            <div class="mdotcar-input-row">
                <div>
                    <label>تاریخ انجام سرویس</label>
                    <input type="text"
                           class="mdotcar-input"
                           placeholder="تاریخ انجام سرویس"
                           data-field="service_date"
                           readonly>
                </div>
                <div>
                    <label>ساعت انجام سرویس</label>
                    <input type="text"
                           class="mdotcar-input"
                           placeholder="ساعت انجام سرویس"
                           data-field="service_time"
                           readonly>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════ -->
        <!-- مرحله ۵: اطلاعات شخصی                             -->
        <!-- ═══════════════════════════════════════════════════ -->
        <div class="mdotcar-step" data-step="5">
            <h3>لطفا اطلاعات شخصی را تکمیل کنید.</h3>
            <div class="mdotcar-input-row">
                <div>
                    <label>نام و نام خانوادگی</label>
                    <input type="text"
                           class="mdotcar-input"
                           placeholder="نام و نام خانوادگی"
                           data-field="full_name">
                </div>
                <div>
                    <label>شماره تماس</label>
                    <input type="text"
                           class="mdotcar-input"
                           placeholder="09123456789"
                           data-field="phone"
                           maxlength="11">
                </div>
            </div>
            <div class="mdotcar-address-wrap">
                <label>آدرس</label>
                <input type="text"
                       class="mdotcar-input"
                       placeholder="آدرس کامل"
                       data-field="address">
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════ -->
        <!-- مرحله ۶: خلاصه سفارش                              -->
        <!-- ═══════════════════════════════════════════════════ -->
        <div class="mdotcar-step" data-step="6">
            <div class="mdotcar-periodic-service-request-summary">
                <div class="mdotcar-summary-Information">
                    <h3>جزئیات سفارش</h3>
                </div>
                <div class="selected-periodic-service"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mdotcar-footer">
            <div>
                <button class="mdotcar-prev step-1" disabled>
                    <img src="/wp-content/uploads/2025/04/arrow-left-2.svg" alt="prev">
                    مرحله قبل
                </button>
                <button class="mdotcar-next">
                    ادامه
                    <img src="/wp-content/uploads/2025/04/arrow-left-1.svg" alt="next">
                </button>
                <button class="mdotcar-submit" style="display: none;">
                    ثبت نهایی سرویس
                    <img src="/wp-content/uploads/2025/04/arrow-left-1.svg" alt="submit">
                </button>
            </div>
        </div>
    </div>

    <!-- مودال موفقیت -->
    <div class="carwash-successful-request" style="display: none;">
        <div>
            <?php echo mdotcar_inline_svg('/2026/01/successful.svg'); ?>
            <span class="successful-title">درخواست سرویس شما ثبت شد!</span>
            <span>به زودی پشتیبانی امداتکار با شما تماس خواهد گرفت.</span>
            <button class="close-success-modal">بستن</button>
        </div>
    </div>

    <!-- مودال ساعت -->
    <div class="hours-modal" style="display: none;">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="infon-modal">
                <span>انتخاب بازه زمانی</span>
                <img src="/wp-content/uploads/2025/09/close-square.svg" alt="close" class="close-modal">
            </div>
            <div class="Periodic-service-hours">
                <div class="mdotcar-time-item" data-value="08:00-12:00">۰۸:۰۰ الی ۱۲:۰۰</div>
                <div class="mdotcar-time-item selected" data-value="12:00-16:00">۱۲:۰۰ الی ۱۶:۰۰</div>
                <div class="mdotcar-time-item" data-value="16:00-20:00">۱۶:۰۰ الی ۲۰:۰۰</div>
            </div>
            <div class="modal-btn">
                <span class="cancel-time">انصراف</span>
                <span class="save-time">ذخیره</span>
            </div>
        </div>
    </div>

    <!-- مودال تقویم -->
    <div class="date-picker-modal" style="display: none;">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="infon-modal">
                <span>انتخاب تاریخ سرویس</span>
                <img src="/wp-content/uploads/2025/09/close-square.svg" alt="close" class="close-modal">
            </div>
            <div class="periodic-service-date-picker">
                <div id="date-picker-modal-app"></div>
            </div>
            <div class="modal-btn">
                <span class="cancel-date">انصراف</span>
                <span class="save-date">ذخیره</span>
            </div>
        </div>
    </div>

    <!-- پاس داده‌ها به JS — تمام داده‌ها از توابع بالا می‌آیند -->
    <script>
        window.carwashConfig = {
            settings:     <?php echo wp_json_encode($settings); ?>,
            carTypes:     <?php echo wp_json_encode($car_types); ?>,
            services:     <?php echo wp_json_encode($additional_services); ?>,
            infoOptions:  <?php echo wp_json_encode($info_options); ?>,
            organization: <?php echo wp_json_encode($organization_options); ?>
        };
    </script>

<?php
    return ob_get_clean();
}

// ════════════════════════════════════════════════════════════════
// AJAX: ثبت فرم
// ════════════════════════════════════════════════════════════════

add_action('wp_ajax_mdotcar_submit_carwash_form',        'mdotcar_submit_carwash_form');
add_action('wp_ajax_nopriv_mdotcar_submit_carwash_form', 'mdotcar_submit_carwash_form');

function mdotcar_submit_carwash_form()
{
    global $wpdb;

    if (!check_ajax_referer('carwash_admin_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'امنیت درخواست تأیید نشد']);
        return;
    }

    if (empty($_POST['data'])) {
        wp_send_json_error(['message' => 'داده‌ای ارسال نشده است']);
        return;
    }

    $data = json_decode(stripslashes($_POST['data']), true);

    if (!is_array($data)) {
        wp_send_json_error(['message' => 'فرمت داده نادرست است']);
        return;
    }

    // ── Validation فیلدهای اجباری ──────────────────────────────
    $required = ['full_name', 'selected_cars', 'phone', 'service_date', 'time_slot', 'info_additional_type'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            wp_send_json_error(['message' => "فیلد الزامی پر نشده: {$field}"]);
            return;
        }
    }

    if (!is_array($data['selected_cars']) || count($data['selected_cars']) === 0) {
        wp_send_json_error(['message' => 'حداقل یک خودرو باید انتخاب شود']);
        return;
    }

    // ── Validation تعداد کل خودروها ────────────────────────────
    $settings  = mdotcar_carwash_settings();
    $total_cars = array_sum(array_column($data['selected_cars'], 'count'));
    if ($total_cars > $settings['max_cars']) {
        wp_send_json_error(['message' => 'تعداد خودروها بیش از حد مجاز است']);
        return;
    }

    // ── Validation شماره تلفن ──────────────────────────────────
    $phone = sanitize_text_field($data['phone']);
    if (!preg_match('/^09\d{9}$/', $phone)) {
        wp_send_json_error(['message' => 'شماره موبایل معتبر نیست']);
        return;
    }

    // ── Validation server-side روزهای مجاز سازمان ─────────────
    $org_type = sanitize_text_field($data['organization_type'] ?? '');
    if ($org_type) {
        // مستقیم از SSOT می‌خواند — بدون تکرار
        $org_days_map = mdotcar_org_days_map();

        if (isset($org_days_map[$org_type]) && !empty($data['week_day'])) {
            $submitted_day = intval($data['week_day']);
            if (!in_array($submitted_day, $org_days_map[$org_type], true)) {
                wp_send_json_error(['message' => 'تاریخ انتخابی با روزهای مجاز سازمان مطابقت ندارد']);
                return;
            }
        }
    }

    // ── Validation قیمت‌ها (server-side price verification) ────
    $car_prices     = mdotcar_car_type_prices();
    $service_prices = mdotcar_service_prices();
    $calculated_total = $settings['labor_fee'];

    foreach ($data['selected_cars'] as $car) {
        $type  = sanitize_text_field($car['type'] ?? '');
        $count = intval($car['count'] ?? 0);
        if (!isset($car_prices[$type]) || $count < 1) {
            wp_send_json_error(['message' => 'نوع خودرو نامعتبر است']);
            return;
        }
        $calculated_total += $car_prices[$type] * $count;
    }

    foreach (($data['selected_services'] ?? []) as $svc) {
        $type  = sanitize_text_field($svc['type'] ?? '');
        $count = intval($svc['count'] ?? 0);
        if (!isset($service_prices[$type]) || $count < 1) {
            wp_send_json_error(['message' => 'نوع خدمت نامعتبر است']);
            return;
        }
        $calculated_total += $service_prices[$type] * $count;
    }

    // ── Insert ─────────────────────────────────────────────────
    $insert_data = [
        'request_date'    => current_time('mysql'),
        'phone'           => $phone,
        'full_name'       => sanitize_text_field($data['full_name']),
        'car_type'        => wp_json_encode($data['selected_cars']),
        'service_type'    => wp_json_encode($data['selected_services'] ?? []),
        'info_additional' => sanitize_text_field($data['info_additional_type']),
        'organization'    => $org_type,
        'service_date'    => sanitize_text_field($data['service_date']),
        'time_slot'       => sanitize_text_field($data['time_slot']),
        'address'         => sanitize_text_field($data['address'] ?? ''),
        'total_price'     => $calculated_total,
        'service_status'  => 'pending',
    ];

    $result = $wpdb->insert(
        $wpdb->prefix . 'carwash_requests',
        $insert_data,
        array_fill(0, count($insert_data), '%s')
    );

    if ($result === false) {
        error_log('MDotCar DB Error: ' . $wpdb->last_error);
        wp_send_json_error(['message' => 'خطا در ثبت اطلاعات']);
        return;
    }

    // ── SMS ────────────────────────────────────────────────────
    $full_name    = $insert_data['full_name'];
    $service_type = 'سرویس کارواش';

    if ($full_name && preg_match('/^09\d{9}$/', $phone)) {
        send_carwash_sms('mdotcar-periodic-client', 'عزیز', $full_name, $service_type, $phone);
    }

    $admin_numbers = ['09197562014', '09123259128', '09199786303', '09035329300', '09010742414'];
    foreach ($admin_numbers as $admin_number) {
        send_carwash_sms('mdotcar-periodic-admin', $insert_data['service_date'], 'متنوع', $service_type, $admin_number);
    }

    wp_send_json_success(['message' => 'درخواست با موفقیت ثبت شد']);
}

// ════════════════════════════════════════════════════════════════
// AJAX: انصراف
// ════════════════════════════════════════════════════════════════

add_action('wp_ajax_mdotcar_cancel_carwash_form',        'mdotcar_cancel_carwash_form');
add_action('wp_ajax_nopriv_mdotcar_cancel_carwash_form', 'mdotcar_cancel_carwash_form');

function mdotcar_cancel_carwash_form()
{
    global $wpdb;

    if (!check_ajax_referer('carwash_admin_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'امنیت درخواست تأیید نشد']);
        return;
    }

    $data = json_decode(stripslashes($_POST['data'] ?? '{}'), true);
    if (!is_array($data)) {
        $data = [];
    }

    $insert_data = [
        'request_date'    => current_time('mysql'),
        'cancel_date'     => current_time('mysql'),
        'phone'           => sanitize_text_field($data['phone']                ?? ''),
        'full_name'       => sanitize_text_field($data['full_name']            ?? ''),
        'car_type'        => wp_json_encode($data['selected_cars']             ?? []),
        'service_type'    => wp_json_encode($data['selected_services']         ?? []),
        'info_additional' => sanitize_text_field($data['info_additional_type'] ?? ''),
        'organization'    => sanitize_text_field($data['organization_type']    ?? ''),
        'service_date'    => sanitize_text_field($data['service_date']         ?? ''),
        'time_slot'       => sanitize_text_field($data['time_slot']            ?? ''),
        'address'         => sanitize_text_field($data['address']              ?? ''),
        'cancel_reason'   => '',
    ];

    $result = $wpdb->insert(
        $wpdb->prefix . 'carwash_cancellations',
        $insert_data,
        array_fill(0, count($insert_data), '%s')
    );

    if ($result === false) {
        error_log('MDotCar Cancel DB Error: ' . $wpdb->last_error);
        wp_send_json_error(['message' => 'خطا در ثبت انصراف']);
        return;
    }

    wp_send_json_success(['message' => 'انصراف ثبت شد']);
}

// ════════════════════════════════════════════════════════════════
// AJAX: به‌روزرسانی وضعیت (ادمین)
// ════════════════════════════════════════════════════════════════

add_action('wp_ajax_mdotcar_update_call_center_status_carwash', 'mdotcar_update_call_center_status_carwash');

function mdotcar_update_call_center_status_carwash()
{
    global $wpdb;

    if (!check_ajax_referer('carwash_admin_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'امنیت درخواست تأیید نشد']);
        return;
    }

    if (empty($_POST['order_id']) || empty($_POST['status']) || empty($_POST['source'])) {
        wp_send_json_error(['message' => 'داده‌های ناقص']);
        return;
    }

    $order_id = intval($_POST['order_id']);
    $status   = sanitize_text_field($_POST['status']);
    $reason   = sanitize_text_field($_POST['reason'] ?? '');
    $source   = sanitize_text_field($_POST['source']);

    $allowed_statuses = ['pending', 'completed', 'canceled_after_call'];
    if (!in_array($status, $allowed_statuses, true)) {
        wp_send_json_error(['message' => 'وضعیت نامعتبر']);
        return;
    }

    $allowed_sources = ['requests', 'cancellations'];
    if (!in_array($source, $allowed_sources, true)) {
        wp_send_json_error(['message' => 'منبع نامعتبر']);
        return;
    }

    $table = $source === 'requests'
        ? $wpdb->prefix . 'carwash_requests'
        : $wpdb->prefix . 'carwash_cancellations';

    $order = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $order_id)
    );

    if (!$order) {
        wp_send_json_error(['message' => 'سفارش یافت نشد']);
        return;
    }

    if ($status === 'canceled_after_call' && $source === 'requests') {

        $insert = [
            'request_date'    => $order->request_date,
            'cancel_date'     => current_time('mysql'),
            'phone'           => $order->phone,
            'full_name'       => $order->full_name,
            'car_type'        => $order->car_type,
            'service_type'    => $order->service_type,
            'info_additional' => $order->info_additional,
            'organization'    => $order->organization,
            'service_date'    => $order->service_date,
            'time_slot'       => $order->time_slot,
            'address'         => $order->address,
            'cancel_reason'   => $reason,
        ];

        $ins = $wpdb->insert(
            $wpdb->prefix . 'carwash_cancellations',
            $insert,
            array_fill(0, count($insert), '%s')
        );

        if ($ins === false) {
            wp_send_json_error(['message' => 'خطا در انتقال به کنسل‌شده‌ها']);
            return;
        }

        $del = $wpdb->delete($wpdb->prefix . 'carwash_requests', ['id' => $order_id], ['%d']);

        if ($del === false) {
            wp_send_json_error(['message' => 'خطا در حذف سفارش']);
            return;
        }

        wp_send_json_success(['message' => 'سفارش به کنسل‌شده‌ها منتقل شد']);

    } else {

        $upd = $wpdb->update($table, ['service_status' => $status], ['id' => $order_id], ['%s'], ['%d']);

        if ($upd === false) {
            wp_send_json_error(['message' => 'خطا در به‌روزرسانی وضعیت']);
            return;
        }

        wp_send_json_success(['message' => 'وضعیت با موفقیت به‌روزرسانی شد']);
    }
}

// ════════════════════════════════════════════════════════════════
// SMS
// ════════════════════════════════════════════════════════════════

function send_carwash_sms( $template,  $token,  $token10,  $token20,  $receptor)
{
    // کلید API از wp-config.php خوانده می‌شود:
    // define('KAVENEGAR_API_KEY', 'your_key_here');
    $api_key = defined('KAVENEGAR_API_KEY') ? KAVENEGAR_API_KEY : '';

    if (empty($api_key)) {
        error_log('MDotCar SMS: KAVENEGAR_API_KEY تعریف نشده است');
        return false;
    }

    $url = "https://api.kavenegar.com/v1/{$api_key}/verify/lookup.json"
         . '?template=' . rawurlencode($template)
         . '&token='    . rawurlencode($token)
         . '&token10='  . rawurlencode($token10)
         . '&token20='  . rawurlencode($token20)
         . '&receptor=' . rawurlencode($receptor);

    $response = wp_remote_get($url, ['timeout' => 10]);

    if (is_wp_error($response)) {
        error_log('SMS Error: ' . $response->get_error_message());
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return isset($body['return']['status']) && $body['return']['status'] === 200;
}

// ════════════════════════════════════════════════════════════════
// ساخت جداول — با version check
// ════════════════════════════════════════════════════════════════

function mdotcar_carwash_tables()
{
    $current_version = get_option('mdotcar_carwash_db_version', '0');
    $target_version  = '1.2'; // افزایش نسخه = اجرای مجدد dbDelta

    if (version_compare($current_version, $target_version, '>=')) {
        return;
    }

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql_requests = "CREATE TABLE {$wpdb->prefix}carwash_requests (
        id               BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        request_date     DATETIME     NOT NULL,
        phone            VARCHAR(20)  NOT NULL,
        full_name        VARCHAR(255) NOT NULL,
        car_type         TEXT         NOT NULL,
        service_type     TEXT         NOT NULL,
        info_additional  VARCHAR(50)  NOT NULL,
        organization     VARCHAR(50)  NOT NULL DEFAULT '',
        service_date     VARCHAR(50)  NOT NULL,
        time_slot        VARCHAR(50)  NOT NULL,
        address          TEXT,
        total_price      BIGINT(20)   NOT NULL DEFAULT 0,
        service_status   VARCHAR(20)  NOT NULL DEFAULT 'pending',
        PRIMARY KEY (id)
    ) {$charset_collate};";

    $sql_cancellations = "CREATE TABLE {$wpdb->prefix}carwash_cancellations (
        id               BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        request_date     DATETIME     NOT NULL,
        cancel_date      DATETIME     NOT NULL,
        phone            VARCHAR(20)  NOT NULL,
        full_name        VARCHAR(255) NOT NULL,
        car_type         TEXT         NOT NULL,
        service_type     TEXT         NOT NULL,
        info_additional  VARCHAR(50)  NOT NULL,
        organization     VARCHAR(50)  NOT NULL DEFAULT '',
        service_date     VARCHAR(50)  NOT NULL,
        time_slot        VARCHAR(50)  NOT NULL,
        address          TEXT,
        cancel_reason    TEXT,
        PRIMARY KEY (id)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_requests);
    dbDelta($sql_cancellations);

    update_option('mdotcar_carwash_db_version', $target_version);
}
// اجرا در هر بارگذاری (dbDelta خودش چک می‌کند)
add_action('init', 'mdotcar_carwash_tables');

// ════════════════════════════════════════════════════════════════
// پنل ادمین
// ════════════════════════════════════════════════════════════════

function carwash_register_admin_menu()
{
    add_menu_page(
        'کارواش',
        'کارواش',
        'manage_options',
        'carwash-orders',
        'mdotcar_carwash_orders_page',
        'dashicons-car',
        7
    );
}
add_action('admin_menu', 'carwash_register_admin_menu');

function mdotcar_carwash_orders_page()
{
    global $wpdb;

    // whitelist صریح برای $tab
    $allowed_tabs = ['pending', 'canceled', 'successful'];
    $tab = isset($_GET['tab']) && in_array($_GET['tab'], $allowed_tabs, true)
        ? $_GET['tab']
        : 'pending';

    // ── همه labels از SSOT می‌آیند ─────────────────────────────
    $car_type_names = mdotcar_car_type_labels();
    $service_names  = mdotcar_service_labels();
    $info_labels    = mdotcar_info_labels();
    $org_labels     = mdotcar_org_labels();

    // ── Helper: نمایش خودروها از JSON ──────────────────────────
    $render_cars = static function ($car_type_json) use ($car_type_names) {
        $cars = json_decode($car_type_json, true);
        if (!is_array($cars)) return esc_html($car_type_json);
        $lines = [];
        foreach ($cars as $car) {
            $name    = $car_type_names[$car['type'] ?? ''] ?? ($car['type'] ?? '');
            $count   = intval($car['count'] ?? 1);
            $lines[] = esc_html($name) . ' <small>(×' . $count . ')</small>';
        }
        return implode('<br>', $lines);
    };

    // ── Helper: نمایش خدمات از JSON ────────────────────────────
    $render_services = static function ($service_type_json) use ($service_names) {
        $raw = json_decode($service_type_json, true);
        if (!is_array($raw)) return '';
        $parts = [];
        foreach ($raw as $s) {
            if (is_array($s)) {
                $key     = $s['type'] ?? $s['value'] ?? '';
                $count   = intval($s['count'] ?? 1);
                $name    = $service_names[$key] ?? $key;
                $parts[] = $name . ($count > 1 ? " (×{$count})" : '');
            } else {
                $parts[] = $service_names[(string)$s] ?? (string)$s;
            }
        }
        return esc_html(implode('، ', array_filter($parts)));
    };

    // ── Query با prepare ───────────────────────────────────────
    if ($tab === 'canceled') {
        $orders = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}carwash_cancellations ORDER BY cancel_date DESC")
        );
    } elseif ($tab === 'successful') {
        $orders = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}carwash_requests WHERE service_status = %s ORDER BY request_date DESC",
                'completed'
            )
        );
    } else {
        $orders = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}carwash_requests WHERE service_status = %s ORDER BY request_date DESC",
                'pending'
            )
        );
    }
?>
    <div class="wrap">
        <h1>درخواست‌های کارواش</h1>
        <nav class="nav-tab-wrapper">
            <a href="?page=carwash-orders"
               class="nav-tab <?php echo $tab === 'pending'     ? 'nav-tab-active' : ''; ?>">جدید</a>
            <a href="?page=carwash-orders&tab=canceled"
               class="nav-tab <?php echo $tab === 'canceled'    ? 'nav-tab-active' : ''; ?>">کنسل شده</a>
            <a href="?page=carwash-orders&tab=successful"
               class="nav-tab <?php echo $tab === 'successful'  ? 'nav-tab-active' : ''; ?>">تایید شده</a>
        </nav>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>تاریخ ثبت</th>
                    <?php if ($tab === 'canceled') : ?><th>تاریخ لغو</th><?php endif; ?>
                    <th>مشخصات</th>
                    <th>سازمان</th>
                    <th>خودروها</th>
                    <th>خدمات</th>
                    <th>آب و برق</th>
                    <th>زمان سرویس</th>
                    <th>آدرس</th>
                    <?php if ($tab === 'canceled') : ?><th>علت کنسلی</th><?php endif; ?>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)) : ?>
                    <tr><td colspan="12">درخواستی یافت نشد.</td></tr>
                <?php else : ?>
                    <?php foreach ($orders as $order) : ?>
                        <tr>
                            <td><?php echo esc_html($order->id); ?></td>
                            <td><?php echo esc_html($order->request_date); ?></td>
                            <?php if ($tab === 'canceled') : ?>
                                <td><?php echo esc_html($order->cancel_date); ?></td>
                            <?php endif; ?>
                            <td>
                                <?php echo esc_html($order->full_name); ?><br>
                                <small><?php echo esc_html($order->phone); ?></small>
                            </td>
                            <td><?php echo esc_html($org_labels[$order->organization] ?? $order->organization ?: '—'); ?></td>
                            <td><?php echo wp_kses($render_cars($order->car_type), ['br' => [], 'small' => []]); ?></td>
                            <td><?php echo $render_services($order->service_type); ?></td>
                            <td><?php echo esc_html($info_labels[$order->info_additional] ?? $order->info_additional); ?></td>
                            <td>
                                <?php echo esc_html($order->service_date); ?><br>
                                <small><?php echo esc_html($order->time_slot); ?></small>
                            </td>
                            <td><?php echo esc_html($order->address); ?></td>
                            <?php if ($tab === 'canceled') : ?>
                                <td><?php echo esc_html($order->cancel_reason ?: '—'); ?></td>
                            <?php endif; ?>
                            <td>
                                <button class="button update-call-center-status"
                                        data-order-id="<?php echo esc_attr($order->id); ?>"
                                        data-source="<?php echo $tab === 'canceled' ? 'cancellations' : 'requests'; ?>">
                                    به‌روزرسانی وضعیت
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php
}