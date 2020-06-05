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
	<div class="map-sidebar">
		<form role="search" method="GET" action="<?php echo esc_url( home_url( '/' ) ); ?>">

			<p><input type="text" value="" name="s" id="s" placeholder="pesquisar por..." /></p>

			<p>
				<label for="tagfilter">Tags</label>

				<?php
					if( $terms = get_terms( array( 'taxonomy' => 'topic-tag', 'orderby' => 'name', 'hide_empty' => true ) ) ) : 
			 
						echo '<select name="tagfilter"><option value="">Selecione a tag...</option>';
						foreach ( $terms as $term ) :
							echo '<option value="' . $term->term_id . '">' . $term->name . '</option>'; // ID of the category as the value of an option
						endforeach;
						echo '</select>';
					endif;
				?>
			</p>
			<p>
				<label for="forumfilter">Fóruns</label>


				<?php
					$args = array(
						'post_type' => 'forum',
						'post_status' => 'public',
						'posts_per_page' => -1,
						'post_parent' => 0,
						'order' => 'ASC',
						'orderby' => 'menu_order'
					);
					$forums = new WP_Query ( $args );

					if( $forums->have_posts() ) {

						echo '<select name="forumfilter"><option value="">Selecione um fórum...</option>';

						while( $forums->have_posts() ){

							$forums->the_post();
							echo '<option value="' . get_the_ID() . '">' . $forums->post->post_title . '</option>';

						}

						echo '</select>';

					} else {

					}
					wp_reset_postdata();
		
				?>
			</p>
			<button type="submit">Pesquisar</button>
			<input type="hidden" name="search-type" value="map" />
		</form>
	</div>

	<?php

	$args = array( 
			'post_type' => 'topic',
			'posts_per_page' => -1, 
			'order' => 'ASC',
			'orderby' => 'menu_order',
	);

	if( is_search() ) {

		if( isset( $_GET['tagfilter'] ) )
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'topic-tag',
					'field' => 'id',
					'terms' => $_GET['tagfilter']
				)
			);

		if( isset( $_GET['forumfilter'] ) )
			$args['post_parent'] = $_GET['forumfilter'];


		$map_query = new WP_Query ( array_merge( $args, $wp_query->query ) );



	} else {
		$map_query = new WP_Query( $args );
	}

	if( $map_query->have_posts() ) :

		while( $map_query->have_posts() ): $map_query->the_post();

			$ID = get_the_ID();
			$localizacao = get_field('localizacao', $value);
			$title = get_the_title();
			$img = get_the_post_thumbnail($ID,'thumbnail');
			$excerpt = get_the_excerpt();
			$permalink = get_permalink();

			if( $localizacao ) {

				$output_map[$ID]['map'] = '<div class="marker" data-lat="'.$localizacao['lat'].'" data-lng="'.$localizacao['lng'].'">'.$img.'<h3 class="marker-title"><a href="'.$permalink.'">'.$title.'</a></h3><p>'.$excerpt.'</p></div>';
			}

		endwhile; endif;

		wp_reset_postdata();

	?>

	<div id="response" class="map-main acf-map">
		<?php
		foreach( $output_map as $key => $map_marker ):
			echo $map_marker['map'];
		endforeach;
		?>
	</div>
</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>
