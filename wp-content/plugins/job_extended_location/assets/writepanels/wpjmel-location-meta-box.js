jQuery( document ).ready( function( $ ) {

	/* Load Map */
	$( wpjmel_mb.input ).mapify( wpjmel_mb );

	/* Meta Box Toggle */
	$( document ).on( 'postbox-toggled', function( e, box ){
		$( box ).find( '.mapify' ).trigger( 'mapify_resize' );
	} );

	/* Meta Box Drag and Drop */
	$( '.meta-box-sortables' ).on( 'sortstop', function( e, box ){
		$( box.item ).find( '.mapify' ).trigger( 'mapify_resize' );
	} );
});