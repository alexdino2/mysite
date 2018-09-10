/*!
 * wpexEqualHeights v1.0
 *
 * Copyright 2016 WPExplorer.com
 */

(function ( $ ) {

	$.fn.wpexEqualHeights = function( options ) {

		var $items   = this,
			$window  = $( window ),
			$targets = null;

		// Options
		var $settings = $.extend({
			children         : '',
			mobileBreakPoint : '',
			reset            : false
		}, options );

		// Return if no children found in DOM
		if ( ! $( $settings.children ).length ) return;

		// Function that sets heights
		function setHeights( reset ) {

			$items.each( function() {

				var $tallest  = 0; // Reset for each

				// Find and loop through target items
				if ( $settings.children ) {

					var $children = $( this ).find( $settings.children ).not( '.vc_row.vc_inner .wpex-vc-column-wrapper' );

					// Loop through children
					$children.each( function() {

						var $child = $( this );

						// Reset height
						if ( reset ) {
							$child.css( 'height', '' );
						}

						// Get tallest item
						$height = $child.outerHeight();
						if ( $height > $tallest ) {
							$tallest = $height;
						}
						
					} );

					// Set height of children
					$children.css( 'height', $tallest +'px' ); 

				}

			} );

		}

		// Set heights on init
		setHeights( false );

		// Update heights on resize
		$window.resize( function() {
			setHeights( true );
		} );

		// Chaining?
		//return this;

	}

}( jQuery ) );