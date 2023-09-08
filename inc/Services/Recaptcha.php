<?php

namespace WpeContactForm\Services;

/**
 * reCAPTCHA static functions 
 * 
 */
class Recaptcha {


    public static $recaptcha_secret_field_in_option_table = 'wpe_contact_form_recaptcha_secret';



    /**
     * Check if reCAPTCHA is enable or no, based on reCAPTCHA secret field saved in Wordpress database option
     * 
     */
    public static function recaptcha_is_enable(){

        return ( empty(self::get_recaptcha_secret()) ) ? false : true;
    }



    /**
     * Return the reCAPTCHA secret field saved in Wordpress database option
     * 
     */
    public static function get_recaptcha_secret(){

        return get_option(self::$recaptcha_secret_field_in_option_table, '');
    }



    /**
     * Google reCAPTCHA: Verifying the user's response
     * 
     */
    public static function recaptcha_check($g_recaptcha_response) {

        $ch = curl_init("https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'secret' => self::get_recaptcha_secret(),
            'response' => $g_recaptcha_response
        ]);
        $recaptcha_response = json_decode( curl_exec($ch) );
        curl_close($ch);

        if( is_object($recaptcha_response) && $recaptcha_response->success )
            return true;
            

        return false;
    }



}