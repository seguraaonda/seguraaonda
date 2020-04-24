<?php
/**
 * The template for displaying memorial victims.
 *
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Segura a Onda
 * @since 1.0.0
 */

get_header();
?>

<main id="site-content" role="main">
	<header class="archive-header has-text-align-center header-footer-group">

		<div class="archive-header-inner section-inner medium">
				<h1 class="archive-title">Memorial</h1>
				<div class="archive-subtitle section-inner thin max-percentage intro-text">Memorial das vítimas do Covid 19</div>
		</div><!-- .archive-header-inner -->
	</header><!-- .archive-header -->

	<?php

	if ( have_posts() ) {

		while ( have_posts() ) {
			the_post();

			get_template_part( 'template-parts/content-memorial' );
		}
	}
	?>


</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>
