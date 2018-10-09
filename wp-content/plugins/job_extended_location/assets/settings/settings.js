jQuery( document ).ready( function( $ ) {

	/* Load Map */
	$( '#setting-wpjmel_map_start_location' ).mapify( wpjmel );

	/* Click Tab, Resize Map */
	$( '.nav-tab[href="#settings-wpjmel_settings"]' ).click( function(){
		$( '.mapify' ).trigger( 'mapify_resize' );
	});

});