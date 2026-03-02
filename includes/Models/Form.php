<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Form extends Model {
    protected static $table_name = 'mtts_forms';

    /**
     * Get form by slug
     */
    public static function find_by_slug( $slug ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE form_slug = %s",
            $slug
        ) );
    }

    /**
     * Save form data
     */
    public static function save_form( $title, $slug, $data ) {
        global $wpdb;
        $table = self::get_table_name();
        $exists = self::find_by_slug( $slug );

        if ( $exists ) {
            return $wpdb->update(
                $table,
                array( 'title' => $title, 'form_data' => $data ),
                array( 'form_slug' => $slug )
            );
        } else {
            return $wpdb->insert(
                $table,
                array( 'title' => $title, 'form_slug' => $slug, 'form_data' => $data )
            );
        }
    }
}
