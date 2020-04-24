<?php
/**
 *
 * Template Name: Map Template
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Segura a Onda
 * @since 1.0.0
 */

get_header();
?>

<main id="site-content" role="main">

	<?php

	if ( have_posts() ) {

		while ( have_posts() ) {
			the_post();
			if( have_rows('locations') ): ?>
    			<div class="acf-map" data-zoom="16">
        	<?php while ( have_rows('locations') ) : the_row(); 

            // Load sub field values.
            $location = get_sub_field('location');
            $title = get_sub_field('description');
            $description = get_sub_field('description');
            ?>
            <div class="marker" data-lat="<?php echo esc_attr($location['lat']); ?>" data-lng="<?php echo esc_attr($location['lng']); ?>">
                <h3><?php echo esc_html( $title ); ?></h3>
                <p><em><?php echo esc_html( $location['address'] ); ?></em></p>
                <p><?php echo esc_html( $description ); ?></p>
            </div>
    <?php endwhile; ?>
    </div>
<?php endif; ?>
		}
	}
	?>


</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>
