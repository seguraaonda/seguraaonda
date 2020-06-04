<?php

namespace wpie\import\images;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

if ( file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
}

class WPIE_Images extends \wpie\import\base\WPIE_Import_Base {

        private $target_dir;
        private $images = [];
        private $attach = [];
        private $gallary = [];
        private $image_options = [];

        public function __construct( $item_id = 0, $is_new_item = true, $wpie_import_option = array(), $wpie_import_record = array() ) {

                $this->item_id = $item_id;

                $this->is_new_item = $is_new_item;

                $this->wpie_import_option = $wpie_import_option;

                $this->wpie_import_record = $wpie_import_record;
        }

        public function prepare_images() {

                if ( $this->item_id ) {

                        $wp_uploads = wp_upload_dir();

                        $this->target_dir = $wp_uploads[ 'path' ];

                        $this->prepare_image_option();

                        if ( ! $this->is_new_item && trim( strtolower( wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_images' ) ) ) ) === "all" ) {
                                $this->prepare_old_attch();
                        }

                        $image_option = trim( strtolower( wpie_sanitize_field( $this->get_field_value( 'wpie_item_image_option', true ) ) ) );

                        $this->process_images( $image_option );

                        if ( ! $this->is_new_item && wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_images' ) ) == "all" ) {
                                $this->remove_old_attch( "images" );
                        }

                        $this->set_gallary_images();

                        unset( $wp_uploads, $image_option );
                }

                return array( "as_draft" => $this->as_draft, "import_log" => $this->import_log );
        }

        private function is_search_existing() {

                return absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_search_existing_images', true ) ) ) === 1;
        }

        private function process_images( $method = "media_library" ) {

                $method = ("download_images" === $method) ? "url" : (("local_images" === $method) ? "local" : $method);

                $image_data = wpie_sanitize_textarea( $this->get_field_value( 'wpie_item_image_' . $method ) );

                if ( empty( $image_data ) ) {
                        return true;
                }

                $data = explode( "\n", $image_data );

                if ( ( ! isset( $data[ 1 ] )) || ( isset( $data[ 1 ] ) && empty( $data[ 1 ] )) ) {

                        $delim = wpie_sanitize_field( $this->get_field_value( 'wpie_item_image_' . $method . '_delim' ) );

                        $data = explode( empty( $delim ) ? "|" : $delim, $image_data );

                        unset( $delim );
                }

                if ( empty( $data ) || ! is_array( $data ) ) {
                        return true;
                }

                foreach ( $data as $index => $image ) {

                        if ( empty( $image ) ) {
                                continue;
                        }
                        $attch_id = null;

                        $existing_image = $this->get_existing_image( $image );

                        if ( $existing_image !== false && absint( $existing_image ) > 0 ) {
                                $attch_id = $existing_image;
                        }

                        if ( empty( $attch_id ) && (($method === "media_library") || ($method !== "media_library" && $this->is_search_existing() ) ) ) {

                                $media_id = $this->wpie_get_image_from_gallery( $image );

                                if ( $media_id !== false ) {
                                        $attch_id = absint( $media_id );
                                }
                        }

                        if ( empty( $attch_id ) ) {
                                $temp_id = false;
                                if ( $method === "local" ) {
                                        $temp_id = $this->wpie_get_image_from_local( $image );
                                } elseif ( $method === "url" ) {
                                        $temp_id = $this->wpie_get_image_from_url( $image );
                                }
                                if ( $temp_id !== false ) {
                                        $attch_id = absint( $temp_id );
                                } else {
                                        $this->set_as_draft();
                                }
                        }

                        if ( ! empty( $attch_id ) ) {

                                $this->images[] = absint( $attch_id );
                                $this->wpie_set_image_meta( absint( $attch_id ), $index );
                        }
                }
        }

        private function prepare_image_option() {

                $this->prepare_image_meta();

                $this->image_options = array();

                $this->image_options[ 'new_name' ] = array();

                if ( absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_image_rename', true ) ) ) === 1 ) {

                        $new_names = wpie_sanitize_field( $this->get_field_value( 'wpie_item_image_new_name' ) );

                        if ( empty( $new_names ) ) {

                                $this->image_options[ 'new_name' ] = explode( ",", $new_names );
                        }
                        unset( $new_names );
                }

                $this->image_options[ 'new_ext' ] = array();

                if ( absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_change_ext', true ) ) ) === 1 ) {

                        $new_ext = wpie_sanitize_field( $this->get_field_value( 'wpie_item_new_ext' ) );

                        if ( empty( $new_ext ) ) {

                                $this->image_options[ 'new_ext' ] = explode( ",", $new_ext );
                        }
                        unset( $new_ext );
                }
        }

        private function get_image_meta_values( $field = "" ) {

                $meta = [];

                if ( absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_set_image_' . $field, true ) ) ) !== 1 ) {
                        return $meta;
                }

                $value = wpie_sanitize_textarea( $this->get_field_value( 'wpie_item_image_' . $field ) );

                if ( empty( $value ) ) {
                        return $meta;
                }

                $data = explode( "\n", $value );

                if ( ( ! isset( $data[ 1 ] )) || ( isset( $data[ 1 ] ) && empty( $data[ 1 ] )) ) {
                        $delim = wpie_sanitize_field( $this->get_field_value( 'wpie_item_set_image_' . $field . '_delim' ) );

                        $meta = explode( $delim != "" ? $delim : ",", $value );

                        unset( $delim );
                } else {
                        $meta = $data;
                }
                unset( $value, $data );


                return $meta;
        }

        private function prepare_image_meta() {

                $this->image_meta = [
                        'title'   => $this->get_image_meta_values( "title" ),
                        'caption' => $this->get_image_meta_values( "caption" ),
                        'alt'     => $this->get_image_meta_values( "alt" ),
                        'desc'    => $this->get_image_meta_values( "description" )
                ];
        }

        private function wpie_set_image_meta( $attch_id = 0, $index = 0 ) {

                $update_attch_meta = array();

                if ( isset( $this->image_meta[ 'title' ][ $index ] ) ) {

                        $update_attch_meta[ 'post_title' ] = $this->image_meta[ 'title' ][ $index ];
                }
                if ( isset( $this->image_meta[ 'caption' ][ $index ] ) ) {

                        $update_attch_meta[ 'post_excerpt' ] = $this->image_meta[ 'caption' ][ $index ];
                }
                if ( isset( $this->image_meta[ 'alt' ][ $index ] ) ) {

                        update_post_meta( $attch_id, '_wp_attachment_image_alt', $this->image_meta[ 'alt' ][ $index ] );
                }
                if ( isset( $this->image_meta[ 'desc' ][ $index ] ) ) {

                        $update_attch_meta[ 'post_content' ] = $this->image_meta[ 'desc' ][ $index ];
                }

                if ( ! empty( $update_attch_meta ) ) {

                        global $wpdb;

                        $wpdb->update( $wpdb->posts, $update_attch_meta, array( 'ID' => $attch_id ) );
                }

                unset( $update_attch_meta );
        }

        private function wpie_get_image_from_gallery( $image_name = "" ) {

                if ( empty( $image_name ) ) {
                        return false;
                }
                global $wpdb;


                $attachment = $wpdb->get_var( $wpdb->prepare( "SELECT post.ID FROM {$wpdb->posts} post INNER JOIN {$wpdb->postmeta} meta ON post.ID = meta.post_id WHERE post.post_type = 'attachment' AND meta.meta_key = %s AND (meta.meta_value = %s OR meta.meta_value LIKE %s) LIMIT 0,1;", '_wp_attached_file', basename( $image_name ), "%/" . basename( $image_name ) ) );

                if ( $attachment && absint( $attachment ) > 0 ) {
                        return $attachment;
                }

                $attachment = $wpdb->get_var( $wpdb->prepare( "SELECT post.ID FROM {$wpdb->posts} post INNER JOIN {$wpdb->postmeta} meta ON post.ID = meta.post_id WHERE post.post_type = 'attachment' AND meta.meta_key = %s AND (meta.meta_value = %s OR meta.meta_value LIKE %s) LIMIT 0,1;", '_wp_attached_file', sanitize_file_name( basename( $image_name ) ), "%/" . sanitize_file_name( basename( $image_name ) ) ) );

                if ( $attachment && absint( $attachment ) > 0 ) {
                        return $attachment;
                }

                $wp_filetype = wp_check_filetype( basename( $image_name ) );

                if ( isset( $wp_filetype[ 'type' ] ) && ! empty( $wp_filetype[ 'type' ] ) ) {
                        $name = pathinfo( $image_name, PATHINFO_FILENAME );
                        $attch = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE (post_title = %s OR post_title = %s OR post_name = %s) AND post_type = 'attachment' AND post_mime_type = %s;", $name, $name, $name, $wp_filetype[ 'type' ] ) );
                        if ( $attch && absint( $attch ) > 0 ) {
                                return $attch;
                        }
                }
                return false;
        }

        private function wpie_get_image_from_local( $filename = "" ) {

                if ( ( ! wp_is_writable( $this->target_dir )) || empty( $filename ) ) {
                        return;
                }

                $file = WPIE_UPLOAD_TEMP_DIR . "/" . $filename;

                $file_info = pathinfo( $filename );

                $rename = wpie_sanitize_field( $this->get_field_value( 'wpie_item_image_rename', true ) );

                if ( $rename == 1 ) {

                        $new_filename = wpie_sanitize_field( $this->get_field_value( 'wpie_item_image_new_name' ) );

                        if ( ! empty( $new_filename ) ) {
                                $filename = sanitize_file_name( $new_filename . '.' . $file_info[ 'extension' ] );
                        }
                        unset( $new_filename );
                }

                unset( $rename );

                $change_ext = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_change_ext' ) ) );

                if ( $change_ext === 1 ) {

                        $new_file_ext = wpie_sanitize_field( $this->get_field_value( 'wpie_item_new_ext' ) );

                        if ( ! empty( $new_file_ext ) ) {

                                $filename = sanitize_file_name( $file_info[ 'filename' ] . '.' . $new_extension );
                        }
                        unset( $new_file_ext );
                }

                $upload_file = wp_upload_bits( $filename, null, file_get_contents( $file ) );

                unset( $file, $file_info, $change_ext );

                if ( ! $upload_file[ 'error' ] ) {

                        $wp_filetype = wp_check_filetype( $filename, null );


                        $attachment = array(
                                'post_mime_type' => $wp_filetype[ 'type' ],
                                'post_parent'    => $this->item_id,
                                'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
                                'post_content'   => '',
                                'post_status'    => 'inherit',
                                'post_author'    => $this->get_post_user()
                        );

                        $attachment_id = wp_insert_attachment( $attachment, $upload_file[ 'file' ], $this->item_id );

                        if ( ! is_wp_error( $attachment_id ) && absint( $attachment_id ) > 0 ) {

                                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file[ 'file' ] );

                                wp_update_attachment_metadata( $attachment_id, $attachment_data );

                                unset( $attachment_data );
                        }

                        unset( $attachment, $wp_filetype, $upload_file, $filename );

                        return $attachment_id;
                }

                $this->import_log[] = '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . __( 'Image Upload failed', 'wp-import-export-lite' );

                unset( $upload_file, $filename );
        }

        private function get_post_user() {

                $user_id = 0;
                if ( ! empty( $this->item_id ) ) {
                        $post = get_post( $this->item_id );
                        if ( $post && isset( $post->post_author ) && ! empty( $post->post_author ) ) {
                                $user_id = $post->post_author;
                        }
                        unset( $post );
                }
                if ( $user_id === 0 ) {
                        if ( ! empty( $this->import_username ) ) {

                                $user = get_user_by( "login", $this->import_username );

                                if ( $user && isset( $user->ID ) ) {
                                        $user_id = $user->ID;
                                }
                                unset( $user );
                        }
                }
                if ( $user_id === 0 ) {

                        $current_user = wp_get_current_user();

                        if ( $current_user && isset( $current_user->ID ) ) {
                                $user_id = $current_user->ID;
                        }
                        unset( $current_user );
                }
                return $user_id;
        }

        private function wpie_get_image_from_url( $image_url = "" ) {

                if ( empty( $image_url ) ) {
                        return false;
                }

                if ( file_exists( ABSPATH . 'wp-admin/includes/media.php' ) ) {
                        require_once(ABSPATH . 'wp-admin/includes/media.php');
                }
                if ( file_exists( ABSPATH . 'wp-admin/includes/file.php' ) ) {
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                }

                $attch = $this->download_image( $image_url );

                if ( is_wp_error( $attch ) ) {
                        $this->import_log[] = '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $attch->get_error_message();
                        return false;
                }
                $author_id = absint( $this->get_post_user() );

                if ( $author_id > 0 ) {
                        global $wpdb;
                        $wpdb->update( $wpdb->posts, [ "post_author" => $author_id ], [ 'ID' => $attch ] );
                }
                return $attch;
        }

        private function download_image( $file_url = "" ) {

                if ( empty( $file_url ) ) {
                        return new \WP_Error( 'http_404', __( 'Empty Image URL', 'wp-import-export-lite' ) );
                }

                $fileName = time() . rand() . ".tmp";

                $filePath = WPIE_UPLOAD_TEMP_DIR . "/" . $fileName;

                $response = wp_safe_remote_get( $file_url, array( 'timeout' => 3000, 'stream' => true, 'filename' => $filePath ) );

                if ( is_wp_error( $response ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        return $response;
                }

                if ( 200 != wp_remote_retrieve_response_code( $response ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        return new \WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
                }

                $content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );

                if ( $content_md5 ) {

                        $md5_check = verify_file_md5( $filePath, $content_md5 );

                        if ( is_wp_error( $md5_check ) ) {

                                if ( file_exists( $filePath ) ) {
                                        unlink( $filePath );
                                }

                                return $md5_check;
                        }

                        unset( $md5_check );
                }

                $original_name = $this->get_filename_from_headers( $response, $file_url );

                if ( is_wp_error( $original_name ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        return $original_name;
                }

                preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', strtolower( trim( $original_name ) ), $matches );

                if ( ! $matches ) {
                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }
                        return new WP_Error( 'invalid_image', __( 'Invalid image Extension', 'wp-import-export-lite' ) );
                }

                if ( $this->is_search_existing() ) {

                        $media_id = $this->wpie_get_image_from_gallery( $original_name );

                        if ( $media_id !== false ) {
                                if ( file_exists( $filePath ) ) {
                                        unlink( $filePath );
                                }
                                return absint( $media_id );
                        }
                }

                $file_array = [ "name" => $original_name, "tmp_name" => $filePath ];

                $id = media_handle_sideload( $file_array, $this->item_id );

                // If error storing permanently, unlink.
                if ( is_wp_error( $id ) ) {
                        @unlink( $file_array[ 'tmp_name' ] );
                        return $id;
                }

                // Store the original attachment source in meta.
                add_post_meta( $id, '_source_url', $file_url );

                return $id;
        }

        private function get_filename_from_headers( $response = "", $file_url = "" ) {

                $header_content_disposition = wp_remote_retrieve_header( $response, 'content-disposition' );

                $default_filename = basename( parse_url( $file_url, PHP_URL_PATH ) );

                if ( empty( $header_content_disposition ) ) {
                        return $default_filename;
                }

                $regex = '/.*?filename=(?<fn>[^\s]+|\x22[^\x22]+\x22)\x3B?.*$/m';

                $new_file_data = null;

                $original_name = "";

                if ( preg_match( $regex, $header_content_disposition, $new_file_data ) ) {

                        if ( isset( $new_file_data[ 'fn' ] ) && ! empty( $new_file_data[ 'fn' ] ) ) {
                                $wp_filetype = wp_check_filetype( $new_file_data[ 'fn' ] );
                                if ( isset( $wp_filetype[ 'ext' ] ) && ( ! empty( $wp_filetype[ 'ext' ] )) && isset( $wp_filetype[ 'type' ] ) && ( ! empty( $wp_filetype[ 'type' ] )) ) {
                                        $original_name = $new_file_data[ 'fn' ];
                                }
                        }
                }

                if ( empty( $original_name ) ) {

                        $regex = '/.*filename=([\'\"]?)([^\"]+)\1/';

                        if ( preg_match( $regex, $header_content_disposition, $new_file_data ) ) {

                                if ( isset( $new_file_data[ '2' ] ) && ! empty( $new_file_data[ '2' ] ) ) {
                                        $wp_filetype = wp_check_filetype( $new_file_data[ '2' ] );
                                        if ( isset( $wp_filetype[ 'ext' ] ) && ( ! empty( $wp_filetype[ 'ext' ] )) && isset( $wp_filetype[ 'type' ] ) && ( ! empty( $wp_filetype[ 'type' ] )) ) {
                                                $original_name = $new_file_data[ '2' ];
                                        }
                                }
                        }
                }
                if ( empty( $original_name ) ) {
                        $original_name = $default_filename;
                }

                return preg_replace( "/[^a-z0-9\_\-\.]/i", '', preg_replace( '#[ -]+#', '-', $original_name ) );
        }

        private function prepare_old_attch() {
                $this->attach = get_posts(
                        [
                                'post_parent' => $this->item_id,
                                'post_type'   => 'attachment',
                                'numberposts' => -1,
                                'post_status' => null,
                                "fields"      => "ids"
                        ]
                );
        }

        private function remove_old_attch( $type = 'images' ) {

                if ( $type === 'images' && has_post_thumbnail( $this->item_id ) ) {
                        delete_post_thumbnail( $this->item_id );
                }

                $ids = array();

                if ( ! empty( $this->attach ) ) {

                        $keep_images = absint( (wpie_sanitize_field( $this->get_field_value( 'wpie_item_keep_images', true ) ) ) === 1 );

                        $attachments = array_diff( $this->attach, $this->images );

                        if ( ! empty( $attachments ) ) {

                                foreach ( $attachments as $attach ) {

                                        if ( ($type === 'files' && ! wp_attachment_is_image( $attach )) || ($type === 'images' && wp_attachment_is_image( $attach )) ) {

                                                if ( $keep_images === false ) {
                                                        wp_delete_attachment( $attach, true );
                                                } else {
                                                        $ids[] = $attach;
                                                }
                                        }
                                }
                        }
                        unset( $attachments, $keep_images );

                        global $wpdb;

                        if ( ! empty( $ids ) ) {

                                $ids_string = implode( ',', array_map( "absint", $ids ) );

                                $wpdb->query( "UPDATE $wpdb->posts SET post_parent = 0 WHERE post_type = 'attachment' AND ID IN ( $ids_string )" );

                                unset( $ids_string );

                                foreach ( $ids as $att_id ) {
                                        clean_attachment_cache( $att_id );
                                }
                        }
                }

                return $ids;
        }

        private function set_as_draft() {
                if ( ! $this->as_draft && absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_unsuccess_set_draft' ) ) ) === 1 ) {
                        $this->as_draft = true;
                }
        }

        private function set_gallary_images() {

                $this->gallary = $this->images;

                if ( ! empty( $this->images ) && absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_first_imaege_is_featured' ) ) ) === 1 ) {

                        $image_id = array_shift( $this->gallary );

                        $thumbnail_id = set_post_thumbnail( $this->item_id, $image_id );

                        if ( $thumbnail_id === false ) {

                                $this->set_as_draft();

                                $this->import_log[] = '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . __( 'Error on try to set Featured Image', 'wp-import-export-lite' );
                        }
                        unset( $image_id, $thumbnail_id );
                }
                update_post_meta( $this->item_id, '_product_image_gallery', (empty( $this->gallary ) ? "" : implode( ",", $this->gallary ) ) );
        }

        private function get_existing_image( $image = "" ) {

                if ( empty( $this->attach ) || empty( $image ) ) {
                        return false;
                }

                $attach_id = 0;

                $name = sanitize_file_name( basename( $image ) );

                foreach ( $this->attach as $id ) {

                        $file = get_post_meta( $id, "_wp_attached_file", true );

                        if ( ! empty( $file ) && $name === sanitize_file_name( basename( $file ) ) || get_the_title( $id ) == pathinfo( $name, PATHINFO_FILENAME ) ) {
                                $attach_id = $id;
                                break;
                        }
                }
                if ( $attach_id !== 0 ) {
                        return $attach_id;
                }
                return false;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
