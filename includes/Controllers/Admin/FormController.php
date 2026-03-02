<?php
namespace MttsLms\Controllers\Admin;

use MttsLms\Models\Form;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FormController {
    
    public static function init() {
        add_action( 'wp_ajax_mtts_ai_generate_form', array( __CLASS__, 'ajax_ai_generate_form' ) );
        add_action( 'wp_ajax_mtts_get_form_template', array( __CLASS__, 'ajax_get_form_template' ) );
        add_action( 'wp_ajax_mtts_save_form_session', array( __CLASS__, 'ajax_save_form_session' ) );
        add_action( 'wp_ajax_nopriv_mtts_save_form_session', array( __CLASS__, 'ajax_save_form_session' ) );
        
        add_action( 'wp_ajax_mtts_verify_form_payment', array( __CLASS__, 'ajax_verify_form_payment' ) );
        add_action( 'wp_ajax_nopriv_mtts_verify_form_payment', array( __CLASS__, 'ajax_verify_form_payment' ) );

        add_action( 'wp_ajax_mtts_preview_form', array( __CLASS__, 'ajax_preview_form' ) );

        // Initialize Entry Controller
        FormEntryController::init();
    }

    public static function render() {
        $action = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : 'list';
        $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        if ( 'delete' === $action && $id ) {
            if ( isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'mtts_delete_form_' . $id) ) {
                if ( current_user_can('manage_options') ) {
                    Form::delete( $id );
                    wp_redirect( admin_url( 'admin.php?page=mtts-form-builder&deleted=1' ) );
                    exit;
                }
            }
        }

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
            $title    = sanitize_text_field( $_POST['form_title'] );
            $slug     = sanitize_title( $_POST['form_slug'] );
            if ( empty( $slug ) ) {
                $slug = sanitize_title( $title );
            }
            $data     = wp_unslash( $_POST['form_data'] ); 
            $deadline = !empty($_POST['submission_deadline']) ? sanitize_text_field($_POST['submission_deadline']) : null;

            if ( $id ) {
                $wpdb_update = Form::update( $id, array(
                    'title'               => $title,
                    'form_slug'           => $slug,
                    'form_data'           => $data,
                    'submission_deadline' => $deadline
                ) );
                if ( false === $wpdb_update ) {
                    global $wpdb;
                    wp_die( 'Database Error during update: ' . $wpdb->last_error );
                }
            } else {
                $wpdb_insert = Form::create( array(
                    'title'               => $title,
                    'form_slug'           => $slug,
                    'form_data'           => $data,
                    'submission_deadline' => $deadline
                ) );
                if ( ! $wpdb_insert ) {
                    global $wpdb;
                    wp_die( 'Database Error during insert: ' . $wpdb->last_error );
                }
                $id = $wpdb_insert;
            }
            
            wp_redirect( admin_url( 'admin.php?page=mtts-form-builder&action=edit&id=' . $id . '&saved=1' ) );
            exit;
        }

        include MTTS_LMS_PATH . 'includes/Views/Admin/form-editor.php';
    }

    /**
     * AJAX handler for AI Generation
     */
    public static function ajax_ai_generate_form() {
        check_ajax_referer( 'mtts_form_builder_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $prompt = sanitize_text_field( $_POST['prompt'] );
        if ( empty( $prompt ) ) {
            wp_send_json_error( array( 'message' => 'Prompt is required.' ) );
        }

        $fields = \MttsLms\Core\AI\AIFormGenerator::generate_form_from_prompt( $prompt );
        
        if ( is_wp_error( $fields ) ) {
            wp_send_json_error( array( 'message' => $fields->get_error_message() ) );
        }

        wp_send_json_success( array( 'fields' => $fields ) );
    }

    /**
     * AJAX handler for Form Templates
     */
    public static function ajax_get_form_template() {
        check_ajax_referer( 'mtts_form_builder_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $template_id = sanitize_key( $_POST['template_id'] );
        $templates   = \MttsLms\Core\Forms\FormTemplates::get_templates();

        if ( isset( $templates[$template_id] ) ) {
            wp_send_json_success( $templates[$template_id] );
        }

        wp_send_json_error( array( 'message' => 'Template not found.' ) );
    }

    public static function ajax_save_form_session() {
        $form_id   = isset( $_POST['mtts_form_id'] ) ? intval( $_POST['mtts_form_id'] ) : 0;
        $last_step = isset( $_POST['last_step'] ) ? intval( $_POST['last_step'] ) : 0;
        $form      = \MttsLms\Models\Form::find( $form_id );

        if ( ! $form ) {
            wp_send_json_error( array( 'message' => 'Invalid form.' ) );
        }

        $fields_raw = json_decode( $form->form_data, true );
        $fields     = isset( $fields_raw['fields'] ) ? $fields_raw['fields'] : array();
        
        $post_data  = \MttsLms\Core\Security::sanitize_deep( $_POST );
        $partial_data = array();

        foreach ( $fields as $index => $field ) {
            $field_id = "field_{$index}";
            if ( isset( $post_data[$field_id] ) ) {
                $partial_data[$field_id] = $post_data[$field_id];
            }
        }

        $session_key = \MttsLms\Models\FormSession::save_session(
            $form_id,
            get_current_user_id() ?: null,
            json_encode( $partial_data ),
            $last_step
        );

        if ( $session_key ) {
            $resume_url = add_query_arg( 'mtts_resume', $session_key, home_url( $_SERVER['REQUEST_URI'] ) );
            wp_send_json_success( array( 
                'message'    => 'Progress saved.',
                'resume_url' => $resume_url
            ) );
        }

        wp_send_json_error( array( 'message' => 'Failed to save session.' ) );
    }

    public static function ajax_verify_form_payment() {
        $reference = sanitize_text_field( $_POST['reference'] );
        $category  = sanitize_key( $_POST['category'] ); // 'undergraduate' or 'postgraduate'
        $form_id   = intval( $_POST['form_id'] );

        if ( empty( $reference ) || empty( $category ) ) {
            wp_send_json_error( array( 'message' => 'Invalid payment data.' ) );
        }

        // Verification Logic (Mock for now, normally would call Paystack API)
        if ( ! session_id() ) {
            session_start();
        }

        $_SESSION['mtts_form_paid_' . $form_id] = $category;
        
        wp_send_json_success( array( 
            'message' => 'Payment verified successfully.',
            'category' => $category
        ) );
    }

    public static function ajax_preview_form() {
        check_ajax_referer( 'mtts_form_builder_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $form_data_raw = wp_unslash( $_POST['form_data'] ?? '' );
        $title         = sanitize_text_field( $_POST['title'] ?? 'Preview Form' );

        // Create a mock Form model object in memory
        $mock_form = new \stdClass();
        $mock_form->id = 0; // 0 ensures it doesn't conflict with real db forms
        $mock_form->title = $title;
        $mock_form->form_slug = 'preview-form';
        $mock_form->form_data = $form_data_raw;
        $mock_form->submission_deadline = null;

        // Ensure FormShortcode expects stdClass or handle it
        if ( ! class_exists( '\MttsLms\Core\FormShortcode' ) ) {
            wp_send_json_error( array( 'message' => 'Renderer not found.' ) );
        }

        // We bypass the shortcode attribute parsing and directly call the rendering logic
        // For preview purposes, we simulate Super Admin bypassing payment so we see the full form.
        ob_start();
        echo \MttsLms\Core\FormShortcode::render_form_bypass( $mock_form );
        $html = ob_get_clean();

        wp_send_json_success( array( 'html' => $html ) );
    }
}
