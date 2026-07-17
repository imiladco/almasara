<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( wp_is_mobile() ) {
    get_template_part( 'footer', 'mobile' ); // یعنی header-mobile.php
} else {
    get_template_part( 'footer', 'desktop' ); // یعنی header-desktop.php
}
?>

<?php wp_footer(); ?>

</body>
</html>