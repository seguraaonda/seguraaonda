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
	<?php $intro = new WP_Query( array( 'pagename' => 'memorial-intro' ) );?>

	<?php if ($intro->have_posts()) : while ($intro->have_posts()) : $intro->the_post(); ?>

		<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

			<header class="entry-header has-text-align-center<?php echo esc_attr( $entry_header_classes ); ?>">

				<div class="entry-header-inner section-inner medium">
				

					<?php the_title( '<h2 class="entry-title heading-size-1">', '</h2>' );?>

				
				</div><!-- .entry-header-inner -->

			</header><!-- .entry-header -->
			
			<div class="post-inner thin">
				
				<div class="entry-content">
					<?php the_content();?>
				</div><!-- .entry-content -->

			</div><!-- .post-inner -->

		</article>

	<?php endwhile; endif; ?>

	<?php wp_reset_query(); ?>

	<div class="section-inner section-inner-memorial">
		
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'template-parts/content-memorial' ); ?>
		
	<?php endwhile; endif; ?>

	</div><!-- .section-inner-memorial -->

	<?php get_template_part( 'template-parts/pagination' ); ?>

</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>
