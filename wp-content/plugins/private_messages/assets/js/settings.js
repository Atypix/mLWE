;( function( $ ){

	// Load.
	private_messages_settings_tabs();

	/**
	 * Settings Tabs
	 * Based on options framework tabs.
	 *
	 * @link https://github.com/devinsays/options-framework-theme/
	 */
	function private_messages_settings_tabs() {
		var tabs_id    = 'private_messages_settings';
		var panels     = $( '.private-messages-section' );
		var navtabs    = $( '.private-messages-settings-tab .nav-tab' );
		var active_tab = '';

		/* Hide panel */
		panels.hide();

		/* Get Local Storage */
		if ( typeof( localStorage ) != 'undefined' ) {
			active_tab = localStorage.getItem( tabs_id + '_active_tab' );
		}
		if ( active_tab != '' && $( active_tab ).length ) {
			$( active_tab ).fadeIn();
			$( '.nav-tab[href="' + active_tab + '"]' ).addClass( 'nav-tab-active' );
		} else {
			$( '.private-messages-section:first' ).fadeIn();
			$( '.private-messages-settings-tab .nav-tab:first' ).addClass( 'nav-tab-active' );
		}

		/* Click tab: add local storage and show/hide */
		navtabs.click( function(e) {
			e.preventDefault();
			navtabs.removeClass( 'nav-tab-active' );
			$( this ).addClass( 'nav-tab-active' ).blur();
			if ( typeof( localStorage ) !== 'undefined' ) {
				localStorage.setItem( tabs_id + '_active_tab', $( this ).attr( 'href' ) );
			}
			var selected = $( this ).attr( 'href' );
			panels.hide();
			$( selected ).fadeIn();
		});
	}

})( jQuery );