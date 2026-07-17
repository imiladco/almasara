<?php
/**
 * The template for displaying header.
 *
 * @package HelloElementorChild
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$site_name = get_bloginfo( 'name' );
$tagline   = get_bloginfo( 'description', 'display' );
$header_nav_menu = wp_nav_menu( [
    'theme_location' => 'menu-1', // منوی اصلی
    'fallback_cb' => false,
    'container' => false,
    'echo' => false,
    'menu_class' => 'main-menu', // کلاس برای استایل‌دهی
] );
?>

<header id="site-header" class="site-header custom-header" role="banner">
    <div class="header-container"> <!-- کانتینر برای responsive بودن -->
        <div class="site-branding">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <h1 class="site-title">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                        <?php echo esc_html( $site_name ); ?>
                    </a>
                </h1>
                <?php if ( $tagline ) : ?>
                    <p class="site-description"><?php echo esc_html( $tagline ); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if ( $header_nav_menu ) : ?>
            <nav class="site-navigation" aria-label="<?php esc_attr_e( 'Main menu', 'hello-elementor-child' ); ?>">
                <?php echo $header_nav_menu; // نمایش منو ?>
            </nav>
        <?php endif; ?>

        <!-- اضافه کردن جستجو (اختیاری) -->
        <div class="header-search">
            <?php get_search_form(); ?>
        </div>
    </div>
</header>