<?php
namespace MttsLms\Core\AI;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AINotificationGenerator {

    /**
     * Generate a creative message using Gemini AI
     * 
     * @param string $subject The subject of the notification
     * @param string $base_content The original/base content containing critical data
     * @param string $type The type of message (email or sms)
     * @return string The AI-generated message
     */
    public static function generate_ai_message( $subject, $base_content, $type = 'email' ) {
        $api_key = get_option( 'mtts_gemini_api_key' );
        
        if ( ! $api_key || ! get_option( 'mtts_enable_ai_notifications' ) ) {
            return $base_content; // Return original if AI is disabled or key missing
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $api_key;

        $system_instruction = "You are a professional and creative communications expert for Mountain-Top Theological Seminary (MTTS). 
        Your task is to rewrite automated notifications to be more engaging and professional while ensuring that ALL critical specific information is preserved EXACTLY as provided.
        
        CRITICAL RULES:
        1. Preserve all data like Matric Numbers, Passwords, Links, and Applicant Names EXACTLY as they appear in the original text.
        2. If the type is 'sms', keep the message under 160 characters and be very concise.
        3. If the type is 'email', use a warm, professional, and encouraging tone.
        4. Do not include any meta-talk or phrases like 'Here is your rewritten message'. Only output the final message content.";

        $body = array(
            'contents' => array(
                array(
                    'role'  => 'user',
                    'parts' => array(
                        array( 'text' => "Message Type: $type\nSubject: $subject\nOriginal Content: " . $base_content )
                    )
                )
            ),
            'system_instruction' => array(
                'parts' => array(
                    array( 'text' => $system_instruction )
                )
            ),
            'generationConfig' => array(
                'temperature' => 0.7,
                'maxOutputTokens' => ( 'sms' === $type ) ? 100 : 800,
            )
        );

        $response = wp_remote_post( $url, array(
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => json_encode( $body ),
            'timeout' => 45,
        ) );

        if ( is_wp_error( $response ) ) {
            return $base_content;
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );

        if ( $response_code === 200 ) {
            $data = json_decode( $response_body, true );
            if ( isset( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
                return trim( $data['candidates'][0]['content']['parts'][0]['text'] );
            }
        }

        return $base_content;
    }
}
