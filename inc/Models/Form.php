<?php

namespace WpeContactForm\Models;

class Form {

    private $WP_Post,
        $fields = null;

    public function __construct( $postId ) {
        $potential_wp_post = get_post($postId);
        if( $potential_wp_post && is_object($potential_wp_post) && $potential_wp_post->post_type == \WpeContactForm\Controllers\Forms::$contact_form_name_custom_post_type ) {
            $this->WP_Post = $potential_wp_post;
        }
    }

    public function is_valid() {

        return ( is_object($this->WP_Post) );
    }

    public function get_name() {
        
        return ( $this->is_valid() ) ? $this->WP_Post->post_title : null;
    }

    public function get_fields() {

        if( is_null($this->fields) )  {
            
            $this->fields = [];

            $parser_class = apply_filters( 'block_parser_class', 'WP_Block_Parser' );
            $parser = new $parser_class();
            $fields_parsed = $parser->parse( $this->WP_Post->post_content );
            if( is_array($fields_parsed) ) {
                foreach( $fields_parsed as $field ) {
                    if( $field['blockName'] == 'custom/wpe-contact-form-field' ) {
                        $this->fields[ sanitize_title($field['attrs']['label']) ] = [
                            'type' => $field['attrs']['type'],
                            'label' => $field['attrs']['label'],
                        ];
                    }
                }
            }
        }

        return $this->fields;
    }

    public function format_for_view () {

        if( $this->is_valid() ) {

            $fields = $this->get_fields();
            if( is_array($fields) && count($fields) > 0 ) {
                
                $formData = [
                    'action' => admin_url( 'admin-post.php' ) . '/?action=contact_form_submit',
                    'lnf' => [
                        'labelType' => 'block'
                    ],
                    'fields' => [
                        'formid' => [
                            'type' => 'text',
                            'label' => 'Form ID',
                            'name' => 'form_id'
                        ]
                    ],
                    'actions' => [
                        [
                            'type' => 'submit',
                            'label' => 'Send my message!'
                        ]
                    ]
                ];

                foreach( $fields as $key_field => $field ) {

                    $formData['fields'][ $key_field ] = [
                        'type' => $field['type'],
                        'label' => $field['label'],
                        'name' => $key_field,
                        'placeholder' => $field['label'],
                        'validations' => [
                            'required' => true,
                            'something' => 'cool',
                        ]
                    ];
                }

                return $formData;
            }
        }

        return null;
    }


    /**
     * Function to insert new Contact form entry
     * 
     */
    public function add_entry( $data ){

        if( $this->is_valid() ) {

            $fields = $this->get_fields();
            if( is_array($fields) && count($fields) > 0 ) {
                $data_entry = [
                    WPE_CF_METADATA_PREFIX . 'form_id' => $this->WP_Post->ID
                ];
                foreach( $fields as $key_field => $field ) {
                    if( isset($data[$key_field]) ) {
                        $data_entry[ WPE_CF_METADATA_PREFIX . $key_field ] = sanitize_textarea_field($data[$key_field]);
                    }
                }

                $defaults = [
                    'post_content'  => '',
                    'post_title'    => '',
                    'post_excerpt'  => '',
                    'post_status'   => 'publish',
                    'post_type'     => \WpeContactForm\Controllers\Entries::$contact_form_entry_name_custom_post_type,
                    'meta_input'    => []
                ];
        
                // Merge defaults args with args passed
                $args = wp_parse_args( [
                    'post_title'    => uniqid(),
                    'meta_input'    => $data_entry
                ], $defaults );
        
                // Entry insertion
                return wp_insert_post($args);
            }
        }

        return false;
    }

}