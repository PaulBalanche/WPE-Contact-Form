<?php

namespace WpeContactForm\Controllers;

class BackOffice {

    public function __construct() {
        $this->add_actions();
    }

    /**
     * Add Wordpress actions & filters
     * 
     */
    public function add_actions() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }


     
    /**
	 * Registers a new config page
	 */
	public function admin_menu() {

        add_menu_page( 'Contact form', 'Contact form', 'edit_posts', 'wpe-contact-form/admin-forms.php', '');
	}

}