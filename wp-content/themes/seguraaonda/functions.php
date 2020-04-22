<?php
add_action( 'wp_enqueue_scripts', 'seguraaonda_enqueue_scripts' );
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
}

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

//add description after forums titles on forum index
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
	if ( is_user_logged_in() ) {
		return bbp_logout_link();
	}
}
add_action('bbp_template_after_user_details_menu_items', 'seguraaonda_user_profile_logout_link');

// Add address custom fields to bbpress topics on front end
function seguraaonda_topic_location_field() {
	$value = get_post_meta( bbp_get_topic_id(), 'topic-location', true);
	echo '<label for="topic-location">Location:</label><br>';
	echo "<input type='text' name='topic-location' value='".$value."'>";
}
add_action ( 'bbp_theme_after_topic_form_content', 'seguraaonda_topic_location_field');

function seguraaonda_save_topic_location_field() {
//Save and update the values from the front end
	if (isset($_POST) && $_POST['topic-location']!='' && function_exists( 'gmw_update_post_location' )) {

		update_post_meta( $topic_id, 'topic-location', $_POST['topic-location'] );
		
		$location = get_post_meta( $topic_id, 'topic-location', true );
	
		//run geocoder function
		gmw_update_post_location( $topic_id, $location );
	}
    
}
add_action ( 'bbp_new_topic', 'seguraaonda_save_topic_location_field', 10, 1 );
add_action ( 'bbp_edit_topic', 'seguraaonda_save_topic_location_field', 10, 1 );