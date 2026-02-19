<?php
namespace MttsLms\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NotificationManager {

    public static function init() {
        add_action( 'mtts_send_email_event', array( __CLASS__, 'process_email_queue' ), 10, 3 );
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
        $sender = get_option( 'blogname' ) . ' <no-reply@' . $_SERVER['SERVER_NAME'] . '>';
        $headers[] = 'From: ' . $sender;

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
                .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; border-top: 5px solid #4b0082; }
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
                    &copy; <?php echo date('Y'); ?> Mountain-Top Theological Seminary. All rights reserved.
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

        // Generic Implementation (Compatible with many providers like EBulkSMS)
        // Adjust parameters as per specific provider
        $params = array(
            'username' => $username,
            'apikey' => $api_key,
            'sender' => $sender_id,
            'messagetext' => $message,
            'flash' => 0,
            'mobiles' => $phone
        );

        $url = add_query_arg( $params, $api_url );
        
        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            error_log( 'SMS Send Error: ' . $response->get_error_message() );
        }
    }
}
