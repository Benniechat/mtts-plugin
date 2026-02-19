<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FriendRequest extends Model {
    protected static $table_name = 'mtts_friend_requests';

    public static function send_request( $sender_id, $receiver_id ) {
        // Check if already friends or request exists
        $existing = self::get_request( $sender_id, $receiver_id );
        if ( $existing ) {
            return false;
        }

        return self::create( array(
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'status' => 'pending'
        ) );
    }

    public static function get_request( $sender_id, $receiver_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_row( $wpdb->prepare( 
            "SELECT * FROM {$table} WHERE (sender_id = %d AND receiver_id = %d) OR (sender_id = %d AND receiver_id = %d)",
            $sender_id, $receiver_id, $receiver_id, $sender_id
        ) );
    }

    public static function get_pending_requests( $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare( 
            "SELECT * FROM {$table} WHERE receiver_id = %d AND status = 'pending' ORDER BY created_at DESC",
            $user_id
        ) );
    }

    public static function get_friends( $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare( 
            "SELECT * FROM {$table} WHERE (sender_id = %d OR receiver_id = %d) AND status = 'accepted' ORDER BY updated_at DESC",
            $user_id, $user_id
        ) );
    }

    public static function accept_request( $request_id ) {
        return self::update( $request_id, array( 'status' => 'accepted' ) );
    }

    public static function reject_request( $request_id ) {
        return self::update( $request_id, array( 'status' => 'rejected' ) );
    }
}
