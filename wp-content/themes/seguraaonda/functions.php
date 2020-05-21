<?php

//Enqueue scripts
function seguraaonda_enqueue_scripts() {

	$parent_style = 'twentytwenty-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/assets/css/editor-style-block.css' );
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/assets/css/editor-style-classic.css' );
	wp_enqueue_style( 'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( $parent_style ),
		wp_get_theme()->get('Version')
	);
	if( is_page_template("mapa.php")) {
		$mapkeyurl = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCIOOogfsVQsOF3VSl6RH928JX8chhFwEg';
		$dir_url = get_stylesheet_directory_uri() . '/assets/js/acf-map.js';

		wp_enqueue_script( 'map_script', $dir_url, array('jquery'), '1.0', true );
		wp_enqueue_script( 'googlemap', $mapkeyurl, true);
		$translation_array = array( 'templateUrl' => get_stylesheet_directory_uri() );
		wp_localize_script( 'map_script', 'object_name', $translation_array );
	}
	
}

add_action( 'wp_enqueue_scripts', 'seguraaonda_enqueue_scripts' );

//Set Home & Archives Facebook thumbnail
function seguraaonda_home_fb_image( $tags ) {

	if ( is_home() || is_front_page() || is_archive() ) {

		unset( $tags['og:image'] );
		unset( $tags['og:image:width'] );
		unset( $tags['og:image:height'] );

		$fb_home_img = get_stylesheet_directory_uri() . '/assets/img/logo_segura_a_onda.png';

		$tags['og:image'] = esc_url( $fb_home_img );
		$tags['og:image:width'] = 1200;
		$tags['og:image:height'] = 675;
	}

	return $tags;

}

add_filter( 'jetpack_open_graph_tags', "seguraaonda_home_fb_image" );

//Set default fallback Facebook thumbnail
function seguraaonda_jetpack_default_image( $image ) {

	$fb_default_img = get_stylesheet_directory_uri() . '/assets/img/logo_segura_a_onda.png';

	return $fb_default_img;

}

add_filter( 'jetpack_open_graph_image_default', "seguraaonda_jetpack_default_image" );

//Register sidebar widget area
function seguraaonda_sidebar_registration() {

	// Forum.
	register_sidebar(
		array(
			'before_title'  => '<h2 class="widget-title subheading heading-size-3">',
			'after_title'   => '</h2>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget'  => '</div></div>',
			'name'        => __( 'Forum', 'seguraaonda' ),
			'id'          => 'sidebar-3',
			'description' => __( 'Widgets in this area will be displayed in the forums pages.', 'seguraaonda' ),
		)
	);

}

add_action( 'widgets_init', 'seguraaonda_sidebar_registration' );

//Register Victim custom post type
function seguraaonda_victim_cpt() {
	$labels = array(
		'name'                  => _x( 'Victim', 'Post type general name', 'seguraaonda' ),
		'singular_name'         => _x( 'Victim', 'Post type singular name', 'seguraaonda' ),
		'menu_name'             => _x( 'Victim', 'Admin Menu text', 'seguraaonda' ),
		'name_admin_bar'        => _x( 'Victim', 'Add New on Toolbar', 'seguraaonda' ),
		'add_new'               => __( 'Add New', 'seguraaonda' ),
		'add_new_item'          => __( 'Add New Victim', 'seguraaonda' ),
		'new_item'              => __( 'New Victim', 'seguraaonda' ),
		'edit_item'             => __( 'Edit Victim', 'seguraaonda' ),
		'view_item'             => __( 'View Victim', 'seguraaonda' ),
		'all_items'             => __( 'All Victims', 'seguraaonda' ),
		'search_items'          => __( 'Search Victim', 'seguraaonda' ),
		'not_found'             => __( 'No victims found.', 'seguraaonda' ),
		'not_found_in_trash'    => __( 'No victims found in Trash.', 'seguraaonda' ),
		'featured_image'        => _x( 'Victim Picture', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'seguraaonda' ),
		'set_featured_image'    => _x( 'Set picture', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'seguraaonda' ),
		'remove_featured_image' => _x( 'Remove picture', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'seguraaonda' ),
		'use_featured_image'    => _x( 'Use as picture', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'seguraaonda' ),
		'archives'              => _x( 'Victim', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'seguraaonda' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'has_archive'		 => true,
		'rewrite'            => array( 'slug' => 'memorial' ),
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'thumbnail', 'excerpt', 'comments' ),
		'menu_icon'          => 'dashicons-heart',
	);

	register_post_type( 'victim', $args );
}
add_action('init', 'seguraaonda_victim_cpt');

//Flush rewrite rules when activating theme
function seguraaonda_rewrite_flush() {

	seguraaonda_victim_cpt();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'seguraaonda_rewrite_flush' );

//Order victims by title
function seguguraaonda_victim_order( $query ) {
	if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'victim' ) ) {
		$query->set( 'orderby', 'title' );
		$query->set('order', 'ASC');
		return;
	}
}
add_action( 'pre_get_posts', 'seguguraaonda_victim_order', 1 );

