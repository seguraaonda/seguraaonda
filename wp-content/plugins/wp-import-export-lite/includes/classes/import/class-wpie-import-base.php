<?php

namespace wpie\import\base;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

abstract class WPIE_Import_Base {

        protected $wpie_import_id = 0;
        protected $wpie_import_option = array ();
        protected $import_log = array ();
        protected $process_log = array ();
        protected $wpie_import_record = array ();
        protected $is_new_item = true;
        protected $item_id = 0;
        protected $item;
        protected $existing_item_id = 0;
        protected $wpie_final_data = array ();
        protected $log_service = false;
        protected $backup_service = false;
        protected $import_type;
        protected $as_draft = false;
        protected $base_dir = false;
        protected $wpie_fileName = "wpie-import-data-";
        protected $addons = array ();
        protected $addon_error = false;
        protected $addon_log = array ();

        public function __construct() {
                
        }

        protected function get_item_id() {
                return $this->item_id;
        }

        protected function get_user_id() {

                if ( $this->import_type == "post" ) {

                        $id = wpie_sanitize_field( $this->get_field_value( "post_author" ) );

                        $user = get_user_by( "id", $id );

                        if ( $user !== false ) {
                                unset( $user );
                                return $id;
                        }

                        unset( $id, $user );
                }
                return get_current_user_id();
        }

        /**
         * Get field value from file or options
         *
         * @since 1.0.0
         *
         * @param string    $field          Field to get value
         * @param bool      $is_option      if true then get value from option
         * @param bool      $as_specified   if value is as_specified then get value from $field."_as_specified" filed value
         * @return mixed if is_option is true then value will get from direct option else from file.
         */
        protected function get_field_value( $field = "", $is_option = false, $as_specified = false ) {

                if ( empty( $field ) ) {
                        return "";
                }

                if ( $is_option ) {
                        $field_data = isset( $this->wpie_import_option[ $field ] ) ? $this->wpie_import_option[ $field ] : "";
                } elseif ( isset( $this->wpie_import_option[ $field ] ) && ! empty( $this->wpie_import_option[ $field ] ) ) {
                        $field_data = $this->get_field( $this->wpie_import_option[ $field ] );
                } else {
                        $field_data = "";
                }

                if ( $as_specified && $field_data == "as_specified" ) {

                        $field = isset( $this->wpie_import_option[ $field . "_as_specified_data" ] ) ? $this->wpie_import_option[ $field . "_as_specified_data" ] : "";

                        $field_data = $this->get_field( $field );
                }

                return $this->decode_special_char( wp_unslash( $field_data ) );
        }

        private function decode_special_char( $data ) {

                return $this->map_deep( $data, array ( __CLASS__, 'str_replace' ) );
        }

        public static function str_replace( $subject ) {

                if ( empty( $subject ) ) {
                        return "";
                }
                return str_replace( [ "&quot;", "&amp;" ], [ '"', '&' ], $subject );
        }

        /**
         * Maps a function to all non-iterable elements of an array or an object.
         *
         * This is similar to `array_walk_recursive()` but acts upon objects too.
         *
         * @since 1.4.0
         *
         * @param mixed    $value    The array, object, or scalar.
         * @param callable $callback The function to map onto $value.
         * @return mixed The value with the callback applied to all non-arrays and non-objects inside it.
         */
        private function map_deep( $value, $callback ) {

                if ( is_array( $value ) ) {
                        foreach ( $value as $index => $item ) {
                                $value[ $index ] = map_deep( $item, $callback );
                        }
                } elseif ( is_object( $value ) ) {
                        $object_vars = get_object_vars( $value );
                        foreach ( $object_vars as $property_name => $property_value ) {
                                $value->$property_name = map_deep( $property_value, $callback );
                        }
                } else {
                        $value = call_user_func( $callback, $value );
                }

                return $value;
        }

        public function get_field( $field = "" ) {

                if ( is_array( $field ) ) {
                        $field = array_map( array ( $this, "get_field" ), $field );
                } elseif ( is_array( $this->wpie_import_record ) && ! empty( $this->wpie_import_record ) ) {
                        $field = str_replace( array_keys( $this->wpie_import_record ), array_values( $this->wpie_import_record ), $field );
                }
                return $field;
        }

        protected function is_update_field( $field = "" ) {

                if ( empty( $field ) ) {
                        return false;
                }
                if ( $this->is_new_item ) {
                        return true;
                }

                if ( wpie_sanitize_field( $this->get_field_value( 'wpie_item_update', true ) ) == 'all' ) {
                        return true;
                }

                return absint( $this->get_field_value( "is_update_item_" . $field, true ) ) == 1;
        }

        protected function update_meta( $meta_key = "", $meta_val = "" ) {
                $meta_val = maybe_unserialize( $meta_val );
                if ( $this->import_type == "taxonomy" ) {
                        update_term_meta( $this->item_id, $meta_key, $meta_val );
                } elseif ( $this->import_type == "user" ) {
                        update_user_meta( $this->item_id, $meta_key, $meta_val );
                } elseif ( $this->import_type == "comment" ) {
                        update_comment_meta( $this->item_id, $meta_key, $meta_val );
                } else {
                        update_post_meta( $this->item_id, $meta_key, $meta_val );
                }
        }

        protected function get_meta( $meta_key = "", $is_single = false ) {

                if ( ! empty( $meta_key ) && ! empty( $this->item_id ) ) {
                        if ( $this->import_type == "taxonomy" ) {
                                return get_term_meta( $this->item_id, $meta_key, $is_single );
                        } elseif ( $this->import_type == "user" ) {
                                return get_user_meta( $this->item_id, $meta_key, $is_single );
                        } elseif ( $this->import_type == "comment" ) {
                                return get_comment_meta( $this->item_id, $meta_key, $is_single );
                        } else {
                                return get_post_meta( $this->item_id, $meta_key, $is_single );
                        }
                }
        }

        protected function remove_meta( $meta_key = "" ) {
                if ( ! empty( $meta_key ) && ! empty( $this->item_id ) ) {
                        if ( $this->import_type == "taxonomy" ) {
                                delete_term_meta( $this->item_id, $meta_key );
                        } elseif ( $this->import_type == "user" ) {
                                delete_user_meta( $this->item_id, $meta_key );
                        } elseif ( $this->import_type == "comment" ) {
                                delete_comment_meta( $this->item_id, $meta_key );
                        } else {
                                delete_post_meta( $this->item_id, $meta_key );
                        }
                }
        }

        protected function wpie_term_exists( $term, $taxonomy = '', $parent = null ) {

                return apply_filters( 'wpie_term_exists', term_exists( $term, $taxonomy, $parent ), $term, $taxonomy, $parent );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
