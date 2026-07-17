<footer role="contentinfo" class="flex flex-col items-center">
    <div class="footer-container flex flex-col gap-46">
        <div class="flex flex-row justify-between items-end">
            <section class="flex flex-col gap-36">
                <!-- logo -->
                <a href="/" class="logo">
                    <img src="<? echo wp_get_attachment_image_url(get_theme_mod('custom_logo')) ?>" alt="almasara"
                        class="w-full" style="height:48px">
                </a>
                <!-- brand info -->
                <address class="flex flex-row gap-20">
                    <a class="flex flex-row gap-16 items-center" href="tel:+989125953809">
                        <img alt="phone" src="<?php echo get_theme_file_uri('/assets/img/footer/phone.svg'); ?>"
                            aria-label="شماره تماس">
                        09125953809
                    </a>
                    <a class="flex flex-row gap-16 items-center" aria-label="آدرس">
                        <img alt="aderess" src="<?php echo get_theme_file_uri('/assets/img/footer/location.svg'); ?>">
                        تهران، علی‌آباد قاجار، خیابان امام خمینی، کوچه رسول اکرم، پلاک
                        28
                    </a>
                </address>
            </section>
            <!-- site licenses -->
            <section class="footer-licenses flex items-center justify-center">
                <ul class="grid grid-cols-3 gap-16">
                    <li>
                        <a class="rounded-md flex items-center justify-center" href="/" aria-label="مجوز اینماد">
                            <img src="<?php echo get_theme_file_uri('/assets/img/licenses/e-namad.svg'); ?>"
                                alt="مجوز اینماد" aria-label="مجوز اینماد">
                        </a>
                    </li>
                    <li>
                        <a class="rounded-md flex items-center justify-center" href="/" aria-label="مجوز دیجی پی">
                            <img src="<?php echo get_theme_file_uri('/assets/img/licenses/digipay.svg'); ?>"
                                alt="مجوز دیجی پی" aria-label="مجوز دیجی پی">
                        </a>
                    </li>
                    <li>
                        <a class="rounded-md flex items-center justify-center" href="/" aria-label="مجوز اسنپ پی">
                            <img src="<?php echo get_theme_file_uri('/assets/img/licenses/snapp.svg'); ?>"
                                alt="مجوز اسنپ پی" aria-label="مجوز اسنپ پی">
                        </a>
                    </li>
                </ul>
            </section>
        </div>
        <div class="flex items-center justify-between">
            <!-- site pages -->
            <section class="footer-links">
                <ul class="flex flex-row items-centers justify-start gap-46">
                    <li><a href="/" aria-label="صفحه اصلی">صفحه اصلی</a></li>
                    <li><a href="/blog" aria-label="بلاگ">بلاگ</a></li>
                    <li><a href="/about-us" aria-label="درباره ما">درباره ما</a></li>
                    <li><a href="/contact-us" aria-label="تماس با ما">تماس با ما</a></li>
                    <li><a href="/terms" aria-label="roles">قوانین</a></li>
                    <li><a href="/privacy-policy" aria-label="نقشه سایت">نقشه سایت</a></li>
                </ul>
            </section>
            <!-- brand social networks -->
            <ul class="social-links flex flex-row gap-20 justify-center">
                <li><a href="https://instagram.com/almasara.ir" target="_blank" rel="noopener noreferrer"
                        aria-label="Instagram">
                        <img src="<?php echo get_theme_file_uri('/assets/img/footer/instagram.svg'); ?>"
                            alt="Instagram">
                    </a></li>
                <li><a href="https://wa.me/yourphone" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                        <img src="<?php echo get_theme_file_uri('/assets/img/footer/whatsapp.svg'); ?>" alt="WhatsApp">
                    </a></li>
            </ul>
        </div>

    </div>
    <!-- copyright -->
    <div class="footer-copyright flex flex-row justify-center items-center">
        <p class="text-right">&copy;
            تمام حقوق اين وب‌سايت متعلق به فروشگاه الماس‌آرا است. هرگونه استفاده از مطالب این وبسایت بدون ذکر منبع تخلف
            محسوب میشود
        </p>
        <a class="flex items-center justify-center" href="#top" aria-label="بازگشت به بالای صفحه">
            <img src="<?php echo get_theme_file_uri('/assets/img/footer/scroll-up.svg'); ?>" alt="بازگشت به بالا">
        </a>
    </div>
</footer>