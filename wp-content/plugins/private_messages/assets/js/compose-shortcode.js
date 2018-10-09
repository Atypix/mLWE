( function($) {
	$( document ).ready( function() {

		/* Compose Form Submit */
		$( 'form.pm-form--compose-message' ).submit( function( event ) {
			event.preventDefault();

			/* Force Editor to save content to textarea */
			if ( typeof tinymce !== 'undefined' ) {
				tinyMCE.get( 'pm_message' ).save();
			}

			/* Empty message alert */
			if ( ! $( '#pm_message' ).val() ) {
				alert( PrivateMessages.empty_message );
				return false;
			}

			/* Form Data */
			var thisForm = $( this );
			var formData = new FormData();

			/* Add attachments files */
			var attachments_input = $( '#pm_attachments' );
			if ( attachments_input.length ) {
				$.each( attachments_input[0].files, function( index, value ) {
					formData.append( "pm_attachments[" + index + "]", value );
				});
			}

			/* WP Ajax Action */
			formData.append( 'action', 'pm_compose_shortcode' );

			/* Fields */
			formData.append( 'pm_recipient', $( '#pm-recipient' ).val() );
			formData.append( 'pm_subject', $( '#pm-subject' ).val() );
			formData.append( 'pm_message', $( '#pm_message' ).val() );

			/* All form fields: for nonce, etc */
			formData.append( 'fields', thisForm.serialize() );

			/* Submit form to ajax callback */
			wp.ajax.send( {
				dataType: 'json',
				data : formData,
				contentType: false, // required
				processData: false, // required
			} )
			.done( function( response ) {
				if ( response.redirect ) {
					window.location.href = response.redirect;
				}
			} )
			.fail( function( response ) {
				if ( response.notice ) {
					$( '.pm-notice' ).remove();
					var notice_html = formatNotice( response.notice );
					thisForm.prepend( notice_html );
				}
			} );

		}); // end submit

		function formatNotice( notice ) {
			var markup = 
				"<div class='pm-notice'>" +
					"<p>" + notice + "</p>" +
				"</div>";
			return markup;
		}

	});
})( jQuery );
