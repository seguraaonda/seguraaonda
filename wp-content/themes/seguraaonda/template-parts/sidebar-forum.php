<div class="sidebar-wrapper">	
	<?php
		if ( ! is_user_logged_in() ) {
			if ( ! ( bbp_is_single_topic() || bbp_is_single_forum() ) ){
				bbp_get_template_part( 'form', 'user-login' );
			}
		} else { ?>
			<div class="bbp-logged-in widget">
				<div class="widget-content">
					<a href="<?php bbp_user_profile_url( bbp_get_current_user_id() ); ?>"><?php echo get_avatar( bbp_get_current_user_id(), '40' ); ?></a>
					<h4><?php bbp_user_profile_link( bbp_get_current_user_id() ); ?></h4>
					<ul>
						<li> <a href="<?php bbp_user_topics_created_url(bbp_get_current_user_id()); ?>" title="<?php printf( esc_attr__( "%s's Topics Started", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php esc_html_e( 'Topics Started', 'bbpress' ); ?></a></li>
						<li><a href="<?php bbp_user_replies_created_url(bbp_get_current_user_id()); ?>" title="<?php printf( esc_attr__( "%s's Replies Created", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php esc_html_e( 'Replies Created', 'bbpress' ); ?></a></li>
						<li><a href="<?php bbp_user_engagements_url(bbp_get_current_user_id()); ?>" title="<?php printf( esc_attr__( "%s's Engagements", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php esc_html_e( 'Engagements', 'bbpress' ); ?></a></li>
						<li><a href="<?php bbp_favorites_permalink(bbp_get_current_user_id()); ?>" title="<?php printf( esc_attr__( "%s's Favorites", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php esc_html_e( 'Favorites', 'bbpress' ); ?></a></li>
						<li><a href="<?php bbp_subscriptions_permalink(bbp_get_current_user_id()); ?>" title="<?php printf( esc_attr__( "%s's Subscriptions", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php esc_html_e( 'Subscriptions', 'bbpress' ); ?></a></li>
						<li><?php bbp_logout_link(); ?></li>
					</ul>
				</div>
			</div>
		<?php	
		}
		dynamic_sidebar( 'sidebar-3' );
	?>
</div>