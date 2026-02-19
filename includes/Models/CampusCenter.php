<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CampusCenter Model
 *
 * Manages campus center records used in matric number generation.
 * Matric format: MTTS/{YEAR}/{CAMPUS_CODE}/{SERIAL}
 * Example: MTTS/2026/LAG/001
 */
class CampusCenter extends Model {
    protected static $table_name = 'mtts_campus_centers';

    /**
     * Get all active campus centers
     */
    public static function get_active() {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( "SELECT * FROM {$table} WHERE is_active = 1 ORDER BY name ASC" );
    }

    /**
     * Find campus center by its code (e.g. 'LAG')
     */
    public static function find_by_code( $code ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE code = %s", strtoupper( $code ) ) );
    }

    /**
     * Generate the next matric number for a given campus and year.
     *
     * Format: MTTS/{YEAR}/{CAMPUS_CODE}/{SERIAL}
     * Example: MTTS/2026/LAG/001
     *
     * @param int    $campus_center_id  The campus center ID
     * @param int    $year              The admission year (e.g. 2026)
     * @return string                   Generated matric number
     */
    public static function generate_matric( $campus_center_id, $year = null ) {
        global $wpdb;

        if ( ! $year ) {
            $year = date( 'Y' );
        }

        $campus = self::find( $campus_center_id );
        if ( ! $campus ) {
            return null;
        }

        $campus_code = strtoupper( $campus->code );

        // Count existing students for this campus + year to get next serial
        $students_table = $wpdb->prefix . 'mtts_students';
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$students_table} WHERE campus_center_id = %d AND admission_year = %d",
            $campus_center_id,
            $year
        ) );

        $serial = str_pad( intval( $count ) + 1, 3, '0', STR_PAD_LEFT );

        return "MTTS/{$year}/{$campus_code}/{$serial}";
    }

    /**
     * Parse a matric number into its components.
     *
     * @param string $matric  e.g. "MTTS/2026/LAG/001"
     * @return array|null     ['prefix', 'year', 'campus_code', 'serial'] or null
     */
    public static function parse_matric( $matric ) {
        $parts = explode( '/', $matric );
        if ( count( $parts ) !== 4 ) {
            return null;
        }
        return [
            'prefix'      => $parts[0],
            'year'        => $parts[1],
            'campus_code' => $parts[2],
            'serial'      => $parts[3],
        ];
    }
}
