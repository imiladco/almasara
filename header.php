<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$viewport_content = apply_filters( 'hello_elementor_viewport_content', 'width=device-width, initial-scale=1' );
$enable_skip_link = apply_filters( 'hello_elementor_enable_skip_link', true );
$skip_link_url = apply_filters( 'hello_elementor_skip_link_url', '#content' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

    
    
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="<?php echo esc_attr( $viewport_content ); ?>">
	<?php wp_head(); ?>
	
</head>
<body <?php body_class(); ?>>

<?php

if ( wp_is_mobile() ) {
    get_template_part( 'header', 'mobile' ); // یعنی header-mobile.php
} else {
    get_template_part( 'header', 'desktop' ); // یعنی header-desktop.php
}

?>