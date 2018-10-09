( function($) {
	$( document ).ready( function() {
		// since we hide the submit div, lets submit it using our custom submit button
		$( '#send_message' ).click( function(event) {
			event.preventDefault();

			if ( ! tinyMCE.activeEditor.isDirty() ) {
				alert( PrivateMessages.empty_message );
			} else {
				$( '#publish' ).click();
			}
			
		} );

		$( 'select[name=pm_recipient]' ).select2( {
			ajax: {
				url: ajaxurl,
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						action: 'pm_recipients_list',
						q: params.term, // search term
						page: params.page
					};
				},
				processResults: function (data, params) {
					// parse the results into the format expected by Select2
					// since we are using custom formatting functions we do not need to
					// alter the remote JSON data, except to indicate that infinite
					// scrolling can be used
					params.page = params.page || 1;

					return {
						results: data.recipients,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			},
			escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
			minimumInputLength: 1,
			templateResult: formatRecipient, // omitted for brevity, see the source of this page
			templateSelection: formatRecipientSelection // omitted for brevity, see the source of this page
		});

		function formatRecipient (recipient) {
			if (recipient.loading) return recipient.text;

			if ( PrivateMessages.show_avatars ) {
				var markup = "<div class='select2-result-pm-recipient clearfix'>" +
					"<div class='select2-result-pm-recipient__avatar'><img src='" + recipient.avatar_url + "' /></div>" +
					"<div class='select2-result-pm-recipient__meta'>" +
					"<div class='select2-result-pm-recipient__title'>" + recipient.name + "</div>";
			} else {
				var markup = "<div class='select2-result-pm-recipient pm-no-avatar clearfix'>" +
					"<div class='select2-result-pm-recipient__meta'>" +
					"<div class='select2-result-pm-recipient__title'>" + recipient.name + "</div>";
			}

			return markup;
		}

		function formatRecipientSelection (recipient) {
			var recipient_name = false, email = '';
			if (recipient.name) {
				if (recipient.email) {
					email = ' (' + recipient.email + ')';
				}
				recipient_name = recipient.name + email;
			}
			return recipient_name || recipient.text;
		}
	});
})( jQuery );