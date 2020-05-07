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
	$map_query = new WP_Query( array( 'post_type' => 'topic', 'posts_per_page' => -1, 'order' => 'ASC', 'orderby' => 'menu_order' ) );

	if($map_query->have_posts()) :

		while($map_query->have_posts()):

			$map_query->the_post();
			$ID = get_the_ID();
			$localizacao = get_field('localizacao', $value);
			$title = get_the_title();
			$img = get_the_post_thumbnail($post_id,'thumbnail');
			$excerpt = get_the_excerpt();

			if($localizacao) {

				$output_map[$ID]['map'] = '<div class="marker" data-lat="'.$localizacao['lat'].'" data-lng="'.$localizacao['lng'].'">'.$img.'<h3 class="marker-title">'.$title.'</h3><p>'.$excerpt.'</p></div>';
			}

		endwhile; endif;

		wp_reset_postdata();

	?>

	<div class="acf-map">
		<?php
		foreach( $output_map as $key => $map_marker ):
			echo $map_marker['map'];
		endforeach;
		?>
	</div>
</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>
