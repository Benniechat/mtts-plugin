<?php
namespace MttsLms\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Security {

    /**
     * Deeply sanitize an array or object.
     * Useful for JSON data to prevent NoSQL-style injection or unexpected keys.
     */
    public static function sanitize_deep( $data ) {
        if ( is_array( $data ) ) {
            foreach ( $data as $key => $value ) {
                $data[ self::sanitize_key( $key ) ] = self::sanitize_deep( $value );
            }
        } elseif ( is_object( $data ) ) {
            foreach ( get_object_vars( $data ) as $key => $value ) {
                $data->{self::sanitize_key( $key )} = self::sanitize_deep( $value );
            }
        } else {
            $data = sanitize_text_field( $data );
        }

        return $data;
    }

    /**
     * Sanitize a key to prevent operator injection ($ne, etc) for JSON/NoSQL contexts.
     */
    public static function sanitize_key( $key ) {
        // Remove any characters that might be used as operators in some NoSQL systems ($)
        // or just keep it alphanumeric/underscores/dashes.
        $key = preg_replace( '/^\$/', '', (string)$key ); // Strip leading $
        return preg_replace( '/[^a-zA-Z0-9_\-]/', '', $key );
    }

    /**
     * Verify a request is both authentic and from a valid source.
     */
    public static function check_request( $nonce_action ) {
        if ( ! is_user_logged_in() ) {
            return false;
        }
        return check_admin_referer( $nonce_action );
    }

    /**
     * Strictly verify nonce and return error if fails (for controllers)
     */
    public static function verify_nonce( $action, $query_arg = '_wpnonce' ) {
        if ( ! isset( $_REQUEST[ $query_arg ] ) || ! wp_verify_nonce( $_REQUEST[ $query_arg ], $action ) ) {
            wp_die( 'Security check failed.' );
        }
        return true;
    }
}
