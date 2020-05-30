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
		<?php if ( bbp_allow_search() ) : ?>

			<div class="bbp-search-form">
				<form role="search" method="get" id="bbp-search-form">
					<div>
						<label class="screen-reader-text hidden" for="bbp_search"><?php esc_html_e( 'Search for:', 'bbpress' ); ?></label>
						<input type="hidden" name="action" value="bbp-search-request" />
						<input type="hidden" name="search-type" value="map" />
						<input type="text" value="<?php bbp_search_terms(); ?>" name="bbp_search" id="bbp_search" />
						<input class="button" type="submit" id="bbp_search_submit" value="<?php esc_attr_e( 'Search', 'bbpress' ); ?>" />
					</div>
				</form>
			</div>

		<?php endif; ?>
	</div>

	<?php

	if ( bbp_has_topics() ) :

		while ( bbp_topics() ) : bbp_the_topic();

			$ID = get_the_ID();
			$localizacao = get_field('localizacao', $value);
			$title = get_the_title();
			$img = get_the_post_thumbnail($ID,'thumbnail');
			$excerpt = get_the_excerpt();
			$permalink = get_permalink();

			if( $localizacao ) {

				$output_map[$ID]['map'] = '<div class="marker" data-lat="'.$localizacao['lat'].'" data-lng="'.$localizacao['lng'].'">'.$img.'<h3 class="marker-title"><a href="'.$permalink.'">'.$title.'</a></h3><p>'.$excerpt.'</p></div>';
			}

		endwhile;

	endif;

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