function seguraaonda_victim_archive_title( $title )
{
	if ( is_post_type_archive('victim') ) {
		$title = 'Memorial';
	}

	return $title;
}
add_filter( 'pre_get_document_title', 'seguraaonda_victim_archive_title' );

//Add description after forums titles on forum index
function seguraaonda_singleforum_description() {
	echo '<p class="forum-description">';
	echo bbp_forum_content();
	echo '</p>';
}

add_action( 'bbp_template_before_single_forum' , 'seguraaonda_singleforum_description');

function seguraaonda_login_stylesheet() {
	wp_enqueue_style( 'twentytwenty-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css' );
	wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/style-login.css' );
}

add_action( 'login_enqueue_scripts', 'seguraaonda_login_stylesheet' );

function seguraaonda_login_title() {
	return get_bloginfo( 'name' );
}

add_filter('login_headertext', 'seguraaonda_login_title');

function seguraaonda_login_logo(){
	return home_url(); // your URL here
}

add_filter('login_headerurl', 'seguraaonda_login_logo');

if ( $GLOBALS['pagenow'] === 'wp-login.php' && ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] === 'register' ) {
	function seguraaonda_register_message( $message ) {
		if ( !empty($message) ){
			$html='<p>Para colaborar com o guia e cadastrar uma iniciativa é necessário criar uma conta abaixo. Se você deseja fazer apenas uma consulta, vá direto para os <a href="/foruns">Fóruns</a>.</p>
			<p>Ao criar a conta você automaticamente concorda com nossos <a href="/foruns/topico/termos-de-uso/">Termos de uso</a> e <a href="/politica-de-privacidade/">Política de Privacidade</a>. Aconselhamos que você tire um tempo para lê-los antes de se cadastrar.</p>';
			return $html;
		} else {
			return $message;
		}
	}

	add_filter( 'login_message', 'seguraaonda_register_message' );
}

//identify new user
function seguraaonda_new_user($user_id) { 
	add_user_meta( $user_id, '_new_user', '1' );
}
add_action( 'user_register', 'seguraaonda_new_user');
// redirect after login
function seguraaonda_check_login_redirect($user_login, $user) {
	$logincontrol = get_user_meta($user->ID, '_new_user', 'TRUE');

	if ( $logincontrol ) {
		//set the user to old
		update_user_meta( $user->ID, '_new_user', '0' );
		//Do the redirects or whatever you need to do for the first login
		wp_redirect( 'http://seguraaonda.com.br/foruns/topico/boas-vindas/', 302 ); exit;
	}
	else {
				//redirect subscribers to forum home
		function seguraaonda_login_redirect( $url, $request, $user ){
			if( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
				if( $user->has_cap( 'subscriber' ) ) {
					$url = home_url('/novo-topico');
				} else {
					$url = admin_url();
				}
			}
			return $url;
		}

		add_filter('login_redirect', 'seguraaonda_login_redirect', 10, 3 );

	}

}
add_action('wp_login', 'seguraaonda_check_login_redirect', 10, 2);

// Tag field helper text
function seguraaonda_topic_tags_helper() {
	$html = '<div class="bbp-template-notice">
	<ul>
	<li><p>Separe as tags com vírgulas. Não é necessário adicionar o símbolo <b>#</b>.</p></li>
	</ul>
	</div>';
	echo $html;
}
add_action('bbp_theme_before_topic_form_tags','seguraaonda_topic_tags_helper');

//Add logout link to user profile page
function seguraaonda_user_profile_logout_link() {
	if ( bbp_is_single_user_profile() || bbp_is_user_home() || bbp_is_topics_created() || bbp_is_replies_created() || bbp_is_favorites() || bbp_is_subscriptions() ) {

		$user_id = get_current_user_id();
		$displayed_user_id = bbp_get_user_id();

		if ($user_id == 0){
			return;
		} elseif ( $user_id == $displayed_user_id ) {
			return bbp_logout_link();
		}
	}
}
add_action('bbp_template_after_user_details_menu_items', 'seguraaonda_user_profile_logout_link');

//Add ACF setting for google maps api key
function seguraaonda_acf_init() {
	acf_update_setting('google_api_key', 'AIzaSyCIOOogfsVQsOF3VSl6RH928JX8chhFwEg');
}
add_action('acf/init', 'seguraaonda_acf_init');

//Display topic location in topic single
function seguraaonda_display_location() {
	$location = get_field('localizacao');
	if( $location ) {

	// Loop over segments and construct HTML.
		$address = '';
		foreach( array('street_name', 'street_number', 'city', 'state', 'post_code', 'country') as $i => $k ) {
			if( isset( $location[ $k ] ) ) {
				$address .= sprintf( '<span class="sao-%s">%s</span>, ', $k, $location[ $k ] );
			}
		}

	// Trim trailing comma.
		$address = trim( $address, ', ' );

	// Display HTML.
		echo '<p class="sao-location"><strong>Localidade:</strong> ' . $address . '.</p>';
	}
}
add_action('bbp_template_before_single_topic', 'seguraaonda_display_location');