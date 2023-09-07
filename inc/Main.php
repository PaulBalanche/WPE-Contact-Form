<?php

namespace WpeContactForm;

use WpeContactForm\Controllers\ContactForm as ContactFormController;
use WpeContactForm\Controllers\Entry as EntryController;
use WpeContactForm\Controllers\SettingsPage as SettingsPageController;
use WpeContactForm\Controllers\BackOffice as BackOfficeController;
use WpeContactForm\Controllers\Fields as FieldsController;

/**
 *
 */
class Main {

    private static $_instance;


    /**
     * Utility method to retrieve the main instance of the class.
     * The instance will be created if it does not exist yet.
     * 
     */
    public static function getInstance() {

        if( is_null(self::$_instance) ) {
            self::$_instance = new Main();
        }
        return self::$_instance;
    }


	/**
    * Construct
    */
    private function __construct() {

        ContactFormController::getInstance();
        EntryController::getInstance();
        SettingsPageController::getInstance();
        new BackOfficeController();
        new FieldsController();
    }



}