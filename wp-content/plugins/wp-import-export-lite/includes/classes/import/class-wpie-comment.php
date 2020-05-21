<?php

namespace wpie\import\comment;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-engine.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-engine.php');
}

class WPIE_Comment extends \wpie\import\engine\WPIE_Import_Engine {

        protected $import_type = "comment";
        protected $post_id = 0;

        public function process_import_data() {

                global $wpdb;

                $this->search_post_item();

                if ( $this->post_id == 0 ) {

                        $this->set_log( "<strong>" . __( 'ERROR', 'wp-import-export-lite' ) . '</strong> : ' . __( 'Post not found', 'wp-import-export-lite' ) );

                        $this->process_log[ 'skipped' ] ++;

                        $this->process_log[ 'imported' ] ++;

                        return true;
                }

                if ( $this->is_update_field( "post_id" ) ) {

                        $this->wpie_final_data[ 'comment_post_ID' ] = $this->post_id;
                }

                if ( $this->is_update_field( "author" ) ) {
                        $this->wpie_final_data[ 'comment_author' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_author' ) );
                }
                if ( $this->is_update_field( "author_email" ) ) {
                        $this->wpie_final_data[ 'comment_author_email' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_author_email' ) );
                }
                if ( $this->is_update_field( "author_url" ) ) {
                        $this->wpie_final_data[ 'comment_author_url' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_author_url' ) );
                }
                if ( $this->is_update_field( "author_ip" ) ) {
                        $this->wpie_final_data[ 'comment_author_IP' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_author_ip' ) );
                }
                if ( $this->is_update_field( "date" ) ) {
                        $comment_date = wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_date' ) );

                        if ( empty( trim( $comment_date ) ) || strtotime( $comment_date ) === false ) {
                                $comment_date = current_time( 'mysql' );
                        }

                        $this->wpie_final_data[ 'comment_date' ] = date( 'Y-m-d H:i:s', strtotime( $comment_date ) );
                }
                if ( $this->is_update_field( "content" ) ) {
                        $this->wpie_final_data[ 'comment_content' ] = wpie_sanitize_textarea( $this->get_field_value( 'wpie_item_comment_content' ) );
                }
                if ( $this->is_update_field( "karma" ) ) {
                        $this->wpie_final_data[ 'comment_karma' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_karma' ) );
                }
                if ( $this->is_update_field( "approved" ) ) {
                        $this->wpie_final_data[ 'comment_approved' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_approved' ) );
                }
                if ( $this->is_update_field( "agent" ) ) {
                        $this->wpie_final_data[ 'comment_agent' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_agent' ) );
                }
                if ( $this->is_update_field( "type" ) ) {
                        $this->wpie_final_data[ 'comment_type' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_type' ) );
                }
                if ( $this->is_update_field( "parent" ) ) {
                        $this->wpie_final_data[ 'comment_parent' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_parent' ) );
                }

                $this->wpie_final_data = apply_filters( 'wpie_before_comment_import', $this->wpie_final_data, $this->wpie_import_option, $this->wpie_import_record );

                if ( $this->is_new_item ) {

                        $this->item_id = wp_insert_comment( $this->wpie_final_data );

                        $this->process_log[ 'imported' ] ++;

                        if ( $this->item_id === false ) {

                                $this->set_log( "<strong>" . __( 'ERROR', 'wp-import-export-lite' ) . '</strong> : ' . __( 'Fail to insert comment', 'wp-import-export-lite' ) );

                                $this->process_log[ 'skipped' ] ++;

                                return true;
                        }

                        $this->process_log[ 'created' ] ++;
                } else {

                        $this->wpie_final_data[ 'comment_ID' ] = $this->existing_item_id;

                        $is_success = wp_update_comment( $this->wpie_final_data );

                        $this->item_id = $this->existing_item_id;

                        $this->process_log[ 'imported' ] ++;

                        if ( $is_success == 0 ) {

                                $this->set_log( "<strong>" . __( 'ERROR', 'wp-import-export-lite' ) . '</strong> : ' . __( 'Fail to Update comment', 'wp-import-export-lite' ) );

                                $this->process_log[ 'skipped' ] ++;

                                return true;
                        }
                        unset( $is_success );

                        $this->process_log[ 'updated' ] ++;
                }

                if ( $this->backup_service !== false && $this->is_new_item ) {
                        $this->backup_service->create_backup( $this->item_id, true );
                }

                $wpdb->update( $wpdb->prefix . "wpie_template", array( 'last_update_date' => current_time( 'mysql' ),
                        'process_log'      => maybe_serialize( $this->process_log ) ), array(
                        'id' => $this->wpie_import_id ) );

                do_action( 'wpie_after_comment_import', $this->item_id, $this->wpie_final_data, $this->wpie_import_option );

                if ( $this->is_update_field( "cf" ) ) {

                        $this->wpie_import_cf();
                }

                return $this->item_id;
        }

        protected function search_duplicate_item() {

                global $wpdb;

                $wpie_duplicate_indicator = strtolower( trim( wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic', true ) ) ) );

                if ( $wpie_duplicate_indicator === "id" ) {

                        $duplicate_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_id' ) ) );

                        if ( $duplicate_id > 0 ) {

                                $comment = get_comment( $duplicate_id );

                                if ( ! empty( $comment ) ) {
                                        $this->existing_item_id = $duplicate_id;
                                }

                                unset( $comment );
                        }
                        unset( $duplicate_id );
                } elseif ( $wpie_duplicate_indicator === "content" ) {

                        $content = wpie_sanitize_textarea( $this->get_field_value( 'wpie_item_comment_content' ) );

                        if ( ! empty( $content ) ) {

                                $comment_id = $wpdb->get_var( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_content IN (%s,%s) ORDER BY `comment_ID` ASC limit 0,1", $content, preg_replace( '%[ \\t\\n]%', '', $content ) ) );

                                if ( $comment_id && $comment_id > 0 ) {
                                        $this->existing_item_id = absint( $comment_id );
                                }
                                unset( $comment_id );
                        }
                        unset( $content );
                } elseif ( $wpie_duplicate_indicator === "cf" ) {

                        $meta_key = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_key' ) );

                        $meta_val = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_value' ) );

                        if ( ! empty( $meta_key ) ) {

                                $args = array(
                                        'number'     => 1,
                                        'offset'     => 0,
                                        'fields'     => "ids",
                                        'meta_key'   => $meta_key,
                                        'meta_value' => $meta_val,
                                        'orderby'    => 'comment_ID',
                                        'order'      => 'ASC '
                                );

                                $comments = get_comments( $args );

                                if ( ! empty( $comments ) && ! is_wp_error( $comments ) ) {
                                        foreach ( $comments as $comment ) {
                                                $this->existing_item_id = $comment->comment_ID;
                                                break;
                                        }
                                }
                                unset( $comments, $args );
                        }

                        unset( $meta_key, $meta_val );
                }

                unset( $wpie_duplicate_indicator );
        }

        protected function search_post_item() {

                global $wpdb;

                $post_types = $this->get_field_value( 'wpie_comment_parent_include_post_types' );

                if ( empty( $post_types ) ) {

                        unset( $post_types );

                        return;
                }

                $post_indicator = strtolower( trim( $this->get_field_value( 'wpie_item_search_post_based_on', true ) ) ) === "id" ? "id" : "title";

                if ( $post_indicator === "id" ) {

                        $post_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_post_id' ) ) );

                        if ( $post_id > 0 ) {
                                $_post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 0,1", $post_id ) );

                                if ( $_post && absint( $_post ) > 0 ) {
                                        $this->post_id = absint( $_post );
                                }
                                unset( $_post );
                        }
                        unset( $post_id );
                } else {


                        $title = $this->get_field_value( "wpie_item_comment_post_title" );

                        if ( ! empty( $title ) ) {
                                $_post = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "SELECT ID FROM " . $wpdb->posts . "
                                WHERE
                                    post_type IN ('" . implode( "','", $post_types ) . "')
                                    AND ID != 0
                                    AND post_title = %s
                                LIMIT 1
                                ", $title
                                        )
                                );


                                if ( $_post && absint( $_post ) > 0 ) {
                                        $this->post_id = absint( $_post );
                                }
                                unset( $_post );
                        }

                        unset( $title );
                }
                unset( $post_indicator );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
