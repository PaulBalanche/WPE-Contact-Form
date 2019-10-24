<?php

namespace WpeContactForm;

/**
 * Helper static functions 
 * 
 */
class Helper {



    /**
     * function to check if it's AJAX request
     * 
     */
    public static function request_is_ajax(){

        if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }

        return false;
    }



    /**
     * Function to return clean response (support AJAX and non-AJAX request)
     * 
     */
    public static function form_send_response($message, $success = true) {

        if( Helper::request_is_ajax() ) {

            // Define status return code
            $status_code = ( $success ) ? null : 422;
            wp_send_json([ 'message' => __($message, PLUGIN_TEXTDOMAIN) ], $status_code);
        }
        else {

            // Define return code
            $code_success = ( $success ) ? 1 : 0;
            $goback = add_query_arg( 'contact_form', $code_success, wp_get_referer() ) . '#' . $_POST['section_id'];
            wp_safe_redirect( $goback );
        }
        
        exit;
    }




    /**
     * Function to replace pattern with $_POST value
     * 
     */
    public static function regex_email($message_formatted){

        preg_match_all( '/{[^{]*}/', $message_formatted, $matches );
        if( is_array($matches) && count($matches) > 0 && is_array($matches) && count($matches) > 0 ) {

            $patterns = [];
            $replacements = [];
            foreach( $matches[0] as $pattern ) {

                $key_post_to_test = trim($pattern, '{}');
                if( array_key_exists($key_post_to_test, $_POST) ) {
                    $patterns[] = '/' . $pattern . '/';
                    $replacements[] = $_POST[$key_post_to_test];
                }
            }

            $message_formatted = preg_replace($patterns, $replacements, $message_formatted);
        }

        return $message_formatted;
    }



}