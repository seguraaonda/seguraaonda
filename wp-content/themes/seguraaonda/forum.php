<?php
/**
 * The template for displaying forums.
 * Template Name: Forum Template
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Segura a Onda
 * @since 1.0.0
 */



	if ( bbp_is_search() ) {

		if(isset($_GET['search-type'])) {

			$type = $_GET['search-type'];

			if($type == 'map') {

				echo "mapa";
			}

		}

	} else {

		get_header();

?>

		<main id="site-content" role="main">

			<?php

			if ( have_posts() ) {

				while ( have_posts() ) {
					the_post();

					get_template_part( 'template-parts/content-forum' );
				}
			}
			?>


		</main><!-- #site-content -->

		<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

		<?php get_footer(); 

	}


?>
