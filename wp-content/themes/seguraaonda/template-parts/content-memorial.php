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
		<div class="entry-content">
			<?php if ( has_post_thumbnail()) :  the_post_thumbnail('thumbnail'); ; ?>
			<?php else : ?>
				<img alt="" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/memorial.jpg" />
			<?php endif; ?>
			<?php the_title( '<h2 class="entry-title heading-size-4"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
			<div class="sao-victim-info">
			<?php
				if(get_field('data')) {
					echo '<p><strong>Data:</strong> ' . get_field('data') . '</p>';
				}
				if(get_field('idade')) {
					echo '<p><strong>Idade:</strong> ' . get_field('idade') . '</p>';
				}
				if(get_field('genero')) {
					echo '<p><strong>Genero:</strong> ' . get_field('genero') . '</p>';
				}
				if(get_field('profissao')) {
					echo '<p><strong>Profissão:</strong> ' . get_field('profissao') . '</p>';
				}
			?>
			<?php seguraaonda_display_location() ?>
			</div>
			<?php the_excerpt(); ?>
			
			<?php
				if(get_field('fonte')) {
					echo '<p class="meta-wrapper"><span class="meta-icon">'; 
					twentytwenty_the_theme_svg( 'link' );
					echo '</span><a href="' . get_field('fonte') . '">Fonte</a></p>';
				}
			?>
			<p class="meta-wrapper">
				<span class="meta-icon">
					<?php twentytwenty_the_theme_svg( 'comment' ); ?>
				</span>
				<span class="meta-text">
					<?php comments_popup_link( __( 'Leave a tribute', 'seguraaonda' ), __( '1 tribute', 'seguraaonda' ), __( '% tributes', 'seguraaonda' ) ); ?>
				</span>
			</p>
		</div><!-- .entry-content -->

</article><!-- .post -->
