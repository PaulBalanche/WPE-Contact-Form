<?php

namespace WpeContactForm;

use WpeContactForm\Controllers\Forms as FormsController;
use WpeContactForm\Controllers\Entries as EntriesController;
use WpeContactForm\Controllers\AdminMenu as AdminMenuController;
use WpeContactForm\Controllers\FieldsBlock as FieldsBlockController;

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

        new FormsController();
        new EntriesController();
        new AdminMenuController();
        new FieldsBlockController();
    }

}