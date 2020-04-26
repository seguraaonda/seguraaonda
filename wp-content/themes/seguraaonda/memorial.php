<?php
/**
 * Template Name: Memorial Template
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
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<?php get_template_part( 'template-parts/entry-header' ); ?>

			<div class="post-inner <?php echo is_page_template( 'templates/template-full-width.php' ) ? '' : 'thin'; ?> ">
				<div class="entry-content">
					<?php the_content();?>
				</div><!-- .entry-content -->
			</div><!-- .post-inner -->

			<div class="section-inner">
				<?php
				$victims = new WP_Query( array('post_type' => 'victim'));
				?>
				<?php while ($victims->have_posts()) : $victims->the_post(); ?>
					<?php get_template_part( 'template-parts/content-memorial' ); ?>
		        <?php endwhile; wp_reset_query(); ?>
			</div>
		</article>
	<?php endwhile; ?>
	<?php endif; ?>

</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>
