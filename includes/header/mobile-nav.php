<?



function custom_footer_nav()
{
    if (is_page('login')) {
        return;
    }

    $user_logged_in = is_user_logged_in();

    $home_link = home_url();
    $shop_link = home_url('/shop');
    $cart_link = home_url('/cart');
    $login_link = home_url('/login');
    $profile_link = home_url('/profile');
    $auth_link = home_url('/verify'); // لینک برای احراز هویت

    $home_img = '/wp-content/uploads/2024/08/home-1.svg';
    $shop_img = '/wp-content/uploads/2024/08/shop-1.svg';
    $cart_img = '/wp-content/uploads/2024/08/shopping-cart.svg';
    $login_img = '/wp-content/uploads/2024/08/login.svg';
    $profile_img = '/wp-content/uploads/2024/08/user.svg';
    $auth_img = '/wp-content/uploads/2024/08/security-safe.svg'; // تصویر برای احراز هویت

    $home_img_active = '/wp-content/uploads/2024/08/home-2.svg';
    $shop_img_active = '/wp-content/uploads/2024/08/shop-2.svg';
    $cart_img_active = '/wp-content/uploads/2024/08/shopping-cart-1.svg';
    $login_img_active = '/wp-content/uploads/2024/08/login-1.svg';
    $profile_img_active = '/wp-content/uploads/2024/08/user-1.svg';
    $auth_img_active = '/wp-content/uploads/2024/08/security-safe-1.svg'; // تصویر فعال برای احراز هویت

    global $wp;
    $current_url = home_url(add_query_arg(array(), $wp->request));
    $home_active = ($current_url == $home_link);
    $shop_active = ($current_url == $shop_link);
    $cart_active = ($current_url == $cart_link);
    $login_active = ($current_url == $login_link);
    $profile_active = ($current_url == $profile_link);
    $auth_active = ($current_url == $auth_link);

?>
    <div class="nav-footer-mobile">
        <a href="<?php echo esc_url($home_link); ?>" class="home-page-nav<?php echo $home_active ? ' active' : ''; ?>">
            <img src="<?php echo esc_url($home_active ? $home_img_active : $home_img); ?>" alt="Home">
            <span class="nav-text<?php echo $home_active ? '' : ' hidden'; ?>">خانه</span>
        </a>
        <a href="<?php echo esc_url($shop_link); ?>" class="shop-page-nav<?php echo $shop_active ? ' active' : ''; ?>">
            <img src="<?php echo esc_url($shop_active ? $shop_img_active : $shop_img); ?>" alt="Shop">
            <span class="nav-text<?php echo $shop_active ? '' : ' hidden'; ?>">فروشگاه</span>
        </a>
        <?php if ($user_logged_in): ?>
            <a href="<?php echo esc_url($cart_link); ?>" class="cart-page-nav<?php echo $cart_active ? ' active' : ''; ?>" style="position: relative;">
                <img src="<?php echo esc_url($cart_active ? $cart_img_active : $cart_img); ?>" alt="Cart">
                <span class="amfc-count amfc-badge">0</span>
                <span class="nav-text<?php echo $cart_active ? '' : ' hidden'; ?>">سبد خرید</span>
            </a>
        <?php endif; ?>
        <?php if (!$user_logged_in): ?>
            <a href="<?php echo esc_url($auth_link); ?>" class="auth-page-nav<?php echo $auth_active ? ' active' : ''; ?>">
                <img src="<?php echo esc_url($auth_active ? $auth_img_active : $auth_img); ?>" alt="Auth">
                <span class="nav-text<?php echo $auth_active ? '' : ' hidden'; ?>">احراز هویت</span>
            </a>
            <a href="<?php echo esc_url($login_link); ?>" class="login-page-nav<?php echo $login_active ? ' active' : ''; ?>">
                <img src="<?php echo esc_url($login_active ? $login_img_active : $login_img); ?>" alt="Login">
                <span class="nav-text<?php echo $login_active ? '' : ' hidden'; ?>">ورود | ثبت نام</span>
            </a>
        <?php endif; ?>
        <?php if ($user_logged_in): ?>
            <a href="<?php echo esc_url($profile_link); ?>" class="acc-page-nav<?php echo $profile_active ? ' active' : ''; ?>">
                <img src="<?php echo esc_url($profile_active ? $profile_img_active : $profile_img); ?>" alt="Profile">
                <span class="nav-text<?php echo $profile_active ? '' : ' hidden'; ?>">حساب کاربری</span>
            </a>
        <?php endif; ?>
    </div>
    <style>
        @media only screen and (min-width: 768px) {
            .nav-footer-mobile {
                display: none;
            }
        }

        .nav-text {
            display: none;
        }

        .nav-footer-mobile a.active .nav-text {
            display: inline;
        }

        .nav-footer-mobile a img {
            width: 24px;
            height: auto;
        }
    </style>
<?php
}

add_action('wp_footer', 'custom_footer_nav');
add_filter('pre_option', 'pre_get_template_option', 20, 3);
function pre_get_template_option($pre, $option, $default_value)
{
    if (! empty($_GET['wc-ajax']) && in_array($option, ["template", "stylesheet"])) {
        return '';
    }

    return $pre;
}