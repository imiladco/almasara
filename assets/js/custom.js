// اطمینان از وجود jQuery
if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded');
} else {
    (function ($) {
        // تمام کدها در یک رویداد DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function () {
            // === ماژول منوی موبایل ===
            function initMobileMenu() {
                function toggleMenu() {
                    const menu = document.getElementById('menumobile');
                    const overlay = document.querySelector('.dialog__overlay');
                    const mobilePanelWrapper = document.querySelector('.mobilepanel-wrapper');
                    const closeMenuButton = document.querySelector('.close-menu-button');
                    const socialsMobile = document.querySelector('.socials-mobile');

                    if (menu) {
                        menu.classList.toggle('active');
                        overlay?.classList.toggle('open');
                        mobilePanelWrapper?.classList.toggle('active');
                        closeMenuButton?.classList.toggle('active');
                        socialsMobile?.classList.toggle('active');
                    }
                }

                // اتصال رویداد به آیکون همبرگر و دکمه بستن
                document.querySelectorAll('.hamburger-icon, .close-menu-button').forEach(element => {
                    element.addEventListener('click', toggleMenu, false);
                });

                // مدیریت زیرمنوها
                document.querySelectorAll('.has-submenu > a').forEach(menuItem => {
                    menuItem.addEventListener('click', function (e) {
                        e.preventDefault();
                        const submenu = this.nextElementSibling;
                        if (submenu) submenu.classList.add('active');
                    });
                });
            }

            // === ماژول بازگشت از زیرمنو ===
            window.goBack = function (element) {
                const submenu = element.closest('.submenu');
                if (submenu) submenu.classList.remove('active');
            };

            // === ماژول دکمه‌های سبد خرید ===
            // ریدایرکت تأیید هویت روی افزودن به سبد حذف شد؛ افزودن به سبد را
            // افزونه «سبد سریع الماسارا» به‌صورت ایجکسی و بدون ترک صفحه انجام می‌دهد.
            function initCartButtons() {}

            // === ماژول جستجو ===
            function initSearch() {
                function toggleSearch() {
                    const searchIconContainer = document.getElementById('search-icon-container');
                    const searchInputContainer = document.getElementById('search-input-container');
                    const searchField = document.getElementById('custom-search-field');

                    if (searchIconContainer && searchInputContainer && searchField) {
                        searchIconContainer.classList.toggle('hidden');
                        searchInputContainer.classList.toggle('open');
                        if (searchInputContainer.classList.contains('open')) {
                            searchField.focus();
                        }
                    }
                }

                // اتصال رویداد به آیکون جستجو و بستن
                document.querySelectorAll('#search-icon, #close-search').forEach(element => {
                    element.addEventListener('click', toggleSearch, false);
                });

                // بستن جستجو با کلید Escape
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        const searchIconContainer = document.getElementById('search-icon-container');
                        const searchInputContainer = document.getElementById('search-input-container');
                        if (searchInputContainer?.classList.contains('open')) {
                            searchIconContainer.classList.remove('hidden');
                            searchInputContainer.classList.remove('open');
                        }
                    }
                });
            }

            // === ماژول جستجوی پیشرفته ===
            function initAdvancedSearch() {
                const container = document.querySelector('.elementor-6007 .elementor-element.elementor-element-3264467 > .elementor-container');
                let originalContent = container?.innerHTML || '';

                function restoreOriginalContent() {
                    if (container) {
                        container.innerHTML = originalContent;
                        loadAllScripts();
                        handleSearchIconClick();
                        bindMenuEvents();
                    }
                }

                function handleSearchIconClick() {
                    const searchIcon = document.getElementById('search-icon');
                    const closeSearch = document.getElementById('close-search');
                    const searchIconContainer = document.getElementById('search-icon-container');
                    const searchInputContainer = document.getElementById('search-input-container');
                    const searchField = document.getElementById('custom-search-field');

                    if (searchIcon && closeSearch && searchIconContainer && searchInputContainer && searchField) {
                        const openSearch = function (event) {
                            event.preventDefault();
                            searchIconContainer.style.display = 'none';
                            if (container) {
                                container.innerHTML = '';
                                container.appendChild(searchInputContainer);
                            }
                            searchInputContainer.style.display = 'flex';
                            searchInputContainer.classList.remove('closed');
                            searchInputContainer.classList.add('open');
                            searchField.focus();
                        };

                        const closeSearchHandler = function () {
                            searchIconContainer.style.display = 'inline-block';
                            searchInputContainer.classList.remove('open');
                            searchInputContainer.classList.add('closed');
                            setTimeout(() => restoreOriginalContent(), 300);
                        };

                        searchIcon.addEventListener('click', openSearch);
                        closeSearch.addEventListener('click', closeSearchHandler);

                        document.addEventListener('keydown', function (event) {
                            if (event.key === 'Escape') {
                                closeSearchHandler();
                            }
                        });
                    }
                }

                function bindMenuEvents() {
                    const menuItem = document.querySelector('.profile-menu-v2');
                    const boxMenu = document.querySelector('.box-menu-v2');

                    if (menuItem && boxMenu) {
                        menuItem.addEventListener('click', function (event) {
                            if (event.target.closest('.title-user')) {
                                event.preventDefault();
                                const isOpen = menuItem.classList.contains('clicked');
                                menuItem.classList.toggle('clicked', !isOpen);
                                boxMenu.classList.toggle('clicked', !isOpen);
                            }
                        });

                        document.addEventListener('click', function (event) {
                            if (!menuItem.contains(event.target)) {
                                menuItem.classList.remove('clicked');
                                boxMenu.classList.remove('clicked');
                            }
                        });

                        document.querySelectorAll('.elementor-widget-container').forEach(container => {
                            if (container.querySelector('.login-rsoon-v2')) {
                                container.classList.add('margin-small');
                            } else if (container.querySelector('.box-menu-v2')) {
                                container.classList.add('margin-large');
                            }
                        });
                    }
                }

                function loadAllScripts() {
                    document.querySelectorAll('script').forEach(script => {
                        const newScript = document.createElement('script');
                        if (script.src) {
                            newScript.src = script.src;
                        } else {
                            newScript.textContent = script.innerText;
                        }
                        document.body.appendChild(newScript);
                        script.remove(); // حذف اسکریپت قدیمی برای جلوگیری از تکرار
                    });
                }

                handleSearchIconClick();
                bindMenuEvents();
            }

            // === ماژول افکت بلور برای گرید ===
            function initGridBlurEffect() {
                document.querySelectorAll('.jet-listing-grid__item').forEach(item => {
                    item.addEventListener('mouseenter', () => {
                        document.querySelectorAll('.jet-listing-grid__item').forEach(otherItem => {
                            if (otherItem !== item) {
                                otherItem.classList.add('blur');
                            }
                        });
                    });

                    item.addEventListener('mouseleave', () => {
                        document.querySelectorAll('.jet-listing-grid__item').forEach(otherItem => {
                            otherItem.classList.remove('blur');
                        });
                    });
                });
            }

            // === ماژول اسلایدر ===
            function initSlider() {
                const sliderContent = document.querySelector('.slider-content');
                if (sliderContent) {
                    const contentHTML = sliderContent.innerHTML;
                    sliderContent.innerHTML += contentHTML + contentHTML;
                }
            }

            // === ماژول تبدیل ارقام به فارسی/عربی ===
            function initDigitConverter() {
                function toPersianOrArabicDigit(text, useArabic = false) {
                    return text.replace(/\d/g, digit => {
                        const enDigit = digit.charCodeAt(0);
                        const offset = useArabic ? 1584 : 1728;
                        return String.fromCharCode(enDigit + offset);
                    });
                }

                function convertDigitsInNode(node, useArabic = false) {
                    if (node.nodeType === Node.TEXT_NODE) {
                        node.nodeValue = toPersianOrArabicDigit(node.nodeValue, useArabic);
                    } else {
                        node.childNodes.forEach(child => convertDigitsInNode(child, useArabic));
                    }
                }

                // اعمال تبدیل ارقام به تمام سند
                convertDigitsInNode(document.body);
            }

            // === ماژول تخفیف محصولات ===
            function initProductDiscounts() {
                function updateDiscountInfo(variation) {
                    const regular_price = parseFloat(variation.display_regular_price);
                    const sale_price = parseFloat(variation.display_price);

                    if (sale_price && regular_price && regular_price > sale_price) {
                        const discount_percentage = Math.round(((regular_price - sale_price) / regular_price) * 100);
                        const discount_amount = Math.round(regular_price - sale_price);
                        $('.offer-darsad').text(`${discount_percentage}% تخفیف`);
                        $('.offer-mablag').text(`${discount_amount.toLocaleString('fa-IR')} تومان تخفیف`);
                    } else {
                        $('.offer-darsad').text('');
                        $('.offer-mablag').text('');
                    }
                }

                $('form.variations_form').on('show_variation', (event, variation) => {
                    updateDiscountInfo(variation);
                });

                $('form.variations_form').on('hide_variation', () => {
                    $('.offer-darsad').text('');
                    $('.offer-mablag').text('');
                });

                // تخفیف پیش‌فرض
                const $form = $('form.variations_form');
                if ($form.length) {
                    const variations = $form.data('product_variations') || [];
                    let max_discount = { percentage: 0, amount: 0 };

                    variations.forEach(v => {
                        const regular_price = parseFloat(v.display_regular_price);
                        const sale_price = parseFloat(v.display_price);
                        if (sale_price && regular_price && regular_price > sale_price) {
                            const discount_percentage = Math.round(((regular_price - sale_price) / regular_price) * 100);
                            const discount_amount = Math.round(regular_price - sale_price);
                            if (discount_percentage > max_discount.percentage) {
                                max_discount = { percentage: discount_percentage, amount: discount_amount };
                            }
                        }
                    });

                    if (max_discount.percentage > 0) {
                        $('.offer-darsad').text(`${max_discount.percentage}% تخفیف`);
                        $('.offer-mablag').text(`${max_discount.amount.toLocaleString('fa-IR')} تومان تخفیف`);
                    }
                }
            }

            // === ماژول قیمت محصولات متغیر ===
            function initVariableProductPrice() {
                $('form.variations_form').on('found_variation', (event, variation) => {
                    if (variation?.price_html) {
                        $('#variable-product-price').html(variation.price_html);
                    }
                });

                $('form.variations_form').on('reset_data', function () {
                    const product_id = $(this).data('product_id');
                    $.ajax({
                        url: '/wp-admin/admin-ajax.php',
                        method: 'POST',
                        data: {
                            action: 'get_product_min_price',
                            product_id: product_id
                        },
                        success: response => {
                            if (response.success) {
                                $('#variable-product-price').html(response.data.price_html);
                            } else {
                                console.error('Error fetching product price:', response.data);
                            }
                        },
                        error: (xhr, status, error) => {
                            console.error('AJAX error:', error);
                        }
                    });
                });

                // بارگذاری اولیه قیمت
                const $form = $('form.variations_form');
                if ($form.length) {
                    const product_id = $form.data('product_id');
                    $.ajax({
                        url: '/wp-admin/admin-ajax.php',
                        method: 'POST',
                        data: {
                            action: 'get_product_min_price',
                            product_id: product_id
                        },
                        success: response => {
                            if (response.success) {
                                $('#variable-product-price').html(response.data.price_html);
                            } else {
                                console.error('Error fetching product price:', response.data);
                            }
                        },
                        error: (xhr, status, error) => {
                            console.error('AJAX error:', error);
                        }
                    });
                }
            }

            // فراخوانی تمام ماژول‌ها
            initMobileMenu();
            initCartButtons();
            initSearch();
            initAdvancedSearch();
            initGridBlurEffect();
            initSlider();
            initDigitConverter();
            initProductDiscounts();
            initVariableProductPrice();
        });
    })(jQuery);
}