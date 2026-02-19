<?php
namespace MttsLms\Controllers\Admin;

use MttsLms\Models\Form;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FormController {

    public static function render() {
        $action = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : 'list';
        $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        if ( 'edit' === $action || 'new' === $action ) {
            self::render_editor( $id );
        } else {
            self::render_list();
        }
    }

    private static function render_list() {
        $forms = Form::all();
        include MTTS_LMS_PATH . 'includes/Views/Admin/form-list.php';
    }

    private static function render_editor( $id ) {
        $form = $id ? Form::find( $id ) : null;
        
        // Handle Save
        if ( isset( $_POST['mtts_save_form'] ) && check_admin_referer( 'mtts_save_form' ) ) {
            $title = sanitize_text_field( $_POST['form_title'] );
            $slug  = sanitize_title( $_POST['form_slug'] );
            $data  = $_POST['form_data']; // This will be JSON from the builder

            if ( $id ) {
                $wpdb_update = Form::update_record( $id, array(
                    'title' => $title,
                    'form_slug' => $slug,
                    'form_data' => $data
                ) );
            } else {
                $wpdb_insert = Form::create( array(
                    'title' => $title,
                    'form_slug' => $slug,
                    'form_data' => $data
                ) );
                $id = $wpdb_insert;
            }
            
            wp_redirect( admin_url( 'admin.php?page=mtts-form-builder&action=edit&id=' . $id . '&saved=1' ) );
            exit;
        }

        include MTTS_LMS_PATH . 'includes/Views/Admin/form-editor.php';
    }
}
