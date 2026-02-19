<?php
namespace MttsLms\Core;

use MttsLms\Models\Form;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FormShortcode {

    public static function init() {
        add_shortcode( 'mtts_form', array( __CLASS__, 'render_form' ) );
    }

    public static function render_form( $atts ) {
        $atts = shortcode_atts( array(
            'slug' => '',
        ), $atts );

        if ( empty( $atts['slug'] ) ) return '';

        $form = Form::get_by_slug( $atts['slug'] );
        if ( ! $form ) return 'Form not found.';

        // Handle Submission
        if ( isset( $_POST['mtts_dynamic_form_id'] ) && intval( $_POST['mtts_dynamic_form_id'] ) == $form->id ) {
            self::handle_submission( $form );
        }

        $form_data = json_decode( $form->form_data, true );
        $fields    = isset( $form_data['fields'] ) ? $form_data['fields'] : array();

        ob_start();
        ?>
        <div class="mtts-frontend-form">
            <h3 class="mtts-form-title"><?php echo esc_html( $form->title ); ?></h3>
            <form method="post" action="" class="mtts-dynamic-form" data-form-id="<?php echo $form->id; ?>">
                <?php wp_nonce_field( 'mtts_submit_dynamic_form_' . $form->id ); ?>
                <input type="hidden" name="mtts_dynamic_form_id" value="<?php echo $form->id; ?>">

                <?php foreach ( $fields as $index => $field ) : 
                    $field_id = "field_{$index}";
                    $required = ! empty( $field['required'] ) ? 'required' : '';
                ?>
                    <div class="mtts-form-group" style="margin-bottom: 20px;">
                        <label for="<?php echo $field_id; ?>">
                            <strong><?php echo esc_html( $field['label'] ); ?></strong>
                            <?php if ( $required ) : ?><span style="color:red;">*</span><?php endif; ?>
                        </label>
                        <br>

                        <?php if ( 'textarea' === $field['type'] ) : ?>
                            <textarea name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" class="mtts-form-control" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" <?php echo $required; ?>></textarea>
                        
                        <?php elseif ( 'select' === $field['type'] ) : ?>
                            <select name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" class="mtts-form-control" <?php echo $required; ?>>
                                <option value="">-- Select --</option>
                                <?php 
                                $options = explode( ',', $field['options'] );
                                foreach ( $options as $opt ) : $opt = trim($opt);
                                ?>
                                    <option value="<?php echo esc_attr( $opt ); ?>"><?php echo esc_html( $opt ); ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php elseif ( 'radio' === $field['type'] ) : ?>
                            <?php 
                            $options = explode( ',', $field['options'] );
                            foreach ( $options as $opt ) : $opt = trim($opt);
                            ?>
                                <label><input type="radio" name="<?php echo $field_id; ?>" value="<?php echo esc_attr( $opt ); ?>" <?php echo $required; ?>> <?php echo esc_html( $opt ); ?></label><br>
                            <?php endforeach; ?>

                        <?php elseif ( 'checkbox' === $field['type'] ) : ?>
                            <?php 
                            $options = explode( ',', $field['options'] );
                            foreach ( $options as $opt ) : $opt = trim($opt);
                            ?>
                                <label><input type="checkbox" name="<?php echo $field_id; ?>[]" value="<?php echo esc_attr( $opt ); ?>"> <?php echo esc_html( $opt ); ?></label><br>
                            <?php endforeach; ?>

                        <?php else : ?>
                            <input type="<?php echo esc_attr( $field['type'] ); ?>" name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" class="mtts-form-control" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" <?php echo $required; ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="mtts-form-footer">
                    <button type="submit" class="mtts-btn mtts-btn-primary">Submit Form</button>
                </div>
            </form>
        </div>
        <style>
            .mtts-form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-top: 5px; }
            .mtts-btn-primary { background: #7c3aed; color: #fff; border: 0; padding: 10px 25px; border-radius: 5px; cursor: pointer; font-weight: 600; }
        </style>
        <?php
        return ob_get_clean();
    }
    private static function handle_submission( $form ) {
        if ( ! \MttsLms\Core\Security::check_request( 'mtts_submit_dynamic_form_' . $form->id ) ) return;

        $fields_raw = json_decode( $form->form_data, true );
        $fields     = isset( $fields_raw['fields'] ) ? $fields_raw['fields'] : array();
        
        // Deep sanitize POST data
        $post_data  = \MttsLms\Core\Security::sanitize_deep( $_POST );
        $entry_data = array();

        foreach ( $fields as $index => $field ) {
            $field_id = "field_{$index}";
            if ( isset( $post_data[$field_id] ) ) {
                $entry_data[$field['label']] = $post_data[$field_id];
            }
        }

        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'mtts_form_entries',
            array(
                'form_id'    => $form->id,
                'user_id'    => get_current_user_id() ?: null,
                'entry_data' => json_encode( $entry_data ),
            )
        );

        echo '<div class="mtts-alert mtts-alert-success" style="padding:15px; background:#dcfce7; border-left:4px solid #22c55e; margin-bottom:20px;">' .
             esc_html__( 'Form submitted successfully. Thank you!', 'mtts-lms' ) . 
             '</div>';
    }
}
