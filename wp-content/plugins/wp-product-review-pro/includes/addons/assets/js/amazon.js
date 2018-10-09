/*jshint sub:true*/
/* global ajaxurl */
/* global wppra */
(function($, wppra){

	function initPro(){
		$( '#cwp_rev_amazon_id_bttn' ).on('click', function(e){
			e.preventDefault();

			$.ajax({
				url: ajaxurl,
				dataType: 'json',
				data: {
					'action'            : wppra.action,
					'_action'           : 'get',
					'cwp_rev_amazon_id' : $( '#cwp_rev_amazon_id' ).val()
				},
				method: 'post',
				success: function(data){
					$( '#tempimg' ).remove();
					$( '#wppr-editor-image' ).parent().append( '<img id="tempimg" style="max-width: 100%" src="' + data.data.details['smallurl'] + '">' );
					$( '#wppr-editor-image' ).val( data.data.details['url'] );
					$( '#wppr-editor-product-name' ).val( data.data.details['name'] );
					$( '#wppr-editor-button-text-1' ).val( wppra.i18n['buyonamazon'] );
					$( '#wppr-editor-button-link-1' ).val( data.data.details['affiliatelink'] );
					$( '#wppr-editor-price' ).val( data.data.details['price'] );
				}
			});
		});
	}

	$( document ).ready(function() {
		initPro();
	});

})(jQuery, wppra);
