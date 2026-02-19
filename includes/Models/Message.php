<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Message extends Model {
    protected static $table_name = 'mtts_messages';

    /**
     * Get all messages in a user's inbox (received messages)
     */
    public static function get_inbox( $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE receiver_id = %d AND parent_id IS NULL ORDER BY created_at DESC",
            $user_id
        ) );
    }

    /**
     * Get all messages sent by a user
     */
    public static function get_sent( $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE sender_id = %d AND parent_id IS NULL ORDER BY created_at DESC",
            $user_id
        ) );
    }

    /**
     * Get a thread: original message + all replies
     */
    public static function get_thread( $message_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d OR parent_id = %d ORDER BY created_at ASC",
            $message_id, $message_id
        ) );
    }

    /**
     * Count unread messages for a user
     */
    public static function count_unread( $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE receiver_id = %d AND is_read = 0",
            $user_id
        ) );
    }

    /**
     * Mark a message as read
     */
    public static function mark_read( $message_id ) {
        global $wpdb;
        $table = self::get_table_name();
        $wpdb->update( $table, [ 'is_read' => 1 ], [ 'id' => $message_id ] );
    }

    /**
     * Send a message
     */
    public static function send( $sender_id, $receiver_id, $subject, $body, $parent_id = null ) {
        return self::create( [
            'sender_id'   => $sender_id,
            'receiver_id' => $receiver_id,
            'subject'     => $subject,
            'body'        => $body,
            'parent_id'   => $parent_id,
            'is_read'     => 0,
        ] );
    }
}
