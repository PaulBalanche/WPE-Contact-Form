<?php

namespace WpeContactForm\Controllers;

/**
 *
 */
class Entries {

    public static $contact_form_entry_name_custom_post_type = 'wpe_contact_entry',
     $export_admin_post_action = 'wpe_contact_entries_export';


	/**
	 * The constructor.
	 *
	 * @return void
	 */
	public function __construct() {

        // Register Contact Form Entry custom post type
        add_action( 'init', array($this, 'register_contact_form_entry') );

        // Create metabox in order to display all entry informations
        add_action( 'add_meta_boxes_' . self::$contact_form_entry_name_custom_post_type, array($this, 'add_contact_entry_metabox') );

        // Enqueue admin JS script
        add_action( 'admin_enqueue_scripts', array($this, 'load_contact_form_entry_script') );

        // Hook export action
        add_action( 'admin_post_' . self::$export_admin_post_action, array($this, 'export_admin_post_action') );
        add_action( 'admin_post_nopriv_' . self::$export_admin_post_action, array($this, 'export_admin_post_action') );

        add_action( 'admin_init', array($this, 'add_contact_form_entry_caps') );

        add_filter('manage_edit-' . self::$contact_form_entry_name_custom_post_type . '_columns', [ $this, 'manage_admin_columns' ]);
        add_action('manage_' . self::$contact_form_entry_name_custom_post_type . '_posts_custom_column', [ $this, 'manage_admin_columns_render'], 10, 2);

        add_action('restrict_manage_posts', [ $this, 'add_form_filter' ]);
        add_action('pre_get_posts', [ $this, 'by_form_filtering' ]);
    }

    public function manage_admin_columns( $columns ) {
        $date = $columns['date'];
        unset($columns['date']);
        $columns["form_id"] = "Form";
        $columns['date'] = $date;
        return $columns;
    }

    public function manage_admin_columns_render( $colname, $cptid ) {
        if ( $colname == 'form_id') {
            $formInstance = new \WpeContactForm\Models\Form(get_post_meta( $cptid, WPE_CF_METADATA_PREFIX . 'form_id', true ));
            echo $formInstance->get_name();
        }
    }

    public function add_form_filter( $options ) {
        $screen = get_current_screen();
        if( $screen->id == 'edit-' . self::$contact_form_entry_name_custom_post_type ){
            $my_args = [
                'post_type' => 'wpe_contact_form',
                'show_option_none' => 'All forms',
                'name' => 'form_id'
            ];
            if(isset($_GET['form_id'])){
                $my_args['selected'] = (int)sanitize_text_field($_GET['form_id']);
            }
            wp_dropdown_pages($my_args);
        }
    }

    public function by_form_filtering($query) {
        $screen = get_current_screen();
        if( $screen && is_object($screen) && isset($screen->id) && $screen->id == 'edit-' . self::$contact_form_entry_name_custom_post_type && $query->get('post_type') == self::$contact_form_entry_name_custom_post_type ){
            if(isset($_GET['form_id'])){
                $contact_form_id = sanitize_text_field($_GET['form_id']);
                if( is_numeric($contact_form_id)){
                    $query->set( 'meta_key', WPE_CF_METADATA_PREFIX . 'form_id' );
                    $query->set( 'meta_value', $contact_form_id );
                }
            }
        }
    }
    

    public function add_contact_form_entry_caps(){
        
        global $wp_roles;

        $wp_roles->add_cap( 'administrator', 'edit_contact_form_entries' );
        $wp_roles->add_cap( 'administrator', 'edit_others_contact_form_entries' );
        $wp_roles->add_cap( 'administrator', 'edit_private_contact_form_entries' );
        $wp_roles->add_cap( 'administrator', 'edit_published_contact_form_entries' );
        $wp_roles->add_cap( 'administrator', 'read_private_contact_form_entries' );

        $wp_roles->add_cap( 'editor', 'edit_contact_form_entries' );
        $wp_roles->add_cap( 'editor', 'edit_others_contact_form_entries' );
        $wp_roles->add_cap( 'editor', 'edit_private_contact_form_entries' );
        $wp_roles->add_cap( 'editor', 'edit_published_contact_form_entries' );
        $wp_roles->add_cap( 'editor', 'read_private_contact_form_entries' );
    }



