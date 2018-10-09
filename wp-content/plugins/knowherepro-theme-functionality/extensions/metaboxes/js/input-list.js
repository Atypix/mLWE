jQuery( function ( $ ) {
	function update() {
		var $this = $( this ),
			$children = $this.closest( 'li' ).children( 'ul' );

		if ( $this.is( ':checked' ) ) {
			$children.removeClass( 'hidden' );
		} else {
			$children
				.addClass( 'hidden' )
				.find( 'input' )
				.removeAttr( 'checked' );
		}
	}

	$( '.mad-input' )
		.on( 'change', '.mad-input-list.collapse :checkbox', update )
		.on( 'clone', '.mad-input-list.collapse :checkbox', update );
	$( '.mad-input-list.collapse :checkbox' ).each( update );
} );
