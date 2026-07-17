
<header class="flex items-center justify-center bg-general-01">
<div class="header-desktop flex flex-row justify-between items-center gap-16">
    <div class="flex flex-row gap-16">
        <a href="/" class="logo">
            <img src="<? echo wp_get_attachment_image_url(get_theme_mod('custom_logo')) ?>" alt="almasara" class="w-full" style="height:48px">
        </a>
        <nav class="nav-menu" aria-label="Main Navigation">
            <ul class="flex flex-row items-center gap-8 font-normal text-body-1 m-unset height-full">
                <li style="height: 40px;">
                    <a class="px-4 rounded-md height-full flex flex flex-row gap-12 items-center" href=""
                        style="color: #4B5259;" id="has-sub-menu-category">
                        دسته‌بندی محصولات
                        <img src="/wp-content/themes/almasara/assets/img//menu/arrow-down.svg" alt="arrow-down-menu">
                    </a>
                    <?php echo do_shortcode( '[sub_menu_almasara]' ); ?>  
                </li>
                <li class="a-divider" style="background:#0000001f;width:1px;height:24px;"></li>
                <li style="height: 40px;"><a class="px-4 rounded-md height-full flex items-center"
                        href="/offers/" style="color: #4B5259;">خرید اقساطی</a></li>
                <li class="a-divider" style="background:#0000001f;width:1px;height:24px;"></li>

            </ul>
        </nav>
    </div>
    <div class="flex flex-row gap-8 items-center"> 
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
        <div>
            <img src="/wp-content/themes/almasara/assets/img/fragment.svg" alt="fragment" id="has-sub-menu-fragment" class="cursor-pointer">
            <?php echo do_shortcode( '[custom_cart_wrapper]' ); ?>    
        </div>
        <span class="a-divider" style="background:#0000001f;width:1px;height:24px;"></span>
        <nav class="nav-menu" aria-label="profil-user">
            <ul>
                <li>
                    <a href="">
                        <img src="/wp-content/themes/almasara/assets/img/profile.svg" alt="profile" id="has-sub-menu-profile" class="cursor-pointer">
                    </a>
                    <ul class="flex flex-col py-2 absolute rounded-md bg-general-01 invisible opacity-0"
                        style="width: 250px;padding-inline: 0 !important;left: 0; margin-top: 20px !important; box-shadow: 0 0px #0000, 0 0 0px #0000, 0 3px 10px 0 rgba(0, 0, 0, .12), 0 10px 10px -6px rgba(0, 0, 0, .12); z-index: 9999;" id="sub-menu-profile">
                        <li class="flex flex-col gap-8 p-16"
                            style="background-color: rgb(248, 250, 251);border-top-left-radius: 8px;border-top-right-radius: 8px;">
                            <span class="text-body-2 font-normal" style="color: rgb(10, 11, 12);">
                                09xxxxxxxxx
                            </span>
                            <div class="flex flex-row items-center justify-between">
                                <span class="flex flex-row gap-8 items-center text-body-2">
                                    <img src="/wp-content/themes/almasara/assets/img/menu/profile/cart-count.svg"
                                        alt="cart-count">
                                    0 کالا
                                </span>
                                <a href="/cart/" class="font-normal flex flex-row items-center py-05 px-1 rounded-md"
                                    style="color: #0077db;">
                                    سبدخرید
                                    <img src="/wp-content/themes/almasara/assets/img/menu/profile/arrow-left.svg"
                                        alt="show-cart">
                                </a>
                            </div>
                        </li>
                        <li>
                            <a class="px-4 flex flex-row gap-8 items-center" href="/profile/" style="height: 48px;">
                                <img src="/wp-content/themes/almasara/assets/img/menu/profile/panel.svg" alt="">
                                پنل کاربری
                            </a>
                        </li>
                        <hr class="a-divider flex" aria-hidden="true"
                            style="min-width: 100%;height:1px;background: #0000001f;">
                        <li>
                            <a class="px-4 flex flex-row gap-8 items-center" href="/profile/edit-account/" style="height: 48px;">
                                <img src="/wp-content/themes/almasara/assets/img/menu/profile/profile.svg" alt="">
                                اطلاعات حساب‌کاربری
                            </a>
                        </li>
                        <hr class="a-divider flex" aria-hidden="true"
                            style="min-width: 100%;height:1px;background: #0000001f;">
                        <li>
                            <a class="px-4 flex flex-row gap-8 items-center" href="/profile/orders/" style="height: 48px;">
                                <img src="/wp-content/themes/almasara/assets/img/menu/profile/oreders.svg" alt="">
                                سفارش‌ها
                            </a>
                        </li>
                        <hr class="a-divider flex" aria-hidden="true"
                            style="min-width: 100%;height:1px;background: #0000001f;">
                        <li>
                            <a class="px-4 flex flex-row gap-8 items-center" href="/profile/customer-logout/?_wpnonce=1d6bde37d9/" style="height: 48px;">
                                <img src="/wp-content/themes/almasara/assets/img/menu/profile/logout.svg" alt="">
                                خروج از حساب‌کاربری
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
</header>
