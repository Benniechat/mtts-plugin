<div class="wrap">
    <h1>System Configuration</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'mtts_lms_options' ); ?>
        <?php do_settings_sections( 'mtts_lms_options' ); ?>
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Current Academic Session</th>
                <td>
                    <select name="mtts_current_session_id">
                        <option value="">-- Select Session --</option>
                        <?php foreach($sessions as $session): ?>
                            <option value="<?php echo esc_attr($session->id); ?>" <?php selected( get_option('mtts_current_session_id'), $session->id ); ?>>
                                <?php echo esc_html($session->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Current Semester</th>
                <td>
                    <select name="mtts_current_semester">
                        <option value="1" <?php selected( get_option('mtts_current_semester'), '1' ); ?>>First Semester</option>
                        <option value="2" <?php selected( get_option('mtts_current_semester'), '2' ); ?>>Second Semester</option>
                    </select>
                </td>
            </tr>

            <tr><th colspan="2"><h3>Payment Gateways</h3></th></tr>

            <tr valign="top">
                <th scope="row">Paystack Public Key</th>
                <td><input type="text" name="mtts_paystack_public_key" value="<?php echo esc_attr( get_option('mtts_paystack_public_key') ); ?>" class="regular-text" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Paystack Secret Key</th>
                <td><input type="password" name="mtts_paystack_secret_key" value="<?php echo esc_attr( get_option('mtts_paystack_secret_key') ); ?>" class="regular-text" /></td>
            </tr>
            
             <tr valign="top">
                <th scope="row">Flutterwave Public Key</th>
                <td><input type="text" name="mtts_flutterwave_public_key" value="<?php echo esc_attr( get_option('mtts_flutterwave_public_key') ); ?>" class="regular-text" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Flutterwave Secret Key</th>
                <td><input type="password" name="mtts_flutterwave_secret_key" value="<?php echo esc_attr( get_option('mtts_flutterwave_secret_key') ); ?>" class="regular-text" /></td>
            </tr>
        </table>
        
        <h3>Zoom Integration</h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Zoom API Key</th>
                <td><input type="text" name="mtts_zoom_api_key" value="<?php echo esc_attr( get_option('mtts_zoom_api_key') ); ?>" class="regular-text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Zoom API Secret</th>
                <td><input type="password" name="mtts_zoom_api_secret" value="<?php echo esc_attr( get_option('mtts_zoom_api_secret') ); ?>" class="regular-text" /></td>
            </tr>
        </table>

        <h3>SMS Gateway Settings</h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">API URL</th>
                <td><input type="text" name="mtts_sms_api_url" value="<?php echo esc_attr( get_option('mtts_sms_api_url') ); ?>" class="regular-text" placeholder="http://api.ebulksms.com/sendsms" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Username</th>
                <td><input type="text" name="mtts_sms_username" value="<?php echo esc_attr( get_option('mtts_sms_username') ); ?>" class="regular-text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">API Key</th>
                <td><input type="password" name="mtts_sms_api_key" value="<?php echo esc_attr( get_option('mtts_sms_api_key') ); ?>" class="regular-text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Sender ID</th>
                <td><input type="text" name="mtts_sms_sender_id" value="<?php echo esc_attr( get_option('mtts_sms_sender_id') ); ?>" class="regular-text" maxlength="11" /></td>
            </tr>
        </table>

        <h3>Language & Accessibility</h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Enable Google Translator</th>
                <td>
                    <input type="checkbox" name="mtts_enable_google_translator" value="1" <?php checked( get_option('mtts_enable_google_translator'), 1 ); ?> />
                    <p class="description">Adds a floating language switcher to the bottom right of the site for international users.</p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
</div>