    /**
     * Register Contact Form Entry custom post type
     * 
     */
    public function register_contact_form_entry(){
    
        $args = [
            'description'           => '',
            'public'                => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'show_in_nav_menus'     => true,
            'show_ui'               => true,
            'show_in_menu'          => 'wpe-contact-form/admin-forms.php',
            'capability_type'       => ['contact_form_entry', 'contact_form_entries'],
            'capabilities'          => [
                'edit_post'                 => 'edit_contact_form_entry',
                'read_post'                 => 'read_contact_form_entry',
                'delete_post'               => 'delete_contact_form_entry',
                'edit_posts'                => 'edit_contact_form_entries',
                'edit_others_posts'         => 'edit_others_contact_form_entries',
                'publish_posts'             => 'publish_contact_form_entries',
                'read_private_posts'        => 'read_private_contact_form_entries',
                'delete_posts'              => 'delete_contact_form_entries',
                'delete_private_posts'      => 'delete_private_contact_form_entries',
                'delete_published_posts'    => 'delete_published_contact_form_entries',
                'delete_others_posts'       => 'delete_others_contact_form_entries',
                'edit_private_posts'        => 'edit_private_contact_form_entries',
                'edit_published_posts'      => 'edit_published_contact_form_entries',
                'create_posts'              => 'do_not_allow'
            ],
            'map_meta_cap'          => true,
            'hierarchical'          => false,
            'rewrite'               => false,
            'has_archive'           => false,
            'show_in_rest'          => false,
            'supports'              => [ 'title' ],
            'labels' => [
                'name'                  => __('Form Entries', 'wpe-contact-form'),
                'singular_name'         => __('Form entry', 'wpe-contact-form'),
                'add_new'               => __('Add', 'wpe-contact-form'),
                'add_new_item'          => __('Add a new entry', 'wpe-contact-form'),
                'new_item'              => __('New', 'wpe-contact-form'),
                'edit_item'             => __('Edit', 'wpe-contact-form'),
                'view_item'             => __('View', 'wpe-contact-form'),
                'all_items'             => __('Entries', 'wpe-contact-form'),
                'search_items'          => __('Search', 'wpe-contact-form'),
                'parent_item_colon'     => __('Parent entry', 'wpe-contact-form'),
                'not_found'             => __('No entry', 'wpe-contact-form'),
                'not_found_in_trash'    => __('No entry', 'wpe-contact-form')
            ]
        ];

        register_post_type( self::$contact_form_entry_name_custom_post_type, $args );
    }



    /**
     * Create metabox in order to display all entry informations
     * 
     */
    public function add_contact_entry_metabox(){

        add_meta_box( 'contact_form_entry_information', __('Détails', 'wpe-contact-form'), array($this, 'entry_metabox_callback') );
    }



    /**
     * Enqueue admin JS script
     * 
     */
    public function load_contact_form_entry_script($hook) {

        wp_enqueue_script( 'wpe_contact_form_entry_script', WPE_CF_PLUGIN_ASSETS_URL . 'js/admin/Entry.js', array('jquery') );
        wp_localize_script( 'wpe_contact_form_entry_script', 'contact_form_entry_admin', [
            'export_feature' => [
                'page_title_action_name'  => 'Export',
                'url'   => admin_url( 'admin-post.php' ) . '?action=' . self::$export_admin_post_action . '&_wpnonce=' . wp_create_nonce(self::$export_admin_post_action),
                'csv'   => ( isset($_GET['csv']) ) ? $_GET['csv'] : false
            ]
        ] );
    }



    /**
     * Entry informations diplayed in metabox
     * 
     */
    public function entry_metabox_callback($post){
        
        $return_html = '';

        $entryInstance = new \WpeContactForm\Models\Entry($post->ID);
        echo $entryInstance->display_data();
    }


    /**
     * Function called to create CSV of all entry saved
     * 
     */
    public function export_admin_post_action(){

        // Check valid nonce
        check_admin_referer(self::$export_admin_post_action);

        $entries = get_posts([
            'posts_per_page'   => -1,
            'post_type'        => self::$contact_form_entry_name_custom_post_type,
            'post_status'      => 'publish'
        ]);
        if( is_array($entries) && count($entries) > 0 ) {

            $name_csv_file = date('Y-M-d_H:i:s') . '.csv';
            $fp = fopen( WPE_CF_PLUGIN_DIR_PATH . 'export/' . $name_csv_file, 'w');
            // fputcsv($fp, ContactForm::get_fields());

            // foreach( $entries as $entry ) {
                
            //     $csv_line_entry = [];

            //     $metadata = get_metadata('post', $entry->ID);
            //     foreach( ContactForm::get_fields() as $key_field => $label_field ) {

            //         $csv_line_entry[$key_field] = ( isset($metadata[WPE_CF_METADATA_PREFIX . $key_field]) ) ? $metadata[WPE_CF_METADATA_PREFIX . $key_field][0] : '';
            //     }

            //     fputcsv($fp, $csv_line_entry);
            // }

            fclose($fp);
        }

        $goback = add_query_arg( 'csv', WPE_CF_PLUGIN_URL . 'export/' . $name_csv_file, wp_get_referer() );
        wp_safe_redirect( $goback );
        exit;
    }



}
