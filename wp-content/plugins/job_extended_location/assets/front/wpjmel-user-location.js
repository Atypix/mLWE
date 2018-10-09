jQuery( document ).ready( function( $ ) {
	if( '' == $( '#search_location' ).val() ){
		$( '#search_location' ).val( wpjmel_loc.address );
	}
});

