( function( $ ) {

	'use strict';

	$( document ).ready( function( $ ) {

		$( '.wpex-social-share li' ).click( function( e ) {

			console.log( 'test' );

			var $this  = $( this ),
				data   = $this.parents( '.wpex-social-share' ).data(),
				sTitle = data.title,
				sUrl   = data.url,
				tUrl   = '',
				specs  = data.specs;

			if ( ! data ) {
				return;
			}

			var fTitle = $this.data( 'title' ) ? $this.data( 'title' ) : '';

			if ( $this.hasClass( 'wpex-twitter' ) ) {
				if ( data.twitterTitle ) {
					sTitle = data.twitterTitle;
				}
				tUrl = 'https://twitter.com/intent/tweet?original_referer=' + sUrl + '&amp;text=' + sTitle + '&amp;url=' + sUrl;
				if ( data.twitterHandle ) {
					tUrl += '&amp;via=' + data.twitterHandle;
				}
			} else if ( $this.hasClass( 'wpex-facebook' ) ) {
				tUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + sUrl;
			} else if ( $this.hasClass( 'wpex-googleplus' ) ) {
				tUrl = 'https://plus.google.com/share?url=' + data.url;
			} else if ( $this.hasClass( 'wpex-pinterest' ) ) {
				tUrl = 'https://www.pinterest.com/pin/create/button/?url=' + sUrl;
				if ( data.image ) {
					tUrl += '&amp;media=' + data.image;
				}
				if ( data.summary ) {
					tUrl += '&amp;description=' + data.summary;
				}
			} else if ( $this.hasClass( 'wpex-linkedin' ) ) {
				tUrl = 'https://www.linkedin.com/shareArticle?mini=true&amp;url=' + sUrl + '&amp;title=' + sTitle;
				if ( data.summary ) {
					tUrl += '&amp;summary=' + data.summary;
				}
				if ( data.source ) {
					tUrl += '&amp;source=' + data.source;
				}
			} else if ( $this.hasClass( 'wpex-email' ) ) {
				tUrl = 'mailto:?subject=' + data.emailSubject + '&body=' + data.emailBody;
				window.location.href = tUrl;
				return;
			} else if ( $this.children( 'a' ).attr( 'href' ) ) {
				tUrl = $this.children( 'a' ).attr( 'href' );
				specs = '';
			}

			if ( ! tUrl ) {
				return;
			}

			e.preventDefault();

			window.open(
				tUrl,
				fTitle,
				specs
			).focus();

		} );

	} );

} ) ( jQuery );