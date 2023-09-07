<?php

namespace WpeContactForm\Controllers;

/**
 *
 */
class ContactForm {



    private static $_instance;
    public static $admin_post_action = 'contact_form_submit',
        $contact_form_name_custom_post_type = 'wpe_contact_form',
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

        // Register Contact Form  custom post type
        add_action( 'init', array($this, 'register_contact_form') );

        add_action( 'add_meta_boxes', [ $this,'add_meta_boxes_form'] );
        add_action( 'save_post', [ $this, 'save_metabox' ], 10, 2 );
    }


    public function add_meta_boxes_form() {
        add_meta_box(
            'wpe_contact_form_settings',
            'Form settings',
            [ $this,'meta_boxes_form_settings'],
            self::$contact_form_name_custom_post_type
        );
    }

    public function meta_boxes_form_settings( $post ) {
		
        wp_nonce_field( 'wpecf_metabox', '_wpnonce_wpecf_metabox' );

        ?>
         <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="email_to">To</label></th>
                    <td>
                        <input type="text" id="email_to" name="email_to" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpecf_email_to', true ) ); ?>" />
                    </td>
                </tr>
            </tbody>
         </table>
        <?php
    }

    public function save_metabox( $post_id, $post ) {
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['_wpnonce_wpecf_metabox'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['_wpnonce_wpecf_metabox'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wpecf_metabox' ) ) {
			return $post_id;
		}

		/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		/* OK, it's safe for us to save the data now. */

		// Sanitize the user input.
		$email_to = sanitize_text_field( $_POST['email_to'] );

		// Update the meta field.
		update_post_meta( $post_id, '_wpecf_email_to', $email_to );
	}


    /**
     * Return contact form success message
     * 
     */
    public static function get_success_message(){
        
        return __('message-formulaire-envoye-avec-succes', 'formulaire-de-contact');
    }



    /**
     * Return contact form fail message
     * 
     */
    public static function get_failure_message(){
        
        return __('message-erreur-lors-de-lenvoi-du-formulaire', 'formulaire-de-contact');
    }



    /**
     * Function called to treat contact form submission
     * 
     */
    public function action_post_contact_form() {

        // Nounce check
        if( !check_ajax_referer(self::$admin_post_action, false, false) ) {
            Helper::form_send_response( __('Nounce error', 'wpe-contact-form'), false );
        }

        // Email check
        if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
            Helper::form_send_response( __('Invalid email', 'wpe-contact-form'), false );
        }

        // reCAPTCHA check
        if( Recaptcha::recaptcha_is_enable() && ( ! isset($_POST['g-recaptcha-response']) || ! Recaptcha::recaptcha_check($_POST['g-recaptcha-response']) ) ) {
            Helper::form_send_response( __('Invalid reCAPTCHA', 'wpe-contact-form'), false );
        }

        // Get receiver according subject
        $email_to = 'pb+test@buzzbrothers.ch';
        if( !$email_to || !filter_var($email_to, FILTER_VALIDATE_EMAIL) ) {
            Helper::form_send_response( __('Invalid receiver', 'wpe-contact-form'), false );
        }

        // Insert entry information
        // $entry_informations = [];
        // foreach( self::get_fields() as $key_field => $label_field ) {
        //     if( isset($_POST[$key_field]) ) {
        //         $entry_informations[ WPE_CF_METADATA_PREFIX . $key_field ] = sanitize_textarea_field($_POST[$key_field]);
        //     }
        // }
        Entry::add_entry([
            'post_title'    => $_POST['firstname'] . ' ' .$_POST['lastname'] . ' (' . $_POST['email'] . ')',
            // 'meta_input'    => $entry_informations
        ]);
        die;
        // $message_email_sender = '<i>' . stripslashes(nl2br($_POST['message'])) . '</i>';
        if( Email::send_html_email($email_to) ){

            // Email::send_html_email($_POST['email'], sprintf( __('[%s] Acknowledgment of receipt', 'wpe-contact-form'), get_option('blogname') ), __('Your message has been sent successfully.<br />Thank you.', 'wpe-contact-form')  . Email::html_separator() . $message_email_sender . Email::html_separator());
            Helper::form_send_response( self::get_success_message(), true );
        }
        else{
            Helper::form_send_response( self::get_failure_message(), false );
        }
    }



    /**
     * Return an array with contact form fields saved in Wordpress database option
     * 
     */
    public static function get_fields(){

        return get_option(self::$name_fields_in_option_table, '');
    }



    /**
     * Register Contact Form Entry custom post type
     * 
     */
    public function register_contact_form(){
    
        $args = [
            'description'           => '',
            'public'                => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'show_in_nav_menus'     => true,
            'show_ui'               => true,
            'show_in_menu'          => 'wpe-contact-form/admin-forms.php',
            'capability_type'       => 'post',
            'hierarchical'          => false,
            'rewrite'               => false,
            'has_archive'           => false,
            'show_in_rest'          => true,
            'supports'              => [
                'title', 'editor', 'custom-fields'
            ],
            'labels' => [
                'name'                  => __('Forms', 'wpe-contact-form'),
                'singular_name'         => __('Form', 'wpe-contact-form'),
                'add_new'               => __('Add', 'wpe-contact-form'),
                'add_new_item'          => __('Add a new form', 'wpe-contact-form'),
                'new_item'              => __('New', 'wpe-contact-form'),
                'edit_item'             => __('Edit', 'wpe-contact-form'),
                'view_item'             => __('View', 'wpe-contact-form'),
                'all_items'             => __('Forms', 'wpe-contact-form'),
                'search_items'          => __('Search', 'wpe-contact-form'),
                'parent_item_colon'     => __('Parent form', 'wpe-contact-form'),
                'not_found'             => __('No form', 'wpe-contact-form'),
                'not_found_in_trash'    => __('No form', 'wpe-contact-form')
            ]
        ];

        register_post_type( self::$contact_form_name_custom_post_type, $args );
    }


}