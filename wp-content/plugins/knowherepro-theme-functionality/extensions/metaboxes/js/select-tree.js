jQuery( function ( $ ) {
	'use strict';

	function update() {
		var $this = $( this ),
			val = $this.val(),
			$selected = $this.siblings( "[data-parent-id='" + val + "']" ),
			$notSelected = $this.parent().find( '.mad-select-tree' ).not( $selected );

		$selected.removeClass( 'hidden' );
		$notSelected
			.addClass( 'hidden' )
			.find( 'select' )
			.prop( 'selectedIndex', 0 );
	}

	$( '.mad-input' )
		.on( 'change', '.mad-select-tree select', update )
		.on( 'clone', '.mad-select-tree select', update );
} );
