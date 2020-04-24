<?php
/**
 * The default template for displaying content
 *
 * Used for bbPress forums.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Segura a Onda
 *
 * @since 1.0.0
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header has-text-align-center">
		<div class="entry-header-inner section-inner medium">
			<?php the_title( '<h2 class="entry-title heading-size-1">', '</h2>' ); ?>
		</div>
	</header>

	<div class="post-inner">

		<div class="entry-content">

			<?php the_content(); ?>

		</div><!-- .entry-content -->
		<div>
			<?php
				$age = get_post_meta( get_the_ID(), 'seguraaonda-memorial-age', true );
				if( !empty( $age ) ) {
					echo $age;
				}
 
			?>

	</div><!-- .post-inner -->

</article><!-- .post -->
