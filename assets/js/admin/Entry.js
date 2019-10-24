( function( $, contact_form_entry_admin ) {
    $( function() {
		if ( 'undefined' === typeof contact_form_entry_admin ) {
			return;
        }

        var $contact_form_entry_screen = $( '.edit-php.post-type-wpe_contact_entry' ),
            $first_title_action   = $contact_form_entry_screen.find( '.wp-heading-inline' );

        $first_title_action.after( '<a href="' + contact_form_entry_admin.export_feature.url + '" class="page-title-action">' + contact_form_entry_admin.export_feature.page_title_action_name + '</a>' );
        if( contact_form_entry_admin.export_feature.csv ){
            $contact_form_entry_screen.find( '.page-title-action:last' ).after( '<div class="notice notice-success is-dismissible"><p><a href="' + contact_form_entry_admin.export_feature.csv + '" target="_blank" >Download last CSV export</a></p></div>' );
        }
          
    });
})( jQuery, contact_form_entry_admin );