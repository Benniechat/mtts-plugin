<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Badge extends Model {
    protected static $table_name = 'mtts_badges';

    /**
     * Award a badge to a user (idempotent — won't duplicate)
     */
    public static function award( $user_id, $badge_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_user_badges';
        // Check if already awarded
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d AND badge_id = %d",
            $user_id, $badge_id
        ) );
        if ( ! $exists ) {
            $wpdb->insert( $table, [
                'user_id'  => $user_id,
                'badge_id' => $badge_id,
            ] );
        }
    }

    /**
     * Get all badges awarded to a user
     */
    public static function get_user_badges( $user_id ) {
        global $wpdb;
        $badges_table      = self::get_table_name();
        $user_badges_table = $wpdb->prefix . 'mtts_user_badges';
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT b.*, ub.awarded_at FROM {$badges_table} b
             INNER JOIN {$user_badges_table} ub ON b.id = ub.badge_id
             WHERE ub.user_id = %d
             ORDER BY ub.awarded_at DESC",
            $user_id
        ) );
    }

    /**
     * Check and auto-award badges based on a trigger event and value
     */
    public static function check_and_award( $user_id, $event, $value ) {
        global $wpdb;
        $table = self::get_table_name();
        $badges = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE trigger_event = %s AND trigger_value <= %d",
            $event, $value
        ) );
        foreach ( $badges as $badge ) {
            self::award( $user_id, $badge->id );
        }
    }
}
