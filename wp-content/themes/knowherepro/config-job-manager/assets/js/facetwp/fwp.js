(function( $ ) {

	'use strict';

	Sortable.create( document.getElementById('knowhere_facets_list'), {
		group: {
			name: 'fwp',
			pull: 'clone',
			put: false
		},
		animation: 200
	} );

	[{
		id: 'shortcode_listing_map',
		name: 'fwp',
		pull: false,
		put: true
	},
	{
		id: 'listings_archive',
		name: 'fwp',
		pull: false,
		put: true
	}].forEach(function (Opts, i, e) {

		var self = e[i];

		Sortable.create( document.getElementById(self.id), {
			sort: true,
			group: Opts,
			animation: 150,
			filter: '.facet-remove',
			onFilter: function (e) {
				e.item.parentNode.removeChild(e.item);
				updateValues( e );
			},
			onUpdate: function( e ) { updateValues( e ); },
			onAdd: function( e )    { updateValues( e ); },
			onRemove: function( e ) { updateValues( e ); }
		});

	});

	/*
	 * A functon to update values when a change is made into the sortable object
	 */
	var updateValues = function( e ) {

		var $hidden = $('#setting-knowhere_facets_config' ), result = {};

		$('.knowhere-facets-config .facets').each( function() {

			var ID = $(this).attr( 'id' ), list = $(this).children( 'li' );

			result[ID] = {};

			if ( list.length > 0 ) {
				$.each(list, function( i, el ) {
					result[ID][i] = $(el).data('facet');
				});
			}

		});

		$hidden.val( JSON.stringify( result ) );
	};
})( jQuery );