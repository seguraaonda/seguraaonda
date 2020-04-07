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

	<?php

	get_template_part( 'template-parts/entry-header' );

	?>

	<div class="post-inner">

		<div class="entry-content">

			<?php the_content(); ?>

		</div><!-- .entry-content -->

		<?php if ( ! bbp_is_single_user() ) {
			get_template_part( 'template-parts/sidebar-forum' );
		}
		?>


	</div><!-- .post-inner -->

</article><!-- .post -->
