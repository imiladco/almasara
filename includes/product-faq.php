<?


function wocommerce_faq_shortcode()
{
    ob_start();
    $query = new WP_Query(array(
        'cat'            => 1790,
        'posts_per_page' => -1,
    ));
    if ($query->have_posts()) : ?>
        <h3 class="faq-title">پرسش های پرتکرار</h3>
        <div class="faq-countiner">
            <?php
            $index = 1;
            while ($query->have_posts()) :
                $query->the_post(); ?>
                <div data-state="close" class="faq-woocomerce question-number-<?php echo $index++; ?>">
                    <h4 class="question"><?php the_title(); ?><img alt="arrow" src="/wp-content/uploads/2024/12/arrow.svg"></h4>
                    <div class="answer"><?php the_content(); ?></div>
                </div>
            <?php endwhile; ?>
        </div>
        <script>
            jQuery(document).ready(function($) {
                $('.question-number-1').attr('data-state', 'open');
                $('.faq-woocomerce .question').on('click', function() {
                    $('.faq-woocomerce').attr('data-state', 'close');
                    $(this).closest('.faq-woocomerce').attr('data-state', 'open');
                });
            });
        </script>
    <?php else :
        echo 'هیچ نوشته‌ای یافت نشد!';
    endif;
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('wocommerce_faq', 'wocommerce_faq_shortcode');