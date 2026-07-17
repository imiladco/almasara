<header class="bg-general-01">
    <div class="header-container flex flex-row items-center justify-between">
        <a href="/" class="logo">
            <img src="<? echo wp_get_attachment_image_url(get_theme_mod('custom_logo')) ?>" alt="almasara" class="w-full" style="height:48px">
        </a>
        <a>
            <img src="/wp-content/themes/almasara/assets/img/search.svg" alt="search" id="has-sub-menu-search" class="cursor-pointer">
            <div id="sub-menu-search" class="absolute w-full bg-general-01 invisible" style="top:0; left:0">
                <form role="search" method="get" class="flex flex-row gap-16 w-full items-center" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="search" id="custom-search-field" class="border-black-02 rounded-ml border-solid border border-1 py-3 px-4 w-full " placeholder="جستجو در محصولات…" value="" name="s" autocomplete="off" style="color: rgb(119, 119, 119);">
                    <button type="submit" class="p-unset w-8 h-8 flex bg-transparent"><img calss="w-8 h-8 flex" src="/wp-content/uploads/2024/07/search-status-1.svg"></button>
                    <span calss="p-unset w-8 h-8 flex"><img class="w-8 h-8" id="close-sub-menu-search" src="/wp-content/uploads/2024/07/Group-1000002050.svg"></span>
                    <input type="hidden" name="post_type" value="product">
                </form>
            </div>
        </a>
    </div>
    
    <nav class="mobile-nav-menu fixed bg-general-01" aria-label="mobile">
        <ul class="grid grid-cols-5 gap-4">
            <li class="<?php echo (is_page(39)) ? 'active' : ''; ?>">
                <a href="/" class="flex flex-col items-center justify-between font-normal">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/menu/mobile/home.svg" alt="profile" id="has-sub-menu-profile" class="cursor-pointer">
                    صفحه اصلی
                </a>
            </li>
            <li class="<?php echo (is_page('/shop/')) ? 'active' : ''; ?>">
                <a href="/shop" class="flex flex-col items-center justify-between font-normal">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/menu/mobile/shop.svg" alt="profile" id="has-sub-menu-profile" class="cursor-pointer">
                    فروشگاه
                </a>
            </li>
            <li>
                <a href="" class="flex flex-col items-center justify-between font-normal">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/menu/mobile/category.svg" alt="profile" id="has-sub-menu-profile" class="cursor-pointer">
                    دسته بندی
                </a>
            </li>
            <li class="<?php echo (is_page(9)) ? 'active' : ''; ?>">
                <a href="/cart" class="flex flex-col items-center justify-between font-normal">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/menu/mobile/cart.svg" alt="profile" id="has-sub-menu-profile" class="cursor-pointer">
                    سبد خرید
                </a>
            </li>
            <li class="<?php echo (is_page(11)) ? 'active' : ''; ?>">
                <a href="/login" class="flex flex-col items-center justify-between font-normal">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/menu/mobile/profile.svg" alt="profile" class="cursor-pointer">
                    حساب‌من
                </a>
            </li>
        </ul>
    </nav>
</header>

<main id="site-content">