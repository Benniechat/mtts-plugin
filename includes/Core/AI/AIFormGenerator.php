<?php
namespace MttsLms\Core\AI;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIFormGenerator {

    /**
     * Generate form structure using Gemini API
     */
    public static function generate_form_from_prompt( $prompt ) {
        $api_key = get_option( 'mtts_gemini_api_key' );
        
        if ( ! $api_key ) {
            return new \WP_Error( 'missing_api_key', 'Gemini API Key is not configured in settings.' );
        }

        // Use v1beta specifically for newer model features like system_instruction
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $api_key;

        $system_instruction = "You are a form building expert. Generate a JSON object representing a form structure based on the user's prompt. 
        The output MUST be a valid JSON with a 'fields' array. Each field should have:
        - 'type': (one of: text, textarea, email, number, date, select, checkbox, radio, tel, url, password, hidden, file, signature, rating)
        - 'label': String
        - 'placeholder': String (optional)
        - 'required': Boolean
        - 'options': Comma-separated string (for select, checkbox, radio)
        
        Do not include any other text or explanation, only the JSON block.";

        $body = array(
            'contents' => array(
                array(
                    'role'  => 'user',
                    'parts' => array(
                        array( 'text' => "User Prompt: " . $prompt )
                    )
                )
            ),
            'system_instruction' => array(
                'parts' => array(
                    array( 'text' => $system_instruction )
                )
            ),
            'generationConfig' => array(
                'response_mime_type' => 'application/json',
            )
        );

        $response = wp_remote_post( $url, array(
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => json_encode( $body ),
            'timeout' => 45,
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );

        if ( $response_code !== 200 ) {
            $err_data = json_decode( $response_body, true );
            $err_msg  = isset( $err_data['error']['message'] ) ? $err_data['error']['message'] : 'API Error ' . $response_code;
            return new \WP_Error( 'gemini_api_error', $err_msg );
        }

        $data = json_decode( $response_body, true );
        
        if ( isset( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
            $text = $data['candidates'][0]['content']['parts'][0]['text'];
            
            // Extract JSON if model included text around it
            if ( preg_match( '/\{.*\}/s', $text, $matches ) ) {
                $text = $matches[0];
            }
            
            $json = json_decode( $text, true );
            
            if ( $json && isset( $json['fields'] ) ) {
                return $json['fields'];
            }
        }

        return new \WP_Error( 'generation_failed', 'Failed to generate form. The AI response was not in the expected format. Please try again.' );
    }
}
