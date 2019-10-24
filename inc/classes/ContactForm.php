<?php

namespace WpeContactForm;

/**
 *
 */
class ContactForm {



    private static $_instance;
    public static $admin_post_action = 'contact_form_submit',
        $name_fields_in_option_table = 'wpe_contact_form_fields';



	/**
	 * Static method which instanciate ContactForm
 	 */
	public static function getInstance() {
		 if (is_null(self::$_instance)) {
			  self::$_instance = new ContactForm();
		 }
		 return self::$_instance;
	}



	/**
	 * The constructor.
	 *
	 * @return void
	 */
	private function __construct() {

		// Define actions and filters
        $this->create_hooks();
    }
    


    /**
     * Define actions and filters
     * 
     */
    public function create_hooks() {

        add_action( 'wp_ajax_' . self::$admin_post_action, array($this, 'action_post_contact_form') );
        add_action( 'wp_ajax_nopriv_' . self::$admin_post_action, array($this, 'action_post_contact_form') );
    }



    /**
     * Return an array with contact form subjects
     * 
     */
    public static function get_form_subjects(){

        $form_subject = [];

        // Get Wpextend form subject
        if( class_exists('\Wpextend\GlobalSettings') ) $form_subject = \Wpextend\GlobalSettings::get('sujet-du-formulaire', 'formulaire-de-contact');
        
        return apply_filters('wpe_contact_form_get_subjects', $form_subject);
    }



    /**
     * Return contact form success message
     * 
     */
    public static function get_success_message(){
        
        return nl2br(\Wpextend\GlobalSettings::get('message-formulaire-envoye-avec-succes', 'formulaire-de-contact'));
    }



    /**
     * Return contact form fail message
     * 
     */
    public static function get_failure_message(){
        
        return nl2br(\Wpextend\GlobalSettings::get('message-erreur-lors-de-lenvoi-du-formulaire', 'formulaire-de-contact'));
    }



    /**
     * Function called to treat contact form submission
     * 
     */
    public function action_post_contact_form() {

        // Nounce check
        if( !check_ajax_referer(self::$admin_post_action, false, false) ) {
            Helper::form_send_response( __('Nounce error', PLUGIN_TEXTDOMAIN), false );
        }

        // Email check
        if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
            Helper::form_send_response( __('Invalid email', PLUGIN_TEXTDOMAIN), false );
        }

        // reCAPTCHA check
        if( Recaptcha::recaptcha_is_enable() && ( ! isset($_POST['g-recaptcha-response']) || ! Recaptcha::recaptcha_check($_POST['g-recaptcha-response']) ) ) {
            Helper::form_send_response( __('Invalid reCAPTCHA', PLUGIN_TEXTDOMAIN), false );
        }

        // Get receiver according subject
        $email_to = false;
        if( is_array(self::get_form_subjects()) && count(self::get_form_subjects()) > 0 ) {
            foreach( self::get_form_subjects() as $subject ) {

                preg_match('/^(.*) : (.*)$/U', $subject, $matches);
                if( is_array($matches) && count($matches) == 3 ) {
                    if( $matches[2] == $_POST['subject'] ){
                        $email_to = $matches[1];
                        $email_subject = $matches[2];
                        break;
                    }
                }
            }
        }
        if( !$email_to || !filter_var($email_to, FILTER_VALIDATE_EMAIL) ) {
            Helper::form_send_response( __('Invalid receiver', PLUGIN_TEXTDOMAIN), false );
        }

        // Insert entry information
        $entry_informations = [];
        foreach( self::get_fields() as $key_field => $label_field ) {
            if( isset($_POST[$key_field]) ) {
                $entry_informations[ METADATA_PREFIX . $key_field ] = sanitize_textarea_field($_POST[$key_field]);
            }
        }
        Entry::add_entry([
            'post_title'    => $_POST['firstname'] . ' ' .$_POST['lastname'] . ' (' . $_POST['email'] . ')',
            'meta_input'    => $entry_informations
        ]);

        // $message_email_sender = '<i>' . stripslashes(nl2br($_POST['message'])) . '</i>';
        if( Email::send_html_email($email_to) ){

            // Email::send_html_email($_POST['email'], sprintf( __('[%s] Acknowledgment of receipt', PLUGIN_TEXTDOMAIN), get_option('blogname') ), __('Your message has been sent successfully.<br />Thank you.', PLUGIN_TEXTDOMAIN)  . Email::html_separator() . $message_email_sender . Email::html_separator());
            Helper::form_send_response( self::get_success_message(), true );
        }
        else{
            Helper::form_send_response( self::get_failure_message(), false );
        }
    }



    /**
     * Get all form subjects formatted in array to contact-formn view
     * 
     */
    public static function get_subjects_formatted() {

        $options_subject = [];
        if( is_array(self::get_form_subjects()) && count(self::get_form_subjects()) > 0 ) {
            foreach( self::get_form_subjects() as $subject ) {

                preg_match( '/^(.*) : (.*)$/U', $subject, $matches );
                if( is_array($matches) && count($matches) == 3 ) {
                    $options_subject[ $matches[2] ] = $matches[2];
                }
            }
        }
        
        return $options_subject;
    }



    /**
     * During synchrone loading, GET return treatment and display message
     * 
     */
    public static function get_returned_message() {

        $reply = null;
        if( isset($_GET['contact_form']) ) {

            if( $_GET['contact_form'] == '1' ) {
                $reply = [
                    'success' => self::get_success_message()
                ];
            }
            else{
                $reply = [
                    'error' => self::get_failure_message()
                ];
            }
        }

        return $reply;
    }



    /**
     * Return an array with contact form fields saved in Wordpress database option
     * 
     */
    public static function get_fields(){

        return get_option(self::$name_fields_in_option_table, '');
    }



}