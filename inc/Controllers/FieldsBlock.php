<?php

namespace WpeContactForm\Controllers;

class FieldsBlock {

    private $handle_name = 'wpe_contact_form_editor';

    public function __construct() {
        $this->add_actions();
    }

    /**
     * Add Wordpress actions & filters
     * 
     */
    public function add_actions() {
        
        add_action( 'admin_enqueue_scripts', [$this, 'register_fields_editor_script']);
        add_action( 'init', [$this, 'register_fields_block'] );
    }

    /**
     * Register editor script
     *
     */
    public function register_fields_editor_script()
    {
        $handle = $this->handle_name . "-script";
        $asset_file = include WPE_CF_PLUGIN_DIR_PATH . 'build/index.asset.php';

        wp_register_script(
            $handle,
            WPE_CF_PLUGIN_URL . 'build/index.js',
            $asset_file["dependencies"],
            $asset_file["version"]
        );
        wp_enqueue_script($handle);
    }

    public function register_fields_block() {

        register_block_type(
            'custom/wpe-contact-form-field',
            [
                "render_callback" => [$this, "render"],
            ]
        );
    }

    public function render($attributes, $content, $block) {

        return $content;
    }
}