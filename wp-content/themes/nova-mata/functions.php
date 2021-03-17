<?php
//Enqueue scripts
function novamata_enqueue_scripts() {

	$parent_style = 'twentytwenty-style';

	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/assets/css/editor-style-block.css' );
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/assets/css/editor-style-classic.css' );
	wp_enqueue_style( 'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( $parent_style ),
		wp_get_theme()->get('Version')
	);
}
add_action( 'wp_enqueue_scripts', 'novamata_enqueue_scripts' );

function novamata_get_the_terms( $taxonomy ) {
		
	$terms = get_the_terms( get_the_ID(), $taxonomy );
						
	if ( $terms && ! is_wp_error( $terms ) ) : 
		
		$term_links = array();
		
		foreach ( $terms as $term ) {
			$term_links[] = '<a href="' . esc_attr( get_term_link( $term->slug, $taxonomy ) ) . '">' . __( $term->name ) . '</a>';
		}
								
		$all_terms = join( ', ', $term_links );

		echo  __( $all_terms );

	endif;

}
 
