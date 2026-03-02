<?php
namespace MttsLms\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NotificationManager {

    public static function init() {
        add_action( 'mtts_send_email_event', array( __CLASS__, 'process_email_queue' ), 10, 3 );
        add_action( 'phpmailer_init', array( __CLASS__, 'configure_smtp' ) );
    }

    /**
     * Configure PHPMailer with SMTP settings
     */
    public static function configure_smtp( $phpmailer ) {
        $host       = get_option( 'mtts_smtp_host' );
        $user       = get_option( 'mtts_smtp_user' );
        $pass       = get_option( 'mtts_smtp_pass' );
        $port       = get_option( 'mtts_smtp_port' );
        $encryption = get_option( 'mtts_smtp_encryption' );

        if ( ! $host || ! $user || ! $pass ) {
            return; // Not configured
        }

        $phpmailer->isSMTP();
        $phpmailer->Host       = $host;
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = $port ?: 587;
        $phpmailer->Username   = $user;
        $phpmailer->Password   = $pass;
        $phpmailer->SMTPSecure = ( 'none' !== $encryption ) ? $encryption : '';
        $phpmailer->From       = get_option( 'mtts_smtp_from_email', get_option( 'admin_email' ) );
        $phpmailer->FromName   = get_option( 'mtts_smtp_from_name', get_option( 'blogname' ) );
    }

    /**
     * Schedule an email to be sent asynchronously
     */
    public static function send_email( $to, $subject, $message ) {
        // In a real high-volume app, we would save to a DB table 'mtts_queue' 
        // and have acron job pick them up. 
        // For simplicity/MVP, we use WP Cron single event per email or just immediate mail if low volume.
        // Let's use wp_schedule_single_event for immediate async offloading.
        
        wp_schedule_single_event( time(), 'mtts_send_email_event', array( $to, $subject, $message ) );
    }

    /**
     * The actual worker that sends the email
     */
    public static function process_email_queue( $to, $subject, $message ) {
        $headers = array( 'Content-Type: text/html; charset=UTF-8' );
        
        $from_email = get_option( 'mtts_smtp_from_email' );
        $from_name  = get_option( 'mtts_smtp_from_name' );

        if ( $from_email && $from_name ) {
            $sender = $from_name . ' <' . $from_email . '>';
        } else {
            $sender = get_option( 'blogname' ) . ' <no-reply@' . $_SERVER['SERVER_NAME'] . '>';
        }
        $headers[] = 'From: ' . $sender;

        if ( get_option( 'mtts_enable_ai_notifications' ) ) {
            $message = \MttsLms\Core\AI\AINotificationGenerator::generate_ai_message( $subject, $message, 'email' );
        }

        // Wrap message in a template
        $full_message = self::get_email_template( $subject, $message );

        wp_mail( $to, $subject, $full_message, $headers );
    }

    private static function get_email_template( $subject, $content ) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: sans-serif; background: #f4f6f9; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; border-top: 5px solid #6b21a8; }
                .header { text-align: center; margin-bottom: 30px; }
                .footer { margin-top: 30px; font-size: 12px; color: #777; text-align: center; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2><?php echo esc_html( get_option( 'blogname' ) ); ?></h2>
                </div>
                <div class="content">
                    <?php echo wp_kses_post( $content ); ?>
                </div>
                <div class="footer">
                    &copy; <?php echo date('Y'); ?> <?php echo esc_html(get_option('mtts_institution_name', 'Mountain-Top Theological Seminary')); ?>. All rights reserved.
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    public static function send_sms( $phone, $message ) {
        $api_url = get_option( 'mtts_sms_api_url' ); // e.g., http://api.ebulksms.com/sendsms
        $api_key = get_option( 'mtts_sms_api_key' );
        $sender_id = get_option( 'mtts_sms_sender_id', 'MTTS' );
        $username = get_option( 'mtts_sms_username' );

        if ( ! $api_url || ! $username || ! $api_key ) {
             error_log( "SMS Gateway not configured. Message to $phone: $message" );
             return;
        }

        if ( get_option( 'mtts_enable_ai_notifications' ) ) {
            $message = \MttsLms\Core\AI\AINotificationGenerator::generate_ai_message( 'SMS Notification', $message, 'sms' );
        }

        // Generic Implementation (Compatible with many providers like EBulkSMS)
        $params = array(
            'username' => $username,
            'apikey' => $api_key,
            'sender' => $sender_id,
            'messagetext' => $message,
            'flash' => 0,
            'mobiles' => $phone
        );

        $url = add_query_arg( $params, $api_url );
        wp_remote_get( $url );
    }

    /**
     * Send a role-specific welcome email with AI enhancement
     */
    public static function send_welcome_email( $user, $password ) {
        $role = reset( $user->roles );
        $institution_name = get_option('mtts_institution_name', 'Mountain-Top Theological Seminary');
        $subject = "Welcome to {$institution_name} - Your Portal Credentials";
        
        $portal_url = home_url( '/portal-login' );
        $username = $user->user_login;

        $instructions = "";
        switch ( $role ) {
            case 'mtts_student':
                $instructions = "You have been admitted as a student. You can use your Matric Number to login. Please register your courses and check your semester results in the portal.";
                break;
            case 'mtts_lecturer':
                $instructions = "Welcome to the faculty. You can now access your class lists, upload assignments, and submit grades via the Lecturer Dashboard.";
                break;
            case 'mtts_accountant':
                $instructions = "You have been assigned to the financial department. Please use the portal to manage student payments and view financial reports.";
                break;
            case 'mtts_registrar':
                $instructions = "Welcome to the Registry. You can now manage student enrollment and process academic records.";
                break;
            default:
                $instructions = "Your MTTS portal account has been created. Please login to explore your assigned capabilities.";
                break;
        }

        $base_message = sprintf(
            "<p>Dear %s,</p>
            <p>Welcome to the <?php echo esc_html(get_option('mtts_institution_name', 'Mountain-Top Theological Seminary')); ?> digital ecosystem.</p>
            <p><strong>Your Access Credentials:</strong><br>
            Username: %s<br>
            Temporary Password: %s</p>
            <p>%s</p>
            <p>Login here: <a href='%s'>MTTS Covenant Portal</a></p>
            <p><em>Note: For security reasons, please change your password upon your first successful login.</em></p>",
            esc_html( $user->display_name ),
            esc_html( $username ),
            esc_html( $password ),
            esc_html( $instructions ),
            esc_url( $portal_url )
        );

        self::send_email( $user->user_email, $subject, $base_message );
    }
}
