<div class="wrap">
    <h1>System Configuration</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'mtts_lms_options' ); ?>
        <?php do_settings_sections( 'mtts_lms_options' ); ?>
        
        <h3>Institutional Branding</h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Institution Name</th>
                <td>
                    <input type="text" name="mtts_institution_name" value="<?php echo esc_attr( get_option('mtts_institution_name', 'Mountain-Top Theological Seminary') ); ?>" class="regular-text" />
                    <p class="description">Main name used across the platform and in automated communications.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Institution Tagline</th>
                <td>
                    <input type="text" name="mtts_institution_tagline" value="<?php echo esc_attr( get_option('mtts_institution_tagline', 'Empowering Ministers for Global Impact') ); ?>" class="regular-text" />
                    <p class="description">Displayed on login pages and dashboard footers.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Logo URL</th>
                <td>
                    <input type="url" name="mtts_institution_logo_url" value="<?php echo esc_attr( get_option('mtts_institution_logo_url') ); ?>" class="regular-text" />
                    <p class="description">Direct link to the seminary logo (recommended height: 80px).</p>
                </td>
            </tr>
        </table>

        <h3>Academic Configuration</h3>
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

            <tr><th colspan="2"><h3>Admission & Payment Settings</h3></th></tr>
            
            <tr valign="top">
                <th scope="row">Enable Admission Payments</th>
                <td>
                    <input type="checkbox" name="mtts_enable_admission_payments" value="1" <?php checked( get_option('mtts_enable_admission_payments'), '1' ); ?> />
                    <span class="description">Require payment before admission form submission.</span>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Active Payment Gateway</th>
                <td>
                    <select name="mtts_active_payment_gateway">
                        <option value="paystack" <?php selected( get_option('mtts_active_payment_gateway'), 'paystack' ); ?>>Paystack (Recommended for NG)</option>
                        <option value="flutterwave" <?php selected( get_option('mtts_active_payment_gateway'), 'flutterwave' ); ?>>Flutterwave</option>
                        <option value="stripe" <?php selected( get_option('mtts_active_payment_gateway'), 'stripe' ); ?>>Stripe (International)</option>
                        <option value="paypal" <?php selected( get_option('mtts_active_payment_gateway'), 'paypal' ); ?>>PayPal</option>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Admin Bypass (Testing)</th>
                <td>
                    <input type="checkbox" name="mtts_admin_bypass_payments" value="1" <?php checked( get_option('mtts_admin_bypass_payments'), '1' ); ?> />
                    <span class="description">Allow Administrators to skip payment steps during testing.</span>
                </td>
            </tr>

            <tr><th colspan="2"><h3>Gateway API Configuration</h3></th></tr>

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

            <tr valign="top">
                <th scope="row">Stripe Public Key</th>
                <td><input type="text" name="mtts_stripe_public_key" value="<?php echo esc_attr( get_option('mtts_stripe_public_key') ); ?>" class="regular-text" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Stripe Secret Key</th>
                <td><input type="password" name="mtts_stripe_secret_key" value="<?php echo esc_attr( get_option('mtts_stripe_secret_key') ); ?>" class="regular-text" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">PayPal Client ID</th>
                <td><input type="text" name="mtts_paypal_client_id" value="<?php echo esc_attr( get_option('mtts_paypal_client_id') ); ?>" class="regular-text" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Square Application ID</th>
                <td><input type="text" name="mtts_square_app_id" value="<?php echo esc_attr( get_option('mtts_square_app_id') ); ?>" class="regular-text" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Authorize.Net Login ID</th>
                <td><input type="text" name="mtts_authorize_login_id" value="<?php echo esc_attr( get_option('mtts_authorize_login_id') ); ?>" class="regular-text" /></td>
            </tr>
        </table>
        
        <h3>AI Integration (Gemini)</h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Gemini API Key</th>
                <td>
                    <input type="password" name="mtts_gemini_api_key" value="<?php echo esc_attr( get_option('mtts_gemini_api_key') ); ?>" class="regular-text" />
                    <p class="description">Used for AI Form Generation.</p>
                </td>
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
                <th scope="row">Enable AI Notifications</th>
                <td>
                    <input type="checkbox" name="mtts_enable_ai_notifications" value="1" <?php checked( get_option('mtts_enable_ai_notifications'), '1' ); ?> />
                    <span class="description">Creatively rewrite automated emails and SMS using Gemini AI.</span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Enable Google Translator</th>
                <td>
                    <input type="checkbox" name="mtts_enable_google_translator" value="1" <?php checked( get_option('mtts_enable_google_translator'), 1 ); ?> />
                    <p class="description">Adds a floating language switcher to the bottom right of the site for international users.</p>
                </td>
            </tr>
        </table>

        <h3>Onboarding & Automation</h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Matric Number Format</th>
                <td>
                    <input type="text" name="mtts_matric_format" value="<?php echo esc_attr( get_option('mtts_matric_format', 'MTTS/[CAMPUS]/[YEAR]/[SEQ]') ); ?>" class="regular-text" />
                    <p class="description">Use placeholders: [CAMPUS], [YEAR], [SEQ]. Default: MTTS/[CAMPUS]/[YEAR]/[SEQ]</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">AI Congratulations Prompt Template</th>
                <td>
                    <textarea name="mtts_ai_congratulations_template" class="large-text" rows="5"><?php echo esc_textarea( get_option('mtts_ai_congratulations_template', "Write a formal, encouraging, and spiritually aligned congratulations message for a new student joining Mountain Top Theological Seminary. \nStudent Name: {name}\nProgram: {program}\nCampus: {campus}\nMatric Number: {matric}\nInclude login instructions: Username is the Matric Number and temporary password is 'student'. Mention that they must change their password on first login.") ); ?></textarea>
                    <p class="description">Base prompt for Gemini AI. Use placeholders like {name}, {program}, {campus}, {matric}.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Password Reset Link Expiry</th>
                <td>
                    <input type="number" name="mtts_reset_link_expiry_hours" value="<?php echo esc_attr( get_option('mtts_reset_link_expiry_hours', '24') ); ?>" class="small-text" /> hours
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Undergraduate Form Price (₦)</th>
                <td>
                    <input type="number" name="mtts_undergraduate_form_price" value="<?php echo esc_attr( get_option('mtts_undergraduate_form_price', '5000') ); ?>" class="regular-text" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Postgraduate Form Price (₦)</th>
                <td>
                    <input type="number" name="mtts_postgraduate_form_price" value="<?php echo esc_attr( get_option('mtts_postgraduate_form_price', '10000') ); ?>" class="regular-text" />
                </td>
            </tr>
        </table>
        
        <h3>SMTP Configuration (Automated Emails)</h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">SMTP Host</th>
                <td><input type="text" name="mtts_smtp_host" value="<?php echo esc_attr( get_option('mtts_smtp_host') ); ?>" class="regular-text" placeholder="smtp.gmail.com" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">SMTP Port</th>
                <td><input type="number" name="mtts_smtp_port" value="<?php echo esc_attr( get_option('mtts_smtp_port', '587') ); ?>" class="small-text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">SMTP Username</th>
                <td><input type="text" name="mtts_smtp_user" value="<?php echo esc_attr( get_option('mtts_smtp_user') ); ?>" class="regular-text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">SMTP Password</th>
                <td><input type="password" name="mtts_smtp_pass" value="<?php echo esc_attr( get_option('mtts_smtp_pass') ); ?>" class="regular-text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Encryption</th>
                <td>
                    <select name="mtts_smtp_encryption">
                        <option value="none" <?php selected( get_option('mtts_smtp_encryption'), 'none' ); ?>>None</option>
                        <option value="ssl" <?php selected( get_option('mtts_smtp_encryption'), 'ssl' ); ?>>SSL</option>
                        <option value="tls" <?php selected( get_option('mtts_smtp_encryption'), 'tls' ); ?>>TLS</option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">From Email</th>
                <td><input type="email" name="mtts_smtp_from_email" value="<?php echo esc_attr( get_option('mtts_smtp_from_email') ); ?>" class="regular-text" placeholder="no-reply@mttseminary.org" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">From Name</th>
                <td><input type="text" name="mtts_smtp_from_name" value="<?php echo esc_attr( get_option('mtts_smtp_from_name') ); ?>" class="regular-text" placeholder="MTTS LMS" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Test SMTP</th>
                <td>
                    <input type="email" id="mtts_test_email_target" class="regular-text" placeholder="Enter email to receive test" />
                    <button type="button" class="button" onclick="sendMttsTestEmail()">Send Test Email</button>
                    <span id="mtts_test_response" style="margin-left: 10px;"></span>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
        
        <h3>Developer Tools (Testing)</h3>
        <div class="mtts-card" style="border: 1px solid #ef4444; padding: 15px; background: #fef2f2;">
            <p style="color: #b91c1c; font-weight: bold;">Warning: These actions are for development/test environments only.</p>
            <a href="<?php echo esc_url( wp_nonce_url( admin_url('admin.php?page=mtts-lms-settings&mtts_generate_mock_data=1'), 'mtts_generate_mock' ) ); ?>" class="button button-link-delete" onclick="return confirm('Are you sure you want to generate mock users? This may create duplicate entries if they already exist.');">
                Generate Mock User Base (All Roles)
            </a>
            <?php if ( isset($_GET['mock_success']) ) : ?>
                <span style="margin-left: 10px; color: green; font-weight: bold;">Mock users generated successfully!</span>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
function sendMttsTestEmail() {
    const email = document.getElementById('mtts_test_email_target').value;
    const responseSpan = document.getElementById('mtts_test_response');
    
    if (!email) {
        alert('Please enter an email address for the test.');
        return;
    }

    responseSpan.innerText = 'Sending...';
    responseSpan.style.color = '#6b21a8';

    jQuery.post(ajaxurl, {
        action: 'mtts_send_test_email',
        nonce: '<?php echo wp_create_nonce("mtts_lms_settings"); ?>',
        email: email
    }, function(response) {
        if (response.success) {
            responseSpan.innerText = response.data.message;
            responseSpan.style.color = 'green';
        } else {
            responseSpan.innerText = 'Error: ' + response.data.message;
            responseSpan.style.color = 'red';
        }
    });
}
</script>
