<?php

namespace WpeContactForm;

/**
 *
 */
class Main {

    private static $_instance;



	/**
    * Static method which instance Wpextend main class
    */
    public static function getInstance() {

        if (is_null(self::$_instance)) {
            self::$_instance = new Main();
        }
        return self::$_instance;
    }




	/**
    * Construct
    */
    private function __construct() {

        ContactForm::getInstance();
        Entry::getInstance();
        SettingsPage::getInstance();
    }



}