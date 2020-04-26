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
		$the_query_map = new WP_Query( array( 'post_type' => 'topic', 'posts_per_page' => -1, 'order' => 'ASC', 'orderby' => 'menu_order' ) );

		if($the_query_map->have_posts()) :
			while($the_query_map->have_posts()):
				$the_query_map->the_post();
				$the_ID = get_the_ID();
				$get_google_map = get_field('localizacao', $value);

				$output_map[$the_ID]['map'] = '<div class="marker" data-lat="'.$get_google_map['lat'].'" data-lng="'.$get_google_map['lng'].'"></div>';

			endwhile; endif;
			wp_reset_postdata();

			?><div class="acf-map"><?php
			foreach( $output_map as $key => $map_marker ):
				echo $map_marker['map'];
			endforeach;
			?>
		</div>
</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>
