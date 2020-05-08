<?php
/**
 *
 * The template for memorial victims archives.
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

	<div class="section-inner section-inner-memorial">
		
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'template-parts/content-memorial' ); ?>
		
	<?php endwhile; endif; ?>

	</div><!-- .section-inner-memorial -->

	<?php if ( comments_open() || get_comments_number() ) {
		?>

		<div class="comments-wrapper section-inner">

			<?php comments_template(); ?>

		</div><!-- .comments-wrapper -->

		<?php
	}
	?>


</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>
