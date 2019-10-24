<?php

namespace WpeContactForm;

/**
 *
 */
class SettingsPage {



	private static $_instance;
	public static $name_action = 'wpe_contact_form_settings';



	/**
	 * Static method which instance SettingsPage
 	 */
	public static function getInstance() {
		 if (is_null(self::$_instance)) {
			  self::$_instance = new SettingsPage();
		 }
		 return self::$_instance;
	}



	/**
	 * The constructor.
	 *
	 * @return void
	 */
	private function __construct() {

		// Hook admin menu to add FormEntries submenu settings page
		add_action( 'admin_menu', array($this, 'add_submenu_contact_form_page'), 20 );
		
		// Hook admin_post to save settings update
		add_action( 'admin_post_' . self::$name_action, array($this, 'hook_save_settings') );
    }



	/**
	 * Hook admin menu to add FormEntries submenu settings page
	 * 
	 */
    public function add_submenu_contact_form_page(){
        
        add_submenu_page( \WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', 'Contact form settings', 'Contact form', 'manage_options', 'wpe_contact_settings', array($this, 'render_settings_admin_page') );
    }



	/**
	 * Callback function to display settings page
	 * 
	 */
    public function render_settings_admin_page(){

		$html = \Wpextend\RenderAdminHtml::header( get_admin_page_title() );

		$html .= '<div class="accordion_wpextend">
		<h2>Global settings</h2><div>';
		$html .= \Wpextend\RenderAdminHtml::form_open( admin_url('admin-post.php'), self::$name_action );
		$html .= \Wpextend\RenderAdminHtml::table_edit_open();
		$html .= \Wpextend\TypeField::render_input_textarea( 'Contact form fields', ContactForm::$name_fields_in_option_table, self::encode_array_to_string( ContactForm::get_fields() ), false, 'Fill one per line<br />(lastname : Lastname)', false);
		$html .= \Wpextend\TypeField::render_input_text( 'Email subject', Email::$name_email_subject_in_option_table, Email::get_email_subject() );
		$html .= \Wpextend\TypeField::render_input_textarea( 'Email message', Email::$name_email_message_in_option_table, Email::get_email_message() );
		$html .= \Wpextend\TypeField::render_input_text( 'reCAPTCHA secret', Recaptcha::$recaptcha_secret_field_in_option_table, Recaptcha::get_recaptcha_secret() );
		$html .= \Wpextend\RenderAdminHtml::table_edit_close();
		$html .= \Wpextend\RenderAdminHtml::form_close('Submit', true);
		$html .= '</div>
		</div>';
		
		echo $html;
	}
	


	/**
	 * Hook admin_post to save settings update
	 * 
	 */
	public static function hook_save_settings(){

		// Verify nonce
		check_admin_referer(self::$name_action);

		// Contact form fields
		if( isset($_POST[ContactForm::$name_fields_in_option_table]) ) {
			update_option(ContactForm::$name_fields_in_option_table, self::decode_textarea_to_array( sanitize_textarea_field($_POST[ContactForm::$name_fields_in_option_table]) ) );
		}

		// Email subject
		if( isset($_POST[Email::$name_email_subject_in_option_table]) ) {
			update_option(Email::$name_email_subject_in_option_table, sanitize_text_field($_POST[Email::$name_email_subject_in_option_table]) );
		}

		// Email message
		if( isset($_POST[Email::$name_email_message_in_option_table]) ) {
			update_option(Email::$name_email_message_in_option_table, sanitize_textarea_field($_POST[Email::$name_email_message_in_option_table]) );
		}

		// reCAPTCHA
		if( isset($_POST[Recaptcha::$recaptcha_secret_field_in_option_table]) ) {
			update_option(Recaptcha::$recaptcha_secret_field_in_option_table, sanitize_text_field($_POST[Recaptcha::$recaptcha_secret_field_in_option_table]) );
		}

		// Redirect
		$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
		wp_safe_redirect( $goback );
		exit;
	}



	/**
	 * Function to decode string fields configuration and return formatted array 
	 * 
	 */
	public static function decode_textarea_to_array($string){

		$array_returned = preg_split('/\r\n|[\r\n]/', $string);
		if( is_array($array_returned) ) {

			$return = [];
			foreach( $array_returned as $line ) {

				preg_match('/^(.*) : (.*)$/', $line, $matches);
				if( is_array($matches) && count($matches) == 3 ) {
					$return[$matches[1]] = $matches[2];
				}
			}

			return $return;
		}
	
		return [];
	}



	/**
	 * Function to encode formatted array of fields configuration and return string in order to display it in textarea
	 * 
	 */
	public static function encode_array_to_string($array){
		
		if( is_array($array) ){

			foreach($array as $key => $field){
				$array[$key] = $key . ' : ' . $field;
			}

			return implode(PHP_EOL, $array);
		}

		return '';
	}



}
