<?php

namespace WpeContactForm\Controllers;

/**
 * Email static functions 
 * 
 */
class Email {


    public static $name_email_subject_in_option_table = 'wpe_contact_email_subject',
        $name_email_message_in_option_table = 'wpe_contact_email_message';



    /**
     * Function to send HTML email
     */
    public static function send_html_email($to){

        $email_subject = Helper::regex_email( self::get_email_subject() );
        $email_message = Helper::regex_email( self::get_email_message() );
        
        $mail_html = '<html>
            <body>' . nl2br($email_message) . self::html_footer() . '</body>
        </html>';

        $header = "Content-type: text/html; charset=utf-8";

        return mail($to, $email_subject, $mail_html, $header);
    }



    /**
     * Function to display html footer
     */
    public static function html_footer(){

        return '<div style="border-top: 1px solid rgb(220,220,220);font-style:italic;font-size: 0.9em;margin-top: 40px;padding-top: 10px;">' . sprintf( __('Email sent from <strong>%s</strong> at %s', 'wpe-contact-form'), get_option('blogname'), date('Y-M-d H:i:s') ) . '</div>';
    }



    /**
     * Display HTML email separator
     */
    public static function html_separator(){

        return '<br /><br />----<br /><br />';
    }



    /**
     * Return email subject regex saved in Wordpress database option 
     */
    public static function get_email_subject(){

        return get_option(self::$name_email_subject_in_option_table, '');
    }



    /**
     * Return email message regex saved in Wordpress database option 
     */
    public static function get_email_message(){

        return get_option(self::$name_email_message_in_option_table, '');
    }



}