/*!
 * Column visibility buttons for Buttons and DataTables.
 * 2016 SpryMedia Ltd - datatables.net/license
 */

(function( factory ){
	if ( typeof define === 'function' && define.amd ) {
		// AMD
		define( ['jquery', 'datatables.net', 'datatables.net-buttons'], function ( $ ) {
			return factory( $, window, document );
		} );
	}
	else if ( typeof exports === 'object' ) {
		// CommonJS
		module.exports = function (root, $) {
			if ( ! root ) {
				root = window;
			}

			if ( ! $ || ! $.fn.dataTable ) {
				$ = require('datatables.net')(root, $).$;
			}

			if ( ! $.fn.dataTable.Buttons ) {
				require('datatables.net-buttons')(root, $);
			}

			return factory( $, root, root.document );
		};
	}
	else {
		// Browser
		factory( jQuery, window, document );
	}
}(function( $, window, document, undefined ) {
'use strict';
var DataTable = $.fn.dataTable;


$.extend( DataTable.ext.buttons, {
	// A collection of column visibility buttons
	colvis: function ( dt, conf ) {
		return {
			extend: 'collection',
			text: function ( dt ) {
				return dt.i18n( 'buttons.colvis', 'Column visibility' );
			},
			className: 'buttons-colvis',
			buttons: [ {
				extend: 'columnsToggle',
				columns: conf.columns,
				columnText: conf.columnText
			} ]
		};
	},

	// Selected columns with individual buttons - toggle column visibility
	columnsToggle: function ( dt, conf ) {
		var columns = dt.columns( conf.columns ).indexes().map( function ( idx ) {
			return {
				extend: 'columnToggle',
				columns: idx,
				columnText: conf.columnText
			};
		} ).toArray();

		return columns;
	},

	// Single button to toggle column visibility
	columnToggle: function ( dt, conf ) {
		return {
			extend: 'columnVisibility',
			columns: conf.columns,
			columnText: conf.columnText
		};
	},

	// Selected columns with individual buttons - set column visibility
	columnsVisibility: function ( dt, conf ) {
		var columns = dt.columns( conf.columns ).indexes().map( function ( idx ) {
			return {
				extend: 'columnVisibility',
				columns: idx,
				visibility: conf.visibility,
				columnText: conf.columnText
			};
		} ).toArray();

		return columns;
	},

	// Single button to set column visibility
	columnVisibility: {
		columns: undefined, // column selector
		text: function ( dt, button, conf ) {
			return conf._columnText( dt, conf );
		},
		className: 'buttons-columnVisibility',
		action: function ( e, dt, button, conf ) {
			var col = dt.columns( conf.columns );
			var curr = col.visible();

			col.visible( conf.visibility !== undefined ?
				conf.visibility :
				! (curr.length ? curr[0] : false )
			);
		},
		init: function ( dt, button, conf ) {
			var that = this;
			button.attr( 'data-cv-idx', conf.columns );

			dt
				.on( 'column-visibility.dt'+conf.namespace, function (e, settings) {
					if ( ! settings.bDestroying && settings.nTable == dt.settings()[0].nTable ) {
						that.active( dt.column( conf.columns ).visible() );
					}
				} )
				.on( 'column-reorder.dt'+conf.namespace, function (e, settings, details) {
					// Don't rename buttons based on column name if the button
					// controls more than one column!
					if ( dt.columns( conf.columns ).count() !== 1 ) {
						return;
					}

					conf.columns = $.inArray( conf.columns, details.mapping );
					button.attr( 'data-cv-idx', conf.columns );

					// Reorder buttons for new table order
					button
						.parent()
						.children('[data-cv-idx]')
						.sort( function (a, b) {
							return (a.getAttribute('data-cv-idx')*1) - (b.getAttribute('data-cv-idx')*1);
						} )
						.appendTo(button.parent());
				} );

			this.active( dt.column( conf.columns ).visible() );
		},
		destroy: function ( dt, button, conf ) {
			dt
				.off( 'column-visibility.dt'+conf.namespace )
				.off( 'column-reorder.dt'+conf.namespace );
		},

		_columnText: function ( dt, conf ) {
			// Use DataTables' internal data structure until this is presented
			// is a public API. The other option is to use
			// `$( column(col).node() ).text()` but the node might not have been
			// populated when Buttons is constructed.
			va{
  am_pm_abbreviated => [
    "a.\N{U+00a0}m.",
    "p.\N{U+00a0}m.",
  ],
  available_formats => {
    Bh => "h B",
    Bhm => "h:mm B",
    Bhms => "h:mm:ss B",
    E => "ccc",
    EBhm => "E h:mm B",
    EBhms => "E h:mm:ss B",
    EHm => "E, HH:mm",
    EHms => "E, HH:mm:ss",
    Ed => "E d",
    Ehm => "E, h:mm a",
    Ehms => "E, h:mm:ss a",
    Gy => "y G",
    GyMMM => "MMM y G",
    GyMMMEd => "E, d 'de' MMM 'de' y G",
    GyMMMM => "MMMM 'de' y G",
    GyMMMMEd => "E, d 'de' MMMM 'de' y G",
    GyMMMMd => "d 'de' MMMM 'de' y G",
    GyMMMd => "d MMM y G",
    H => "HH",
    Hm => "HH:mm",
    Hms => "HH:mm:ss",
    Hmsv => "HH:mm:ss v",
    Hmsvvvv => "HH:mm:ss (vvvv)",
    Hmv => "HH:mm v",
    M => "L",
    MEd => "E d-M",
    MMM => "LLL",
    MMMEd => "E, d MMM",
    MMMMEd => "E, d 'de' MMMM",
    "MMMMW-count-one" => "'semana' W 'de' MMMM",
    "MMMMW-count-other" => "'semana' W 'de' MMMM",
    MMMMd => "d 'de' MMMM",
    MMMd => "d MMM",
    MMMdd => "dd-MMM",
    MMd => "d/M",
    MMdd => "d/M",
    Md => "d/M",
    d => "d",
    h => "h a",
    hm => "h:mm a",
    hms => "hh:mm:ss",
    hmsv => "h:mm:ss a v",
    hmsvvvv => "h:mm:ss a (vvvv)",
    hmv => "h:mm a v",
    ms => "mm:ss",
    y => "y",
    yM => "M-y",
    yMEd => "E, d/M/y",
    yMM => "M/y",
    yMMM => "MMM y",
    yMMMEd => "E, d MMM y",
    yMMMM => "MMMM 'de' y",
    yMMMMEd => "EEE, d 'de' MMMM 'de' y",
    yMMMMd => "d 'de' MMMM 'de' y",
    yMMMd => "d 'de' MMM 'de' y",
    yMd => "d/M/y",
    yQQQ