<?php
namespace MttsLms\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MatricNumberHelper {

    /**
     * Generate a new Matric Number based on campus and year.
     * Format: [CAMPUS]/[YEAR]/[SEQUENCE] (e.g., LAG/2026/0001)
     */
    public static function generate( $campus_name, $year = null ) {
        global $wpdb;
        $campus_code = self::get_campus_code( $campus_name );
        $year        = $year ? intval( $year ) : date( 'Y' );
        
        // Get format from settings
        $format = get_option( 'mtts_matric_format', 'MTTS/[CAMPUS]/[YEAR]/[SEQ]' );

        // Get the last sequence number for this campus and year
        $pattern = str_replace( array('[CAMPUS]', '[YEAR]', '[SEQ]'), array($campus_code, $year, '%'), $format );

        $last_matric = $wpdb->get_var( $wpdb->prepare(
            "SELECT matric_number FROM {$wpdb->prefix}mtts_students 
             WHERE matric_number LIKE %s 
             ORDER BY id DESC LIMIT 1",
            $pattern
        ) );

        $sequence = 1;
        if ( $last_matric ) {
            // Extract sequence from the end (simplified assumption)
            $parts = preg_split('/[\/\\-]/', $last_matric); // Split by / or -
            $last_seq = intval( end( $parts ) );
            $sequence = $last_seq + 1;
        }

        $padded_sequence = str_pad( $sequence, 3, '0', STR_PAD_LEFT );

        // Final generation
        $matric = str_replace(
            array('[CAMPUS]', '[YEAR]', '[SEQ]'),
            array($campus_code, $year, $padded_sequence),
            $format
        );

        return $matric;
    }

    /**
     * Get the code for a campus name.
     */
    public static function get_campus_code( $campus_name ) {
        global $wpdb;
        $code = $wpdb->get_var( $wpdb->prepare(
            "SELECT code FROM {$wpdb->prefix}mtts_campus_centers WHERE name LIKE %s",
            $campus_name
        ) );

        // Fallback to first 3 letters if not found
        return $code ? $code : strtoupper( substr( $campus_name, 0, 3 ) );
    }

    /**
     * Get the campus name for a specific campus code.
     */
     public static function get_campus_name_by_code( $code ) {
        global $wpdb;
        return $wpdb->get_var( $wpdb->prepare(
            "SELECT name FROM {$wpdb->prefix}mtts_campus_centers WHERE code = %s",
            $code
        ) );
     }
}
