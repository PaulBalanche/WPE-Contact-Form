<?php

namespace WpeContactForm\Models;

class Entry {

    private $WP_Post;

    public function __construct( $postId ) {
        $potential_wp_post = get_post($postId);
        if( $potential_wp_post && is_object($potential_wp_post) && $potential_wp_post->post_type == \WpeContactForm\Controllers\Entries::$contact_form_entry_name_custom_post_type ) {
            $this->WP_Post = $potential_wp_post;
        }
    }

    public function display_data() {

        $all_post_metadata = get_metadata('post', $this->WP_Post->ID);

        if( isset($all_post_metadata[ WPE_CF_METADATA_PREFIX . 'form_id' ]) ) {
            $formInstance = new Form($all_post_metadata[ WPE_CF_METADATA_PREFIX . 'form_id' ][0]);
            if( $formInstance->is_valid() ) {
                $fields = $formInstance->get_fields();
                if( is_array($fields) && count($fields) > 0 ) {

                    echo '<table class="form-table" role="presentation">
                        <tbody>';
                    foreach( $fields as $key_field => $field ) {
                        if( isset($all_post_metadata[ WPE_CF_METADATA_PREFIX . $key_field ]) ) {
                            echo '<tr>
                                <th scope="row">' . $field['label'] . '</th>
                                <td>' . $all_post_metadata[ WPE_CF_METADATA_PREFIX . $key_field ][0] . '</td>
                            </tr>';
                        }
                    }

                    echo '</tbody>
                    </table>';
                }
            }
        }
    }

}