<?php

namespace wpie\import\upload\url;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php' ) ) {
        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php');
}

class WPIE_URL_Upload extends \wpie\import\upload\WPIE_Upload {

        public function __construct() {
                
        }

        public function wpie_download_file_from_url( $wpie_import_id = 0, $file_url = "" ) {

                if ( empty( $file_url ) ) {

                        return new \WP_Error( 'wpie_import_error', __( 'File URL is empty', 'wp-import-export-lite' ) );
                } elseif ( ! is_dir( WPIE_UPLOAD_IMPORT_DIR ) || ! wp_is_writable( WPIE_UPLOAD_IMPORT_DIR ) ) {

                        return new \WP_Error( 'wpie_import_error', __( 'Uploads folder is not writable', 'wp-import-export-lite' ) );
                }

                $fileName = preg_replace( "/[^a-z0-9\_\-\.]/i", '', basename( parse_url( $file_url, PHP_URL_PATH ) ) );

                $newfiledir = parent::wpie_create_safe_dir_name( $fileName );

                $custom_query = false;

                if ( ! preg_match( '%\W(xml|zip|csv|xls|xlsx|xml|ods|txt|json)$%i', trim( $fileName ) ) ) {
                        $fileName = $fileName . ".tmp";
                        $custom_query = true;
                }

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original" );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse" );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse/chunks" );

                $filePath = WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName;

                chmod( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/", 0755 );

                $response = wp_safe_remote_get( $file_url, array ( 'timeout' => 3000, 'stream' => true, 'filename' => $filePath ) );

                if ( is_wp_error( $response ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        unset( $wpie_import_id, $file_url, $fileName, $newfiledir, $filePath );

                        return $response;
                }

                if ( 200 != wp_remote_retrieve_response_code( $response ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        unset( $wpie_import_id, $file_url, $fileName, $newfiledir, $filePath );

                        return new \WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
                }

                $content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );

                if ( $content_md5 ) {

                        $md5_check = verify_file_md5( $filePath, $content_md5 );

                        if ( is_wp_error( $md5_check ) ) {

                                if ( file_exists( $filePath ) ) {
                                        unlink( $filePath );
                                }

                                unset( $wpie_import_id, $file_url, $fileName, $newfiledir, $filePath, $content_md5 );

                                return $md5_check;
                        }

                        unset( $md5_check );
                }

                if ( $custom_query ) {

                        $header_content_disposition = wp_remote_retrieve_header( $response, 'content-disposition' );

                        $regex = '/.*?filename=(?<fn>[^\s]+|\x22[^\x22]+\x22)\x3B?.*$/m';

                        $new_file_data = null;

                        if ( preg_match( $regex, $header_content_disposition, $new_file_data ) ) {

                                if ( isset( $new_file_data[ 'fn' ] ) && ! empty( $new_file_data[ 'fn' ] ) && preg_match( '/^.*\.(xml|zip|csv|xls|xlsx|xml|ods|txt|json)$/i', strtolower( trim( $new_file_data[ 'fn' ] ) ) ) ) {
                                        $fileName = preg_replace( "/[^a-z0-9\_\-\.]/i", '', $new_file_data[ 'fn' ] );
                                        rename( $filePath, WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName );
                                }
                        }
                }

                unset( $file_url, $filePath, $content_md5, $custom_query, $response );

                return parent::wpie_manage_import_file( $fileName, $newfiledir, $wpie_import_id );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
