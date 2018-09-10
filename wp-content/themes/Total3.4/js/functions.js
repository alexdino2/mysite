/**
 * Project: Total WordPress Theme
 * Description: Initialize all scripts and add custom js
 * Author: WPExplorer
 * Theme URI: http://www.wpexplorer.com
 * Author URI: http://www.wpexplorer.com
 * License: Custom
 * License URI: http://themeforest.net/licenses
 * Version 3.4.0
 */

( function( $ ) {
	'use strict';

	var wpexTheme = {

		/**
		 * Main init function
		 *
		 * @since 2.0.0
		 */
		init : function() {
			this.config();
			this.bindEvents();
		},

		/**
		 * Define & Cache main variables
		 *
		 * @since 2.0.0
		 */
		config : function() {

			this.config = {

				// Main
				$window                 : $( window ),
				$document               : $( document ),
				$windowWidth            : $( window ).width(),
				$windowHeight           : $( window ).height(),
				$windowTop              : $( window ).scrollTop(),
				$body                   : $( 'body' ),
				$viewportWidth          : '',
				$is_rtl                 : false,
				$wpAdminBar             : null,
				$isRetina               : false,

				// Mobile
				$isMobile               : false,
				$mobileMenuStyle        : null,
				$mobileMenuToggleStyle  : null,
				$mobileMenuBreakpoint   : 960,

				// Header
				$siteHeader             : null,
				$siteHeaderStyle        : null,
				$siteHeaderHeight       : 0,
				$siteHeaderTop          : 0,
				$siteHeaderBottom       : 0,
				$verticalHeaderActive   : false,
				$hasHeaderOverlay       : false,
				$hasStickyHeader        : false,
				$hasStickyMobileHeader  : false,
				$hasStickyNavbar        : false,

				// Logo
				$siteLogo               : null,
				$siteLogoHeight         : 0,
				$siteLogoSrc            : null,
				$retinaLogo             : null,
				$siteNavWrap            : null,
				$siteNavDropdowns       : null,

				// Local Scroll
				$localScrollTargets     : 'li.local-scroll a, a.local-scroll, .local-scroll-link',
				$localScrollOffset      : 0,
				$localScrollSpeed       : 600,
				$localScrollSections    : [],	

				// Topbar
				$hasTopBar              : false,
				$hasStickyTopBar        : false,
				$stickyTopBar           : null,
				$hasStickyTopBarMobile  : false,

				// Footer
				$hasFixedFooter         : false,
				$hasFooterReveal        : false

			};

		},

		/**
		 * Bind Events
		 *
		 * @since 2.0.0
		 */
		bindEvents : function() {

			var self = this;

			// Run on document ready
			self.config.$document.on( 'ready', function() {

				// Update vars on init
				self.initUpdateConfig();

				// Page animations
				self.pageAnimations();

				// Main nav dropdowns
				self.superFish();

				// Calculate megamenu width
				self.megaMenusWidth();

				// Mobile menu
				self.mobileMenu();

				// Prevent menu item click
				self.navNoClick();

				// Hide/show post edit link
				self.hideEditLink();

				// Custom menu widget accordion
				self.customMenuWidgetAccordion();

				// Header 5 logo
				self.inlineHeaderLogo();

				// Menu search toggle,overlay,header replace
				self.menuSearch();

				// Header cart
				self.headerCart();

				// Back to top link
				self.backTopLink();

				// Scroll to comments
				self.smoothCommentScroll();

				// Tooltips
				self.tipsyTooltips();

				// Responsive text
				self.responsiveText();

				// Custom color hovers using data-attr
				self.customHovers();

				// Togglebar
				self.toggleBar();

				// Local scrolling links
				self.localScrollLinks();

				// Custom selects
				self.customSelects();

				// Skillbar
				self.skillbar();

				// Milestones
				self.milestone();

				// Carousels
				self.owlCarousel();

				// Archive masonry grids
				self.archiveMasonryGrids();

				// Lightbox
				self.iLightbox();

				// Overlay Hovers
				self.overlayHovers();

				// Isotope masonry grids
				self.isotopeGrids();
				if ( self.config.$body.hasClass( 'wpb-js-composer' ) ) {
					self.visualComposer();
				}

			} );

			// Run on Window Load
			self.config.$window.on( 'load', function() {
				var $headerStyle = self.config.$siteHeaderStyle;

				// Update config on window load
				self.windowLoadUpdateConfig();

				// Get correct mega menu top position
				self.megaMenusTop();

				// Setup flush dropdowns
				self.flushDropdownsTop();

				// Equal height elements
				self.equalHeights();

				// FadeIn elements
				self.fadeIn();

				// Parallax backgrounds
				self.parallax();

				// Re-position cart dropdown as needed
				self.cartSearchDropdownsRelocate();

				// Sliders
				self.sliderPro();

				// Sticky Topbar
				self.newStickyTopbar();

				// Sticky Header
				if ( self.config.$hasStickyHeader ) {
					var $stickyStyle = wpexLocalize.stickyHeaderStyle;
					if ( 'standard' == $stickyStyle
						|| 'shrink' == $stickyStyle
						|| 'shrink_animated' == $stickyStyle
					) {
						self.stickyHeader();
						self.shrinkStickyHeader();
					}
				}

				// Sticky Navbar
				if ( self.config.$hasStickyNavbar ) {
					self.stickyHeaderMenu();
				}

				// Sticky vcex navbar
				self.stickyVcexNavbar();

				// Footer Reveal => Must run before fixed footer!!!
				self.footerRevealInit();

				// Fixed Footer
				self.fixedFooter();

				// Scroll to hash
				window.setTimeout( function() {
					self.scrollToHash( self )
				}, 500 );

			} );

			// Run on Window Resize
			self.config.$window.resize( function() {

				// Window width change
				if ( self.config.$window.width() != self.config.$windowWidth ) {
					self.resizeUpdateConfig(); // update vars
					self.megaMenusWidth();
					self.inlineHeaderLogo();
					self.fixedFooter();
					self.footerRevealInit();
					self.cartSearchDropdownsRelocate();
				}

				// Window height change
				if ( self.config.$window.height() != self.config.$windowHeight ) {
					self.fixedFooter();
					self.footerRevealInit();
				}

			} );

			// Run on Scroll
			self.config.$window.scroll( function() {
				self.config.$windowTop = self.config.$window.scrollTop();
				self.localScrollHighlight();
				self.footerRevealScrollShow();
			} );

			// On orientation change
			self.config.$window.on( 'orientationchange',function() {
				self.resizeUpdateConfig();
				self.isotopeGrids();
				self.archiveMasonryGrids();
				self.inlineHeaderLogo();
			} );

		},

		/**
		 * Updates config on doc ready
		 *
		 * @since 3.0.0
		 */
		initUpdateConfig: function() {

			// Get Viewport width
			this.config.$viewportWidth = this.viewportWidth();

			// Check if retina
			this.config.$isRetina = this.retinaCheck();
			if ( this.config.$isRetina ) {
				this.config.$body.addClass( 'wpex-is-retina' );
			}

			// Mobile check & add mobile class to the header
			if ( this.mobileCheck() ) {
				this.config.$isMobile = true;
				this.config.$body.addClass( 'wpex-is-mobile-device' );
			}

			// Local scroll speed
			if ( wpexLocalize.localScrollSpeed ) {
				this.config.$localScrollSpeed = parseInt( wpexLocalize.localScrollSpeed );
			}

			// Define Wp admin bar
			var $wpAdminBar = $( '#wpadminbar' );
			if ( $wpAdminBar.length ) {
				this.config.$wpAdminBar = $wpAdminBar;
			}

			// Define header
			var $siteHeader = $( '#site-header' );
			if ( $siteHeader.length ) {
				this.config.$siteHeaderStyle = wpexLocalize.siteHeaderStyle;
				this.config.$siteHeader = $( '#site-header' );
			}

			// Define logo
			var $siteLogo = $( '#site-logo img' );
			if ( $siteLogo.length ) {
				this.config.$siteLogo = $siteLogo;
				this.config.$siteLogoSrc = this.config.$siteLogo.attr( 'src' );
			}

			// Menu Stuff
			var $siteNavWrap = $( '#site-navigation-wrap' );
			if ( $siteNavWrap.length ) {

				// Define menu
				this.config.$siteNavWrap = $siteNavWrap;

				// Check if sticky menu is enabled
				if ( wpexLocalize.hasStickyNavbar ) {
					this.config.$hasStickyNavbar = true;
				}

				// Store dropdowns
				var $siteNavDropdowns = $( '#site-navigation-wrap .dropdown-menu > .menu-item-has-children > ul' );
				if ( $siteNavWrap.length ) {
					this.config.$siteNavDropdowns = $siteNavDropdowns;
				}

				// Mobile menu Style
				this.config.$mobileMenuStyle       = wpexLocalize.mobileMenuStyle;
				this.config.$mobileMenuToggleStyle = wpexLocalize.mobileMenuToggleStyle;

				// Mobile menu breakpoint
				this.config.$mobileMenuBreakpoint   = wpexLocalize.mobileMenuBreakpoint;

			}

			// Get local scrolling sections
			this.config.$localScrollSections = this.localScrollSections();

			// Check if fixed footer is enabled
			if ( this.config.$body.hasClass( 'wpex-has-fixed-footer' ) ) {
				this.config.$hasFixedFooter = true;
			}
			
			// Footer reveal
			if ( $( '.footer-reveal' ).length && $( '#wrap' ).length && $( '#main' ).length ) {
				this.config.$hasFooterReveal = true;
			}

			// Header overlay
			if ( this.config.$siteHeader && this.config.$body.hasClass( 'has-overlay-header' ) ) {
				this.config.$hasHeaderOverlay = true;
			}

			// RTL
			if ( wpexLocalize.isRTL ) {
				this.config.$isRTL = true;
			}

			// Top bar enabled
			if ( $( '#top-bar-wrap' ).length ) {
				this.config.$hasTopBar = true;
				if ( $( '#top-bar-wrap' ).hasClass( 'wpex-top-bar-sticky' ) ) {
					this.config.$stickyTopBar = $( '#top-bar-wrap' );
				}
			}

			// Local scroll speed
			if ( wpexLocalize.localScrollSpeed ) {
				this.config.localScrollSpeed = parseInt( wpexLocalize.localScrollSpeed );
			}

			// Sticky Header => Mobile Check (must check first)
			if ( 'toggle' == this.config.$mobileMenuStyle ) {
				this.config.$hasStickyMobileHeader = false;
			} else {
				this.config.$hasStickyMobileHeader = wpexLocalize.hasStickyMobileHeader;
			}

			// Check if sticky header is enabled
			if ( this.config.$siteHeader && wpexLocalize.hasStickyHeader ) {
				this.config.$hasStickyHeader = true;
			}

			// Retina logo
			if ( typeof $wpexRetinaLogo !== 'undefined' && window.devicePixelRatio >= 2 ) {
				this.config.retinaLogo = $wpexRetinaLogo;
			}

			// Vertical header
			if ( this.config.$body.hasClass( 'wpex-has-vertical-header' ) ) {
				this.config.$verticalHeaderActive = true;
			}

			// Remove active class from has-scroll links
			// And save array of localscroll- links
			var $links = $( '#site-navigation a' );
			$links.each( function() {
				var $this = $( this ),
					$ref = $this.attr( 'href' );
					if ( $ref ) {
						if ( $ref.indexOf( 'localscroll-' ) != -1 ) {
							$this.parent( 'li' ).addClass( 'local-scroll' );
						}
					}
			} );

			// Sticky VCEX Navbar => Disable all other sticky elements
			if ( $( '.vcex-navbar-sticky' ).length ) {
				this.config.$hasStickyTopBar = false;
				this.config.$hasStickyHeader = false;
				this.config.$hasStickyNavbar = false;
			}

		},

		/**
		 * Updates config on window load
		 *
		 * @since 3.0.0
		 */
		windowLoadUpdateConfig: function() {

			// Header bottom position
			if ( this.config.$siteHeader ) {
				var $siteHeaderTop = this.config.$siteHeader.offset().top;
				this.config.$windowHeight = this.config.$window.height();
				this.config.$siteHeaderHeight = this.config.$siteHeader.outerHeight();
				this.config.$siteHeaderBottom = $siteHeaderTop + this.config.$siteHeaderHeight;
				this.config.$siteHeaderTop = $siteHeaderTop;
				if ( this.config.$siteLogo ) {
					this.config.$siteLogoHeight = this.config.$siteLogo.height();
				}
			}

			// Set localScrollOffset after site is loaded to make sure it includes dynamic items
			this.config.$localScrollOffset = this.parseLocalScrollOffset();

		},

		/**
		 * Updates config whenever the window is resized
		 *
		 * @since 3.0.0
		 */
		resizeUpdateConfig: function() {

			// Update main configs
			this.config.$windowHeight  = this.config.$window.height();
			this.config.$windowWidth   = this.config.$window.width();
			this.config.$windowTop     = this.config.$window.scrollTop();
			this.config.$viewportWidth = this.viewportWidth();

			// Update header height
			if ( this.config.$siteHeader ) {
				this.config.$siteHeaderHeight = this.config.$siteHeader.outerHeight();
			}

			// Get logo height
			if ( this.config.$siteLogo ) {
				this.config.$siteLogoHeight = this.config.$siteLogo.height();
			}

			// Vertical Header
			if ( this.config.$windowWidth < 960 ) {
				this.config.$verticalHeaderActive = false;
			} else if ( this.config.$body.hasClass( 'wpex-has-vertical-header' ) ) {
				this.config.$verticalHeaderActive = true;
			}

			// Local scroll offset => update last
			this.config.$localScrollOffset = this.parseLocalScrollOffset();

		},

		/**
		 * Retina Check
		 *
		 * @since 3.4.0
		 */
		retinaCheck: function() {
			var mediaQuery = '(-webkit-min-device-pixel-ratio: 1.5), (min--moz-device-pixel-ratio: 1.5), (-o-min-device-pixel-ratio: 3/2), (min-resolution: 1.5dppx)';
			if ( window.devicePixelRatio > 1 ) {
	            return true;
			}
	        if ( window.matchMedia && window.matchMedia( mediaQuery ).matches ) {
				return true;
			}
			return false;
		},

		/**
		 * Mobile Check
		 *
		 * @since 2.1.0
		 */
		mobileCheck: function() {
			if ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent ) ) {
				return true;
			}
		},

		/**
		 * Viewport width
		 *
		 * @since 3.4.0
		 */
		viewportWidth: function() {
			var e = window, a = 'inner';
			if ( !( 'innerWidth' in window ) ) {
				a = 'client';
				e = document.documentElement || document.body;
			}
			return e[ a+'Width' ];
		},

		/**
		 * Page Animations
		 *
		 * @since 2.1.0
		 */
		pageAnimations: function() {

			if ( ! $.fn.animsition ) {
				return;
			}

			// Return if wrapper doesn't exist
			if ( ! wpexLocalize.pageAnimation ) {
				return;
			}

			// Run animsition
			$( '.animsition' ).animsition( {
				touchSupport: false,
				inClass: wpexLocalize.pageAnimationIn,
				outClass: wpexLocalize.pageAnimationOut,
				inDuration: wpexLocalize.pageAnimationInDuration,
				outDuration: wpexLocalize.pageAnimationOutDuration,
				linkElement: 'a[href]:not([target="_blank"]):not([href^="#"]):not([href*="javascript"]):not([href*=".jpg"]):not([href*=".jpeg"]):not([href*=".gif"]):not([href*=".png"]):not([href*=".mov"]):not([href*=".swf"]):not([href*=".mp4"]):not([href*=".flv"]):not([href*=".avi"]):not([href*=".mp3"]):not([href^="mailto:"]):not([href*="?"]):not([href*="#localscroll"]):not([class="wcmenucart"])',
				loading: true
			} );

		},

		/**
		 * Superfish menus
		 *
		 * @since 2.0.0
		 */
		superFish: function() {

			if ( ! $.fn.superfish ) {
				return;
			}

			$( '#site-navigation ul.sf-menu' ).superfish( {
				delay: wpexLocalize.superfishDelay,
				animation: {
					opacity: 'show'
				},
				animationOut: {
					opacity: 'hide'
				},
				speed: wpexLocalize.superfishSpeed,
				speedOut: wpexLocalize.superfishSpeedOut,
				cssArrows: false,
				disableHI: false
			} );


		},

		 /**
		 * MegaMenus Width
		 *
		 * @since 2.0.0
		 */
		megaMenusWidth: function() {

			if ( ! this.config.$siteHeader || wpexLocalize.siteHeaderStyle !== 'one' ) {
				return;
			}

			var $siteNavigationWrap         = $( '#site-navigation-wrap' ),
				$headerContainerWidth       = this.config.$siteHeader.find( '.container' ).outerWidth(),
				$navWrapWidth               = $siteNavigationWrap.outerWidth(),
				$siteNavigationWrapPosition = $siteNavigationWrap.css( 'right' ),
				$siteNavigationWrapPosition = parseInt( $siteNavigationWrapPosition );

			if ( 'auto' == $siteNavigationWrapPosition ) {
				$siteNavigationWrapPosition = 0;
			}

			var $megaMenuNegativeMargin = $headerContainerWidth-$navWrapWidth-$siteNavigationWrapPosition;

			$( '#site-navigation-wrap .megamenu > ul' ).css( {
				'width'       : $headerContainerWidth,
				'margin-left' : -$megaMenuNegativeMargin
			} );

		},

		/**
		 * MegaMenus Top Position
		 *
		 * @since 2.0.0
		 */
		megaMenusTop: function() {
			var self = this;
			if ( ! self.config.$siteNavWrap
				|| ! self.config.$siteNavDropdowns
				|| ! self.config.$siteHeader.hasClass( 'header-one' )
			) {
				return;
			}
			var $window = this.config.$window;
			function setPosition() {
				var $headerHeight = self.config.$siteHeader.outerHeight(),
					$navHeight    = self.config.$siteNavWrap.outerHeight(),
					$megaMenuTop  = $headerHeight - $navHeight;
				$( '#site-navigation-wrap .megamenu > ul' ).css( {
					'top': $megaMenuTop/2 + $navHeight
				} );
			}
			setPosition();
			$window.scroll( function() {
				setPosition();
			} );
			$window.resize( function() {
				setPosition();
			} );
			$( '#site-navigation .megamenu > a' ).hover( function() {
				setPosition();
			} );
		},

		/**
		 * FlushDropdowns top positioning
		 *
		 * @since 2.0.0
		 */
		flushDropdownsTop: function() {
			var self = this;
			if ( ! self.config.$siteNavDropdowns || ! self.config.$siteNavWrap.hasClass( 'wpex-flush-dropdowns' ) ) {
				return;
			}
			var $window = this.config.$window;
			function setPosition() {
				if ( self.config.$siteNavWrap.is( ':visible' ) ) {
					var $headerHeight      = self.config.$siteHeader.outerHeight();
					var $siteNavWrapHeight = self.config.$siteNavWrap.outerHeight();
					var $dropTop           = $headerHeight - $siteNavWrapHeight;
					self.config.$siteNavDropdowns.css( 'top', $dropTop/2 + $siteNavWrapHeight );
				}
			}
			setPosition();
			$window.scroll( function() {
				setPosition();
			} );
			$window.resize( function() {
				setPosition();
			} );
			$( '.wpex-flush-dropdowns li.menu-item-has-children > a' ).hover( function() {
				setPosition();
			} );
		},

		/**
		 * Mobile Menu
		 *
		 * @since 2.0.0
		 */
		mobileMenu: function( event ) {

			var self = this;

			/***** Sidr Mobile Menu ****/
			if ( 'sidr' == this.config.$mobileMenuStyle && typeof wpexLocalize.sidrSource !== 'undefined' ) {

				var self = this;

				// Add sidr
				$( 'a.mobile-menu-toggle, li.mobile-menu-toggle > a' ).sidr( {
					name     : 'sidr-main',
					source   : wpexLocalize.sidrSource,
					side     : wpexLocalize.sidrSide,
					displace : wpexLocalize.sidrDisplace,
					speed    : parseInt( wpexLocalize.sidrSpeed ),
					renaming : true,
					onOpen   : function() {

						// Add extra classname
						$( '#sidr-main' ).addClass( 'wpex-mobile-menu' );

						// Prevent body scroll
						self.config.$body.addClass( 'wpex-noscroll' );

						// Declare useful vars
						var $hasChildren = $( '.sidr-class-menu-item-has-children' );

						// Add dropdown toggle (arrow)
						$hasChildren.children( 'a' ).append( '<span class="sidr-class-dropdown-toggle"></span>' );

						// Toggle dropdowns
						var $sidrDropdownTarget = $( '.sidr-class-dropdown-toggle' );

						// Check localization
						if ( wpexLocalize.sidrDropdownTarget == 'li' ) {
							$sidrDropdownTarget = $( '.sidr-class-sf-with-ul' );
						}

						// Add toggle click event
						$sidrDropdownTarget.on( 'click', function( event ) {

							// Define toggle vars
							if ( wpexLocalize.sidrDropdownTarget == 'li' ) {
								var $toggleParentLi = $( this ).parent( 'li' );
							} else {
								var $toggleParentLink = $( this ).parent( 'a' ),
									$toggleParentLi   = $toggleParentLink.parent( 'li' );
							}

							// Get parent items and dropdown
							var $allParentLis = $toggleParentLi.parents( 'li' ),
								$dropdown     = $toggleParentLi.children( 'ul' );

							// Toogle items
							if ( ! $toggleParentLi.hasClass( 'active' ) ) {
								$hasChildren.not( $allParentLis ).removeClass( 'active' ).children( 'ul' ).slideUp( 'fast' );
								$toggleParentLi.addClass( 'active' ).children( 'ul' ).slideDown( 'fast' );
							} else {
								$toggleParentLi.removeClass( 'active' ).children( 'ul' ).slideUp( 'fast' );
							}

							// Return false
							return false;

						} );

						// Add dark overlay to content
						self.config.$body.append( '<div class="wpex-sidr-overlay wpex-hidden"></div>' );
						$( '.wpex-sidr-overlay' ).fadeIn( wpexLocalize.sidrSpeed );

						/* Bind scroll - buggy
						$( '#sidr-main' ).bind( 'mousewheel DOMMouseScroll', function ( e ) {
							var e0 = e.originalEvent,
								delta = e0.wheelDelta || -e0.detail;
							this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
							e.preventDefault();
						} );*/

						// Close sidr when clicking toggle
						$( 'a.sidr-class-toggle-sidr-close' ).on( 'click', function( event ) {
							$.sidr( 'close', 'sidr-main' );
							return false;
						} );

						// Close sidr when clicking on overlay
						$( '.wpex-sidr-overlay' ).on( 'click', function( event ) {
							$.sidr( 'close', 'sidr-main' );
							return false;
						} );

						// Close on resize
						self.config.$window.resize( function() {
							if ( self.config.$windowWidth >= self.config.$mobileMenuBreakpoint ) {
								$.sidr( 'close', 'sidr-main' );
							}
						} );

					},
					onClose : function() {

						// Allow body scroll
						self.config.$body.removeClass( 'wpex-noscroll' );

						// Remove active dropdowns
						$( '.sidr-class-menu-item-has-children.active' ).removeClass( 'active' ).children( 'ul' ).hide();
						
						// FadeOut overlay
						$( '.wpex-sidr-overlay' ).fadeOut( wpexLocalize.sidrSpeed, function() {
							$( this ).remove();
						} );
					}

				} );

				// Close when clicking local scroll link
				$( 'li.sidr-class-local-scroll > a' ).click( function() {
					var $hash = this.hash;
					if ( $.inArray( $hash, self.config.$localScrollSections ) > -1 ) {
						$.sidr( 'close', 'sidr-main' );
						self.scrollTo( $hash );
						return false;
					}
				} );

			}

			/***** Toggle Mobile Menu ****/
			else if ( 'toggle' == self.config.$mobileMenuStyle && self.config.$siteHeader ) {

				var $classes = 'mobile-toggle-nav wpex-mobile-menu wpex-clr';

				// Insert nav
				if ( $( '#wpex-mobile-menu-fixed-top' ).length ) {
					$( '#wpex-mobile-menu-fixed-top' ).append( '<nav class="'+ $classes +'"></nav>' );
				}

				// Overlay header
				else if ( self.config.$hasHeaderOverlay && $( '#overlay-header-wrap' ).length ) {
					$( '<nav class="'+ $classes +'"></nav>' ).insertBefore( "#overlay-header-wrap" );
				}

				// Normal toggle insert
				else {
					$( '<nav class="'+ $classes +'"></nav>' ).insertAfter( self.config.$siteHeader );
				}

				// Grab all content from menu and add into mobile-toggle-nav element
				if ( $( '#mobile-menu-alternative' ).length ) {
					var mobileMenuContents = $( '#mobile-menu-alternative .dropdown-menu' ).html();
				} else {
					var mobileMenuContents = $( '#site-navigation .dropdown-menu' ).html();
				}
				$( '.mobile-toggle-nav' ).html( '<ul class="mobile-toggle-nav-ul">' + mobileMenuContents + '</ul>' );

				// Remove all styles
				$( '.mobile-toggle-nav-ul, .mobile-toggle-nav-ul *' ).children().each( function() {
					var attributes = this.attributes;
					$( this ).removeAttr( 'style' );
				} );

				// Add classes where needed
				$( '.mobile-toggle-nav-ul' ).addClass( 'container' );

				// Show/Hide
				$( '.mobile-menu-toggle' ).on( self.config.$isMobile ? 'touchstart' : 'click', function( event ) {
					if ( wpexLocalize.animateMobileToggle ) {
						$( '.mobile-toggle-nav' ).stop(true,true).slideToggle( 'fast' ).toggleClass( 'visible' );
					} else {
						$( '.mobile-toggle-nav' ).toggle().toggleClass( 'visible' );
					}
					return false;
				} );

				// Close on resize
				self.config.$window.resize( function() {
					if ( self.config.$windowWidth >= self.config.$mobileMenuBreakpoint && $( '.mobile-toggle-nav' ).length ) {
						$( '.mobile-toggle-nav' ).hide().removeClass( 'visible' );
					}
				} );

				// Add search to toggle menu
				var $mobileSearch = $( '#mobile-menu-search' );
				if ( $mobileSearch.length ) {
					$( '.mobile-toggle-nav' ).append( '<div class="mobile-toggle-nav-search container"></div>' );
					$( '.mobile-toggle-nav-search' ).append( $mobileSearch );
				}

			}

			/***** Full-Screen Overlay Mobile Menu ****/
			else if ( 'full_screen' == self.config.$mobileMenuStyle && self.config.$siteHeader ) {

				// Style
				var $style = wpexLocalize.fullScreenMobileMenuStyle ? wpexLocalize.fullScreenMobileMenuStyle : false;

				// Insert new nav
				self.config.$body.append( '<div class="full-screen-overlay-nav wpex-mobile-menu wpex-clr '+ $style +'"><span class="full-screen-overlay-nav-close"></span><nav class="full-screen-overlay-nav-ul-wrapper"><ul class="full-screen-overlay-nav-ul"></ul></nav></div>' );

				// Grab all content from menu and add into mobile-toggle-nav element
				if ( $( '#mobile-menu-alternative' ).length ) {
					var mobileMenuContents = $( '#mobile-menu-alternative .dropdown-menu' ).html();
				} else {
					var mobileMenuContents = $( '#site-navigation .dropdown-menu' ).html();
				}
				$( '.full-screen-overlay-nav-ul' ).html( mobileMenuContents );

				// Remove all styles
				$( '.full-screen-overlay-nav, .full-screen-overlay-nav *' ).children().each( function() {
					var attributes = this.attributes;
					$( this ).removeAttr( 'style' );
				} );

				// Show
				$( '.mobile-menu-toggle' ).on( self.config.$isMobile ? 'touchstart' : 'click', function( event ) {
					$( '.full-screen-overlay-nav' ).addClass( 'visible' );
					self.config.$body.addClass( 'wpex-noscroll' );
					return false;
				} );

				// Hide
				$( '.full-screen-overlay-nav-close' ).on( self.config.$isMobile ? 'touchstart' : 'click', function( event ) {
					$( '.full-screen-overlay-nav' ).removeClass( 'visible' );
					self.config.$body.removeClass( 'wpex-noscroll' );
					return false;
				} );

			}

		},

		/**
		 * Prevent clickin on links
		 *
		 * @since 2.0.0
		 */
		navNoClick: function() {
			$( 'li.nav-no-click > a, li.sidr-class-nav-no-click > a' ).live( 'click', function() {
				return false;
			} );
		},

		/**
		 * Header Search
		 *
		 * @since 2.0.0
		 */
		menuSearch: function() {

			var self = this;

			/**** Menu Search > Dropdown ****/
			if ( 'drop_down' == wpexLocalize.menuSearchStyle ) {

				var $searchDropdownToggle = $( 'a.search-dropdown-toggle' );
				var $searchDropdownForm   = $( '#searchform-dropdown' );

				$searchDropdownToggle.click( function( event ) {
					// Display search form
					$searchDropdownForm.toggleClass( 'show' );
					// Active menu item
					$( this ).parent( 'li' ).toggleClass( 'active' );
					// Focus
					var $transitionDuration = $searchDropdownForm.css( 'transition-duration' );
					$transitionDuration = $transitionDuration.replace( 's', '' ) * 1000;
					if ( $transitionDuration ) {
						setTimeout( function() {
							$searchDropdownForm.find( 'input[type="search"]' ).focus();
						}, $transitionDuration );
					}
					// Hide other things
					$( 'div#current-shop-items-dropdown' ).removeClass( 'show' );
					$( 'li.wcmenucart-toggle-dropdown' ).removeClass( 'active' );
					// Return false
					return false;
				} );

				// Close on doc click
				self.config.$document.on( 'click', function( event ) {
					if ( ! $( event.target ).closest( '#searchform-dropdown.show' ).length ) {
						$searchDropdownToggle.parent( 'li' ).removeClass( 'active' );
						$searchDropdownForm.removeClass( 'show' );
					}
				} );

			}

			/**** Menu Search > Overlay Modal ****/
			else if ( 'overlay' == wpexLocalize.menuSearchStyle ) {

				if ( ! $.fn.leanerModal ) {
					return;
				}

				var $searchOverlayToggle = $( 'a.search-overlay-toggle' );

				$searchOverlayToggle.leanerModal( {
					'id'      : '#searchform-overlay',
					'top'     : 100,
					'overlay' : 0.8
				} );

				$searchOverlayToggle.click( function() {
					$( '#site-searchform input' ).focus();
				} );

			}
			
			/**** Menu Search > Header Replace ****/
			else if ( 'header_replace' == wpexLocalize.menuSearchStyle ) {

				// Show
				var $headerReplace = $( '#searchform-header-replace' );
				$( 'a.search-header-replace-toggle' ).click( function( event ) {
					// Display search form
					$headerReplace.toggleClass( 'show' );
					// Focus
					var $transitionDuration =  $headerReplace.css( 'transition-duration' );
					$transitionDuration = $transitionDuration.replace( 's', '' ) * 1000;
					if ( $transitionDuration ) {
						setTimeout( function() {
							$headerReplace.find( 'input[type="search"]' ).focus();
						}, $transitionDuration );
					}
					// Return false
					return false;
				} );

				// Close on click
				$( '#searchform-header-replace-close' ).click( function() {
					$headerReplace.removeClass( 'show' );
					return false;
				} );

				// Close on doc click
				self.config.$document.on( 'click', function( event ) {
					if ( ! $( event.target ).closest( $( '#searchform-header-replace.show' ) ).length ) {
						$headerReplace.removeClass( 'show' );
					}
				} );
			}

		},

		/**
		 * Header Cart
		 *
		 * @since 2.0.0
		 */
		headerCart: function() {

			if ( $( 'a.wcmenucart' ).hasClass( 'go-to-shop' ) ) {
				return;
			}

			// Drop-down
			if ( 'drop_down' == wpexLocalize.wooCartStyle ) {

				// Display cart dropdown
				$( '.toggle-cart-widget' ).click( function( event ) {
					$( '#searchform-dropdown' ).removeClass( 'show' );
					$( 'a.search-dropdown-toggle' ).parent( 'li' ).removeClass( 'active' );
					$( 'div#current-shop-items-dropdown' ).toggleClass( 'show' );
					$( this ).toggleClass( 'active' );
					return false;
				} );

				// Hide cart dropdown
				$( 'div#current-shop-items-dropdown' ).click( function( event ) {
					event.stopPropagation(); 
				} );
				this.config.$document.click( function() {
					$( 'div#current-shop-items-dropdown' ).removeClass( 'show' );
					$( 'li.wcmenucart-toggle-dropdown' ).removeClass( 'active' );
				} );

				/* Prevent body scroll on current shop dropdown - seems buggy...
				$( '#current-shop-items-dropdown' ).bind( 'mousewheel DOMMouseScroll', function ( e ) {
					var e0 = e.originalEvent,
						delta = e0.wheelDelta || -e0.detail;
					this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
					e.preventDefault();
				} );*/

			}

			// Modal
			else if ( 'overlay' == wpexLocalize.wooCartStyle ) {

				if ( ! $.fn.leanerModal ) {
					return;
				}

				$( '.toggle-cart-widget' ).leanerModal( {
					id: '#current-shop-items-overlay',
					top: 100,
					overlay: 0.8
				} );

			}

		},

		/**
		 * Relocate the cart and search dropdowns for specific header styles
		 *
		 * @since 2.0.0
		 */
		cartSearchDropdownsRelocate: function() {

			// Get last menu item
			var $lastMenuItem = $( '#site-navigation .dropdown-menu > li:nth-last-child(1)' );

			// Validate first
			if ( this.config.$hasHeaderOverlay
				|| ! this.config.$siteHeader
				|| ! $lastMenuItem.length
				|| ! this.config.$siteHeader.hasClass( 'wpex-reposition-cart-search-drops' )
			) {
				return;
			}

			// Define search and cart elements
			var $searchDrop = $( '#searchform-dropdown' ),
				$shopDrop   = $( '#current-shop-items-dropdown');

			// Get last menu item offset
			var $lastMenuItemOffset = $lastMenuItem.position();

			// Position search dropdown
			if ( $searchDrop.length ) {

				var $searchDropPosition = $lastMenuItemOffset.left - $searchDrop.outerWidth() + $lastMenuItem.width();

				$searchDrop.css( {
					'right' : 'auto',
					'left'  : $searchDropPosition
				} );

			}

			// Position Woo dropdown
			if ( $shopDrop.length ) {

				var $shopDropPosition = $lastMenuItemOffset.left - $shopDrop.outerWidth() + $lastMenuItem.width();

				$shopDrop.css( {
					'right': 'auto',
					'left': $shopDropPosition
				} );

			}

		},

		/**
		 * Hide post edit link
		 *
		 * @since 2.0.0
		 */
		hideEditLink: function() {

			$( 'a.hide-post-edit' ).click( function() {
				$( 'div.post-edit' ).hide();
				return false;
			} );

		},

		/**
		 * Custom menu widget toggles
		 *
		 * @since 2.0.0
		 */
		customMenuWidgetAccordion: function() {

			var self = this;

			$( '#main .widget_nav_menu .current-menu-ancestor' ).addClass( 'active' ).children( 'ul' ).show();

			$( '#main .widget_nav_menu' ).each( function() {
				var $widgetMenu  = $( this ),
					$hasChildren = $( this ).find( '.menu-item-has-children' ),
					$allSubs     = $hasChildren.children( '.sub-menu' );
				$hasChildren.each( function() {
					$( this ).addClass( 'parent' );
					var $links = $( this ).children( 'a' );
					$links.on( self.config.$isMobile ? 'touchstart' : 'click', function( event ) {
						var $linkParent = $( this ).parent( 'li' ),
							$allParents = $linkParent.parents( 'li' );
						if ( ! $linkParent.hasClass( 'active' ) ) {
							$hasChildren.not( $allParents ).removeClass( 'active' ).children( '.sub-menu' ).slideUp( 'fast' );
							$linkParent.addClass( 'active' ).children( '.sub-menu' ).stop( true, true ).slideDown( 'fast' );
						} else {
							$linkParent.removeClass( 'active' ).children( '.sub-menu' ).stop( true, true ).slideUp( 'fast' );
						}
						return false;
					} );
				} );
			} );

		},

		/**
		 * Header 5 - Inline Logo
		 *
		 * @since 2.0.0
		 */
		inlineHeaderLogo: function() {

			// Only needed for header style 5
			if ( 'five' != wpexLocalize.siteHeaderStyle ) {
				return;
			}

			var $headerLogo        = $( '#site-header-inner > .header-five-logo' ),
				$headerNav         = $( '#site-header-inner .navbar-style-five' ),
				$navLiCount        = $headerNav.children( '#site-navigation' ).children( 'ul' ).children( 'li' ).size(),
				$navBeforeMiddleLi = Math.round( $navLiCount / 2 ) - parseInt( wpexLocalize.headerFiveSplitOffset ),
				$centeredLogo      = $( '.menu-item-logo .header-five-logo' );

				// Add logo into menu
				if ( this.config.$windowWidth >= this.config.$mobileMenuBreakpoint && $headerLogo.length && $headerNav.length ) {
					$('<li class="menu-item-logo"></li>').insertAfter( $headerNav.find( '#site-navigation > ul > li:nth( '+ $navBeforeMiddleLi +' )' ) );
						$headerLogo.appendTo( $headerNav.find( '.menu-item-logo' ) );
				}

				// Remove logo from menu and add to header
				if ( this.config.$windowWidth < this.config.$mobileMenuBreakpoint && $centeredLogo.length ) {
					$centeredLogo.prependTo( $( '#site-header-inner' ) );
					$( '.menu-item-logo' ).remove();
				}

			// Add display class to logo (hidden by default)
			$headerLogo.addClass( 'display' );

		},

		/**
		 * Back to top link
		 *
		 * @since 2.0.0
		 */
		backTopLink: function() {

			var self = this,
				$scrollTopLink = $( 'a#site-scroll-top' );

			if ( $scrollTopLink.length ) {

				var $speed = wpexLocalize.windowScrollTopSpeed ? wpexLocalize.windowScrollTopSpeed : 2000,
					$speed = parseInt( $speed );

				this.config.$window.scroll( function() {
					if ( $( this ).scrollTop() > 100 ) {
						$scrollTopLink.addClass( 'show' );
					} else {
						$scrollTopLink.removeClass( 'show' );
					}
				} );

				$scrollTopLink.on( self.config.$isMobile ? 'touchstart' : 'click', function( event ) {
					$( 'html, body' ).stop(true,true).animate( {
						scrollTop : 0
					}, $speed );
					return false;
				} );

			}

		},

		/**
		 * Smooth Comment Scroll
		 *
		 * @since 2.0.0
		 */
		smoothCommentScroll: function() {

			$( '.single li.comment-scroll a' ).click( function( event ) {
				$( 'html, body' ).stop(true,true).animate( {
					scrollTop: $( this.hash ).offset().top -180
				}, 'normal' );
				return false;
			} );

		},

		/**
		 * Tooltips
		 *
		 * @since 2.0.0
		 */
		tipsyTooltips: function() {

			$( 'a.tooltip-left' ).tipsy( {
				fade    : true,
				gravity : 'e'
			} );

			$( 'a.tooltip-right' ).tipsy( {
				fade    : true,
				gravity : 'w'
			} );

			$( 'a.tooltip-up' ).tipsy( {
				fade    : true,
				gravity : 's'
			} );

			$( 'a.tooltip-down' ).tipsy( {
				fade    : true,
				gravity : 'n'
			} );

		},


		/**
		 * Tooltips
		 *
		 * @since 3.2.0
		 */
		responsiveText: function() {

			var self = this,
				$responsiveText = $( '.wpex-responsive-txt' );

			$responsiveText.each( function() {

				var $this  = $( this ),
					$data  = $this.data(),
					$min   = self.parseData( $data.minFontSize, 13 ),
					$max   = self.parseData( $data.maxFontSize, 40 ),
					$ratio = self.parseData( $data.responsiveTextRatio, 10 );

				$this.flowtype( {
					fontRatio : $ratio,
					minFont   : $min,
					maxFont   : $max
				} );

			} );

		},

		/**
		 * Custom hovers using data attributes
		 *
		 * @since 2.0.0
		 */
		customHovers: function() {

			$( '.wpex-data-hover' ).each( function() {

				var $this = $( this ),
					$originalBg = $( this ).css( 'backgroundColor' ),
					$originalColor = $( this ).css( 'color' ),
					$hoverBg = $( this ).attr( 'data-hover-background' ),
					$hoverColor = $( this ).attr( 'data-hover-color' );

				$this.hover( function () {
					if ( CSSStyleDeclaration.prototype.setProperty !== 'undefined' ) {
						if ( $hoverBg ) {
							this.style.setProperty( 'background-color', $hoverBg, 'important' );
						}
						if ( $hoverColor ) {
							this.style.setProperty( 'color', $hoverColor, 'important' );
						}
					} else {
						if ( $hoverBg ) {
							$this.css( 'background-color', $hoverBg );
						}
						if ( $hoverColor ) {
							$this.css( 'color', $hoverColor );
						}
					}
				}, function () {
					if ( CSSStyleDeclaration.prototype.setProperty !== 'undefined' ) {
						if ( $hoverBg ) {
							this.style.setProperty( 'background-color', $originalBg, 'important' );
						}
						if ( $hoverColor ) {
							this.style.setProperty( 'color', $originalColor, 'important' );
						}
					} else {
						if ( $hoverBg && $originalBg ) {
							$this.css( 'background-color', $originalBg );
						}
						if ( $hoverColor && $originalColor ) {
							$this.css( 'color', $originalColor );
						}
					}
				} );

			} );

		},


		/**
		 * Togglebar toggle
		 *
		 * @since 2.0.0
		 */
		toggleBar: function() {

			var self = this;
			var $toggleBtn = $( 'a.toggle-bar-btn' );
			var $toggleBarWrap = $( '#toggle-bar-wrap' );

			if ( $toggleBtn.length && $toggleBarWrap.length ) {

				$toggleBtn.on( self.config.$isMobile ? 'touchstart' : 'click', function( event ) {
					var $fa = $( '.toggle-bar-btn' ).find( '.fa' );
					$fa.toggleClass( $toggleBtn.data( 'icon' ) );
					$fa.toggleClass( $toggleBtn.data( 'icon-hover' ) );
					$toggleBarWrap.toggleClass( 'active-bar' );
					return false;
				} );

				// Close on doc click
				self.config.$document.on( 'click', function( event ) {
					if ( ! $( event.target ).closest( '#toggle-bar-wrap.active-bar' ).length ) {
						$toggleBarWrap.removeClass( 'active-bar' );
						$toggleBtn.children( '.fa' ).removeClass( $toggleBtn.data( 'icon-hover' ) ).addClass( $toggleBtn.data( 'icon' ) );
					}
				} );

			}

		},

		/**
		 * Skillbar
		 *
		 * @since 2.0.0
		 */
		skillbar: function() {

			$( '.vcex-skillbar' ).each( function() {
				var $this = $( this );
				$this.appear( function() {
					$this.find( '.vcex-skillbar-bar' ).animate( {
						width: $( this ).attr( 'data-percent' )
					}, 800 );
				} );
			}, {
				accX : 0,
				accY : 0
			} );

		},

		/**
		 * Milestones
		 *
		 * @since 2.0.0
		 */
		milestone: function() {

			$( '.vcex-animated-milestone' ).each( function() {
				$( this ).appear( function() {
					$( this ).find( '.vcex-milestone-time' ).countTo( {
						formatter: function ( value, options ) {
							return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, wpexLocalize.milestoneDecimalFormat );
						},
					} );
				}, {
					accX : 0,
					accY : 0
				} );
			} );

		},

		/**
		 * Advanced Parallax
		 *
		 * @since 2.0.0
		 */
		parallax: function() {

			$( '.wpex-parallax-bg' ).each( function() {
				var $this = $( this );
				$this.scrolly2().trigger( 'scroll' );
				$this.css( {
					'opacity' : 1
				} );
			} );

		},

		/**
		 * Local Scroll Offset
		 *
		 * @since 2.0.0
		 */
		parseLocalScrollOffset: function() {
			var self    = this;
			var $offset = 0;

			// Return custom offset
			if ( wpexLocalize.localScrollOffset ) {
				return wpexLocalize.localScrollOffset;
			}

			// VCEX Navbar module
			if ( $( '.vcex-navbar-sticky' ).length ) {
				$offset = parseInt( $offset ) + parseInt( $( '.vcex-navbar-sticky' ).outerHeight() );
			}

			// Fixed header
			if ( self.config.$hasStickyHeader ) {


				// Return 0 for small screens if mobile fixed header is disabled
				if ( ! self.config.$hasStickyMobileHeader && self.config.$windowWidth <= wpexLocalize.stickyHeaderBreakPoint ) {
					$offset = 0;
				}

				// Return header height
				else {

					// Shrink header
					if ( self.config.$siteHeader.hasClass( 'shrink-sticky-header' ) ) {
						$offset = wpexLocalize.shrinkHeaderHeight;
					}

					// Standard header
					else {
						$offset = self.config.$siteHeaderHeight;
					}

				}

			}

			// Fixed Nav
			if ( self.config.$hasStickyNavbar ) {
				if ( self.config.$windowWidth >= wpexLocalize.stickyHeaderBreakPoint ) {
					$offset = parseInt( $offset ) + parseInt( $( '#site-navigation-wrap' ).outerHeight() );
				}
			}

			// Add sticky topbar height offset
			if ( self.config.$stickyTopBar ) {
				$offset = parseInt( $offset ) + parseInt( self.config.$stickyTopBar.outerHeight() );
			}

			// Add wp toolbar
			if ( $( '#wpadminbar' ).length ) {
				$offset = parseInt( $offset ) +  parseInt( $( '#wpadminbar' ).outerHeight() );
			}

			// Add 1 extra decimal to prevent cross browser rounding issues
			$offset = $offset ? $offset - 1 : 0;

			// Return offset
			return $offset;

		},

		/**
		 * Local scroll links array
		 *
		 * @since 2.0.0
		 */
		localScrollSections: function() {

			// Define main vars
			var $array = [],
				$links = $( this.config.$localScrollTargets );

			// Loop through links
			for ( var i=0; i < $links.length; i++ ) {

				// Add to array and save hash
				var $link = $links[i],
					$hash = '#' + $( $link ).attr('href').replace(/^.*?(#|$)/,'');

				// Hash required
				if ( $hash ) {

					// Add custom data attribute to each
					$( $link ).attr( 'data-ls_linkto', $hash );
					//$( $link ).parent( 'li.current-menu-item' ).removeClass( 'current-menu-item' );

					// Data attribute targets
					if ( $( '[data-ls_id="'+ $hash +'"]' ).length ) {
						if ( $.inArray( $hash, $array ) == -1 ) {
							$array.push( $hash );
						}
					}

					// Standard ID targets
					else if ( $( $hash ).length ) {
						if ( $.inArray( $hash, $array ) == -1 ) {
							$array.push( $hash );
						}
					}

				}

			}

			// Return array of local scroll links
			return $array;

		},

		/**
		 * Scroll to function
		 *
		 * @since 2.0.0
		 */
		scrollTo: function( hash, offset, callback ) {

			// Hash is required
			if ( ! hash ) {
				return;
			}

			// Define important vars
			var self          = this,
				$target       = null,
				$page         = $( 'html, body' ),
				$isLsDataLink = false, // we can do special things here
				$lsSpeed      = self.config.$localScrollSpeed ? parseInt( self.config.$localScrollSpeed ) : 1000;

			// Check for target in data attributes
			var $lsTarget = $( '[data-ls_id="'+ hash +'"]' );

			if ( $lsTarget.length ) {
				$target       = $lsTarget;
				$isLsDataLink = true;
			}

			// Check for targets with localscroll- in hash
			else if ( hash.indexOf( 'localscroll-' ) != -1 ) {
				var $parseHash = hash.replace( 'localscroll-', '' );
				$lsTarget = $( '[data-ls_id="'+ $parseHash +'"]' );
				if ( $lsTarget.length ) {
					$target = $lsTarget;
				} else {
					$target = $( $parseHash );
				}
			}

			// Check for straight up element with ID
			else {
				$target = $( hash );
			}

			// Target check
			if ( $target.length ) {

				// Update hash
				if ( hash && $isLsDataLink && wpexLocalize.localScrollUpdateHash ) {
					window.location.hash = hash;
				}

				// Mobile toggle Menu needs it's own code
				var $mobileToggleNav = $( '.mobile-toggle-nav' );
				if ( $mobileToggleNav.length && $mobileToggleNav.is( ':visible' ) ) {
					if ( wpexLocalize.animateMobileToggle ) {
						$( '.mobile-toggle-nav' ).slideUp( 'fast', function() {
							$( '.mobile-toggle-nav' ).removeClass( 'visible' );
							$page.stop( true, true ).animate( {
								scrollTop: $target.offset().top
							}, $lsSpeed );
						} );
					} else {
						$( '.mobile-toggle-nav' ).hide().removeClass( 'visible' );
						$page.stop( true, true ).animate( {
							scrollTop: $target.offset().top
						}, $lsSpeed );
					}

				}

				// Scroll to target
				else {

					// Get offset
					var $scrollTo = offset ? offset : $target.offset().top - self.config.$localScrollOffset;

					/* Stop animation if user tries to scroll while animating (BUGGY)
					$page.on( 'scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove', function() {
				       $page.stop();
				 	} );*/

					// Animate scroll
					$page.stop( true, true ).animate( {
						scrollTop: $scrollTo
					}, $lsSpeed );

				}

			}

		},

		/**
		 * Local Scroll link
		 *
		 * @since 2.0.0
		 */
		localScrollLinks: function() {

			// Set global object to "self" var
			var self = this,
				$localScrollSections = self.config.$localScrollSections;

			// Local Scroll - Menus
			$( this.config.$localScrollTargets ).on( 'click', function() {
				var $hash = this.hash;
				if ( $.inArray( $hash, $localScrollSections ) > -1 ) {
					self.scrollTo( $hash );
					return false;
				}
			} );

			// LocalScroll Woocommerce Reviews
			$( 'body.single div.entry-summary a.woocommerce-review-link' ).click( function() {
				var $hash   = this.hash,
					$target = $( $hash );
				if ( $target.length ) {
					var $offset = $target.offset().top - self.config.$localScrollOffset - 20;
					self.scrollTo( $hash, $offset );
				}
				return false;
			} );

		},

		/**
		 * Local Scroll Highlight on scroll
		 *
		 * @since 2.0.0
		 */
		localScrollHighlight: function() {

			// Get local scroll array
			var self              = this;
			var $localScrollSections = self.config.$localScrollSections;

			// Return if there aren't any local scroll items
			if ( ! $localScrollSections.length ) {
				return;
			}

			// Define vars
			var $windowPos    = this.config.$window.scrollTop(),
				$windowHeight = this.config.$windowHeight,
				$docHeight    = this.config.$document.height();

			// Highlight active items
			for ( var i=0; i < $localScrollSections.length; i++ ) {

				// Get section
				var $section = $localScrollSections[i];

				// Data attribute targets
				if ( $( '[data-ls_id="'+ $section +'"]' ).length ) {
					var $targetDiv     = $( '[data-ls_id="'+ $section +'"]' ),
						$divPos        = $targetDiv.offset().top - self.config.$localScrollOffset - 1,
						$divHeight     = $targetDiv.outerHeight(),
						$higlight_link = $( '[data-ls_linkto="'+ $section +'"]' );
				}

				// Standard element targets
				else if ( $( $section ).length ) {
					var $divPos        = $( $section ).offset().top - self.config.$localScrollOffset - 1,
						$divHeight     = $( $section ).outerHeight(),
						$higlight_link = $( '[data-ls_linkto="'+ $section +'"]' );
				}

				// Higlight items
				if ( $windowPos >= $divPos && $windowPos < ( $divPos + $divHeight ) ) {
						$higlight_link.addClass( 'active' );
						$higlight_link.parent( 'li' ).addClass( 'current-menu-item' );
				} else {
					$higlight_link.removeClass( 'active' );
					$higlight_link.parent( 'li' ).removeClass( 'current-menu-item' );
				}

			}

			/* Highlight last item if at bottom of page - needs major testing now.
			var $lastLink = $localScrollSections[$localScrollSections.length-1];
			if ( $windowPos + $windowHeight == $docHeight ) {
				$( '.local-scroll.current-menu-item' ).removeClass( 'current-menu-item' );
				$( "li.local-scroll a[href='" + $lastLink + "']" ).parent( 'li' ).addClass( 'current-menu-item' );
			}*/

		},

		/**
		 * Scroll to Hash
		 *
		 * @since 2.0.0
		 */
		scrollToHash: function( $this ) {

			// Declare function vars
			var self  = $this,
				$hash = location.hash;

			// Hash needed
			if ( ! $hash ) {
				return;
			}

			// Scroll to hash for localscroll links
			if ( $hash.indexOf( 'localscroll-' ) != -1 ) {
				self.scrollTo( $hash.replace( 'localscroll-', '' ) );
				return;
			}

			// Check elements with data attributes
			else if ( $( '[data-ls_id="'+ $hash +'"]' ).length ) {
				self.scrollTo( $hash );
			}

		},

		/**
		 * Equal heights function
		 *
		 * @since 2.0.0
		 */
		equalHeights: function() {

			if ( $.fn.wpexEqualHeights!=undefined ) {

				// Add equal heights grid
				$( '.match-height-grid' ).wpexEqualHeights( {
					children : '.match-height-content'
				} );

				// Columns
				$( '.match-height-row' ).wpexEqualHeights( {
					children : '.match-height-content'
				} );

				// Feature Box
				$( '.vcex-feature-box-match-height' ).wpexEqualHeights( {
					children : '.vcex-match-height'
				} );

				// Blog entries
				$( '.blog-entry-equal-heights' ).wpexEqualHeights( {
					children : '.blog-entry-inner'
				} );

				// Rows
				$( '.wpex-vc-row-columns-match-height' ).wpexEqualHeights( {
					children : '.wpex-vc-column-wrapper'
				} );

				// Equal Height class targeting itself
				$( '.wpex-vc-columns-wrap' ).wpexEqualHeights( {
					children : '.equal-height-column'
				} );
				$( '.wpex-vc-columns-wrap' ).wpexEqualHeights( {
					children : '.equal-height-content'
				} );

			}

		},

		/**
		 * Footer Reveal Display on Load
		 *
		 * @since 2.0.0
		 */
		footerRevealInit: function() {

			// Return if disabled
			if ( ! this.config.$hasFooterReveal ) {
				return;
			}

			// Declare main vars
			var $showFooter         = false,
				$windowHeight       = $( window ).height(),
				$footerRevealHeight = $( '.footer-reveal' ).outerHeight();

			// If window height is greater then the wrap height display footer
			if ( $windowHeight > $( '#wrap' ).height() ) {
				$showFooter = true;
			}

			// If window height is smaller then footer reveal display footer
			if ( $windowHeight < $footerRevealHeight ) {
				$showFooter = true;
			}

			// Display footer reveal since we can't properly perform the reveal
			if ( $showFooter ) {
				$( '.footer-reveal' ).show().toggleClass( 'footer-reveal footer-reveal-visible' );
			}

			// Add margin to the wrap div for the footer reveal
			else {
				
				$( '#wrap' ).css( {
					'margin-bottom': $footerRevealHeight
				} );

			}

		},

		/**
		 * Footer Reveal Display on Scroll
		 *
		 * @since 2.0.0
		 */
		footerRevealScrollShow: function() {
			if ( this.config.$hasFooterReveal ) {
				if ( this.config.$windowTop > $( '#main' ).offset().top ) {
					if ( ! $( '.footer-reveal' ).hasClass( 'wpex-visible' ) ) {
						$( '.footer-reveal' ).show().addClass( 'wpex-visible' );
					}
				} else {
					if ( $( '.footer-reveal' ).hasClass( 'wpex-visible' ) ) {
						$( '.footer-reveal' ).removeClass( 'wpex-visible' ).hide();
					}
				}
			}
		},

		/**
		 * Set min height on main container to prevent issue with extra space below footer
		 *
		 * @since 3.1.1
		 */
		fixedFooter: function() {

			// Return if disabled
			if ( ! this.config.$hasFixedFooter ) {
				return;
			}

			// Get main wrapper
			var $main = $( '#main' );

			// Make sure main exists
			if ( $main.length ) {

				// Set main vars
				var $mainHeight = $( '#main' ).outerHeight(),
					$htmlHeight = $( 'html' ).height();

				// Check for footerReveal and add min height
				var $minHeight = $mainHeight + ( this.config.$window.height() - $htmlHeight );

				// Add min height
				$main.css( 'min-height', $minHeight );

			}
		},

		/**
		 * Custom Selects
		 *
		 * @since 2.0.0
		 */
		customSelects: function() {

			// Custom selects based on wpexLocalize array
			$( wpexLocalize.customSelects ).customSelect( {
				customClass: 'theme-select'
			} );

			// WooCommerce
			if ( $.fn.select2 !== undefined ) {
				$( '#calc_shipping_country' ).select2();
			}

		},

		/**
		 * FadeIn Elements
		 *
		 * @since 2.0.0
		 */
		fadeIn: function() {
			$( '.fade-in-image, .wpex-show-on-load' ).addClass( 'no-opacity' );
		},

		/**
		 * OwlCarousel
		 *
		 * @since 2.0.0
		 */
		owlCarousel: function() {

			var self = this;
			
			$( '.wpex-carousel' ).each( function() {

				var $this = $( this ),
					$data = $this.data();

				$this.owlCarousel( {
					animateIn          : false,
					animateOut         : false,
					lazyLoad           : false,
					smartSpeed         : self.parseData( $data.smartSpeed, wpexLocalize.carouselSpeed ),
					rtl                : self.config.$isRTL,
					dots               : $data.dots,
					nav                : $data.nav,
					items              : $data.items,
					slideBy            : $data.slideby,
					center             : $data.center,
					loop               : $data.loop,
					margin             : $data.margin,
					autoplay           : $data.autoplay,
					autoplayTimeout    : $data.autoplayTimeout,
					autoplayHoverPause : true,
					navText            : [ '<span class="fa fa-chevron-left"><span>', '<span class="fa fa-chevron-right"></span>' ],
					responsive         : {
						0: {
							items : $data.itemsMobilePortrait
						},
						480: {
							items : $data.itemsMobileLandscape
						},
						768: {
							items : $data.itemsTablet
						},
						960: {
							items : $data.items
						}
					},
					onInitialized : function() {
						/*if ( $this.hasClass( 'lightbox-group' ) ) {
							$this.find( '.cloned .wpex-lightbox-group-item' ).removeClass( 'wpex-lightbox-group-item' );
						}*/
					}
				} );

			} );

		},

		/**
		 * SliderPro
		 *
		 * @since 2.0.0
		 */
		sliderPro: function() {

			// Set main object to self
			var self = this;

			// Loop through each slider
			$( '.wpex-slider' ).each( function() {

				// Declare vars
				var $slider = $( this ),
					$data   = $slider.data();

				// Lets show things that were hidden to prevent flash
				$( '.wpex-slider-slide, .wpex-slider-thumbnails' ).css( {
					'opacity': 1,
					'display': 'block'
				} );

				// Get height based on first items to prevent animation on initial load
				var $preloader               = $( '.wpex-slider' ).prev( '.wpex-slider-preloaderimg' ),
					$height                  = $preloader.length ? $preloader.outerHeight() : null,
					$heightAnimationDuration = self.parseData( $data.heightAnimationDuration, 500 );

				// Run slider
				$slider.sliderPro( {
					responsive              : true,
					width                   : '100%',
					height                  : $height,
					fade                    : self.parseData( $data.fade, 600 ),
					touchSwipe              : self.parseData( $data.touchSwipe, true ),
					fadeDuration            : self.parseData( $data.animationSpeed, 600 ),
					slideAnimationDuration  : self.parseData( $data.animationSpeed, 600 ),
					autoHeight              : self.parseData( $data.autoHeight, true ),
					heightAnimationDuration : $heightAnimationDuration,
					arrows                  : self.parseData( $data.arrows, true ),
					fadeArrows              : self.parseData( $data.fadeArrows, true ),
					autoplay                : self.parseData( $data.autoPlay, true ),
					autoplayDelay           : self.parseData( $data.autoPlayDelay, 5000 ),
					buttons                 : self.parseData( $data.buttons, true ),
					shuffle                 : self.parseData( $data.shuffle, false ),
					orientation             : self.parseData( $data.direction, 'horizontal' ),
					loop                    : self.parseData( $data.loop, false ),
					keyboard                : false,
					fullScreen              : self.parseData( $data.fullscreen, false ),
					slideDistance           : self.parseData( $data.slideDistance, 0 ),
					thumbnailsPosition      : 'bottom',
					thumbnailHeight         : self.parseData( $data.thumbnailHeight, 70 ),
					thumbnailWidth          : self.parseData( $data.thumbnailWidth, 70 ),
					thumbnailPointer        : self.parseData( $data.thumbnailPointer, false ),
					updateHash              : self.parseData( $data.updateHash, false ),
					thumbnailArrows         : false,
					fadeThumbnailArrows     : false,
					thumbnailTouchSwipe     : true,
					fadeCaption             : self.parseData( $data.fadeCaption, true ),
					captionFadeDuration     : 500,
					waitForLayers           : true,
					autoScaleLayers         : true,
					forceSize               : 'none',
					reachVideoAction        : 'playVideo',
					leaveVideoAction        : 'pauseVideo',
					endVideoAction          : 'nextSlide',
					fadeOutPreviousSlide    : false, // prevents flash on fast transitions
					init                    : function( event ) {
						$slider.prev( '.wpex-slider-preloaderimg' ).hide();
						if ( $slider.parent( '.gallery-format-post-slider' ) && $( '.blog-masonry-grid' ).length ) {
							setTimeout( function() {
								$( '.blog-masonry-grid' ).isotope( 'layout' );
							}, $heightAnimationDuration + 1 );
						}
					},
					gotoSlideComplete       : function( event ) {
						if ( $slider.parent( '.gallery-format-post-slider' ) && $( '.blog-masonry-grid' ).length ) {
							$( '.blog-masonry-grid' ).isotope( 'layout' );
						}
					}

				} );

			} );

			// WooCommerce: Prevent clicking on Woo entry slider
			$( '.woo-product-entry-slider' ).click( function() {
				return false;
			} );

		   
		},

		/**
		 * Isotope Grids
		 *
		 * @since 2.0.0
		 */
		isotopeGrids: function() {

			var self = this;

			$( '.vcex-isotope-grid' ).each( function() {

				// Isotope layout
				var $container = $( this );

				// Run only once images have been loaded
				$container.imagesLoaded( function() {

					// Crete the isotope layout
					var $grid = $container.isotope( {
						itemSelector       : '.vcex-isotope-entry',
						transformsEnabled  : true,
						isOriginLeft       : self.config.$isRTL ? false : true,
						transitionDuration : $container.data( 'transition-duration' ) ? $container.data( 'transition-duration' ) + 's' : '0.4s',
						layoutMode         : $container.data( 'layout-mode' ) ? $container.data( 'layout-mode' ) : 'masonry',
						filter             : $container.data( 'filter' ) ? $container.data( 'filter' ) : ''
					} );

					// Filter links
					var $filter = $container.prev( 'ul.vcex-filter-links' );
					if ( $filter.length ) {
						var $filterLinks = $filter.find( 'a' );
						$filterLinks.click( function() {
							$grid.isotope( {
								filter: $( this ).attr( 'data-filter' )
							} );
							$( this ).parents( 'ul' ).find( 'li' ).removeClass( 'active' );
							$( this ).parent( 'li' ).addClass( 'active' );
							return false;
						} );
					}

					/* Run functions on trigger
					$grid.on( 'arrangeComplete', function() {
						// You can do cool things here
					} );*/

				} );

			} );

		},

		/**
		 * Isotope Grids
		 *
		 * @since 2.0.0
		 */
		archiveMasonryGrids: function() {

			// Define main vars
			var self      = this,
				$archives = $( '.blog-masonry-grid,div.wpex-row.portfolio-masonry,div.wpex-row.portfolio-no-margins,div.wpex-row.staff-masonry,div.wpex-row.staff-no-margins' );

			// Loop through archives
			$archives.each( function() {

				var $this               = $( this ),
					$data               = $this.data(),
					$transitionDuration = self.parseData( $data.transitionDuration, '0.0' ),
					$layoutMode         = self.parseData( $data.layoutMode, 'masonry' );

				// Load isotope after images loaded
				$this.imagesLoaded( function() {
					$this.isotope( {
						itemSelector       : '.isotope-entry',
						transformsEnabled  : true,
						isOriginLeft       : self.config.$isRTL ? false : true,
						transitionDuration : $transitionDuration + 's'
					} );
				} );

			} );

		},

		/**
		 * iLightbox
		 *
		 * @since 2.0.0
		 */
		iLightbox: function() {

			// Set main object to self
			var self = this;

			// Auto lightbox
			if ( wpexLocalize.iLightbox.auto ) {
				var $iLightboxAutoExtensions = ['bmp', 'gif', 'jpeg', 'jpg', 'png', 'tiff', 'tif', 'jfif', 'jpe'];
				$( '.wpb_text_column a:has(img), body.no-composer .entry a:has(img)' ).each( function() {
					var $this = $( this ),
						$url  = $this.attr( 'href' ),
						$ext  = self.getUrlExtension( $url );
					if ( $iLightboxAutoExtensions.indexOf( $ext ) !== -1 ) {
						$this.addClass( 'wpex-lightbox' );
					}
				} );
			}

			// Lightbox Standard
			$( '.wpex-lightbox' ).each( function() {

				var $this = $( this );

				if ( ! $this.hasClass( 'wpex-lightbox-group-item' ) ) {

					var $data = $this.data();

					$this.iLightBox( {
						skin     : self.parseData( $data.skin, wpexLocalize.iLightbox.skin ),
						controls : {
							fullscreen : wpexLocalize.iLightbox.controls.fullscreen
						},
						show     : {
							title : wpexLocalize.iLightbox.show.title,
							speed : parseInt( wpexLocalize.iLightbox.show.speed )
						},
						hide     : {
							speed : parseInt( wpexLocalize.iLightbox.hide.speed )
						},
						effects  : {
							reposition      : true,
							repositionSpeed : 200,
							switchSpeed     : 300,
							loadedFadeSpeed : wpexLocalize.iLightbox.effects.loadedFadeSpeed,
							fadeSpeed       : wpexLocalize.iLightbox.effects.fadeSpeed
						},
						overlay  : wpexLocalize.iLightbox.overlay,
						social   : wpexLocalize.iLightbox.social
					} );

				}

			} );

			// Lightbox Videos => OLD SCHOOL STUFF, keep for old customers
			$( '.wpex-lightbox-video, .wpb_single_image.video-lightbox a, .wpex-lightbox-autodetect, .wpex-lightbox-autodetect a' ).each( function() {

				var $this = $( this ),
					$data = $this.data();

				$this.iLightBox( {
					smartRecognition : true,
					skin             : self.parseData( $data.skin, wpexLocalize.iLightbox.skin ),
					path             : 'horizontal',
					controls         : {
						fullscreen : wpexLocalize.iLightbox.controls.fullscreen
					},
					show             : {
						title : wpexLocalize.iLightbox.show.title,
						speed : parseInt( wpexLocalize.iLightbox.show.speed )
					},
					hide             : {
						speed : parseInt( wpexLocalize.iLightbox.hide.speed )
					},
					effects          : {
						reposition      : true,
						repositionSpeed : 200,
						switchSpeed     : 300,
						loadedFadeSpeed : wpexLocalize.iLightbox.effects.loadedFadeSpeed,
						fadeSpeed       : wpexLocalize.iLightbox.effects.fadeSpeed
					},
					overlay : wpexLocalize.iLightbox.overlay,
					social  : wpexLocalize.iLightbox.social
				} );
			} );

			// Lightbox Galleries - NEW since 1.6.0
			$( '.lightbox-group' ).each( function() {

				// Get lightbox data
				var $this = $( this ),
					$item = $this.find( 'a.wpex-lightbox-group-item' ),
					$data = $this.data();

				// Start up lightbox
				$item.iLightBox( {
					skin     : self.parseData( $data.skin, wpexLocalize.iLightbox.skin ),
					path     : self.parseData( $data.path, wpexLocalize.iLightbox.path ),
					infinite : true,
					show     : {
						title : wpexLocalize.iLightbox.show.title,
						speed : parseInt( wpexLocalize.iLightbox.show.speed )
					},
					hide     : {
						speed: parseInt( wpexLocalize.iLightbox.hide.speed )
					},
					controls : {
						arrows     : self.parseData( $data.arrows, wpexLocalize.iLightbox.controls.arrows ),
						thumbnail  : self.parseData( $data.thumbnails, wpexLocalize.iLightbox.controls.thumbnail ),
						fullscreen : wpexLocalize.iLightbox.controls.fullscreen,
						mousewheel : wpexLocalize.iLightbox.controls.mousewheel
					},
					effects : {
						reposition      : true,
						repositionSpeed : 200,
						switchSpeed     : 300,
						loadedFadeSpeed : wpexLocalize.iLightbox.effects.loadedFadeSpeed,
						fadeSpeed       : wpexLocalize.iLightbox.effects.fadeSpeed
					},
					overlay : wpexLocalize.iLightbox.overlay,
					social  : wpexLocalize.iLightbox.social
				} );

			} );

			// Lightbox Gallery with custom imgs
			$( '.wpex-lightbox-gallery' ).on( 'click', function( event ) {
				// event.preventDefault(); // to fix customizer bug
				var imagesArray = $( this ).data( 'gallery' ).split( ',' );
				if ( imagesArray ) {
					$.iLightBox( imagesArray, {
						skin: wpexLocalize.iLightbox.skin,
						path: 'horizontal',
						infinite: true,
						show: {
							title: wpexLocalize.iLightbox.show.title,
							speed: parseInt( wpexLocalize.iLightbox.show.speed )
						},
						hide: {
							speed: parseInt( wpexLocalize.iLightbox.hide.speed )
						},
						controls: {
							arrows: wpexLocalize.iLightbox.controls.arrows,
							thumbnail: wpexLocalize.iLightbox.controls.thumbnail,
							fullscreen: wpexLocalize.iLightbox.controls.fullscreen,
							mousewheel: wpexLocalize.iLightbox.controls.mousewheel
						},
						effects: {
							reposition: true,
							repositionSpeed: 200,
							switchSpeed: 300,
							loadedFadeSpeed: wpexLocalize.iLightbox.effects.loadedFadeSpeed,
							fadeSpeed: wpexLocalize.iLightbox.effects.fadeSpeed
						},
						overlay: wpexLocalize.iLightbox.overlay,
						social : wpexLocalize.iLightbox.social
					} );
				}
				return false;
			} );

			// Carousel lightbox needs to be custom - zzz
			$( '.wpex-carousel-lightbox' ).each( function() {

				var $owlItems      = $( this ).find( '.owl-item' ),
					$lightboxItems = $( this ).find( '.wpex-carousel-lightbox-item' ),
					$imagesArray   = new Array();

				$owlItems.each( function() {
					if ( ! $( this ).hasClass( 'cloned' ) ) {
						var $image = $( this ).find( '.wpex-carousel-lightbox-item' );
						if ( $image.length > 0 ) {
							$imagesArray.push( {
								URL   : $image.attr( 'href' ),
								title : $image.attr( 'title' )
							} );
						}
					}
				} );

				if ( $imagesArray.length > 0 ) {

					$lightboxItems.on( 'click', function( event ) {

						event.preventDefault();

						var $startFrom = $( this ).data( 'count' ) - 1,
							$startFrom = $startFrom ? $startFrom : 0;

						$.iLightBox( $imagesArray, {
							startFrom : parseInt( $startFrom ),
							path      : 'horizontal',
							infinite  : true,
							skin      : wpexLocalize.iLightbox.skin,
							show      : {
								title : wpexLocalize.iLightbox.show.title,
								speed : parseInt( wpexLocalize.iLightbox.show.speed )
							},
							hide      : {
								speed : parseInt( wpexLocalize.iLightbox.hide.speed )
							},
							controls  : {
								arrows    : wpexLocalize.iLightbox.controls.arrows,
								thumbnail  : wpexLocalize.iLightbox.controls.thumbnail,
								fullscreen : wpexLocalize.iLightbox.controls.fullscreen,
								mousewheel : wpexLocalize.iLightbox.controls.mousewheel
							},
							effects   : {
								reposition      : true,
								repositionSpeed : 200,
								switchSpeed     : 300,
								loadedFadeSpeed : wpexLocalize.iLightbox.effects.loadedFadeSpeed,
								fadeSpeed       : wpexLocalize.iLightbox.effects.fadeSpeed
							},
							overlay   : wpexLocalize.iLightbox.overlay,
							social    : wpexLocalize.iLightbox.social
						} );

					} );

				}

			} );

		},

		/**
		 * Overlay Hovers
		 *
		 * @since 2.0.0
		 */
		overlayHovers: function() {

			$( '.overlay-parent-title-push-up' ).each( function() {

				// Define vars
				var $this        = $( this ),
					$title       = $this.find( '.overlay-title-push-up' ),
					$child       = $this.find( 'a' ),
					$img         = $child.find( 'img' ),
					$titleHeight = $title.outerHeight();

				// Create overlay after image is loaded to prevent issues
				$this.imagesLoaded( function() {

					// Position title
					$title.css( {
						'bottom' : - $titleHeight
					} );

					// Add height to child
					$child.css( {
						'height' : $img.outerHeight()
					} );

					// Position image
					$img.css( {
						'position' : 'absolute',
						'top'      : '0',
						'left'     : '0',
						'width'    : '100%',
						'height'   : '100%'
					} );

					// Animate image on hover
					$this.hover( function() {
						$img.css( {
							'top' : -20
						} );
						$title.css( {
							'bottom' : 0
						} );
					}, function() {
						$img.css( {
							'top' : '0'
						} );
						$title.css( {
							'bottom' : - $titleHeight
						} );
					} );

				} );

			} );

		},

		/**
		 * Sticky Topbar
		 *
		 * @since 3.4.0
		 */
		newStickyTopbar: function() {
			var $isSticky     = false;
			var self          = this;
			var $window       = self.config.$window;
			var $stickyTopbar = self.config.$stickyTopBar;
			var $mobileMenu   = $( '#wpex-mobile-menu-fixed-top' );

			// Return if disabled
			if ( ! $stickyTopbar ) return;

			// Main vars
			var $stickyTopbarHeight = $stickyTopbar.outerHeight(),
				$mobileSupport      = self.config.$hasStickyTopBarMobile,
				$brkPoint           = wpexLocalize.stickyTopBarBreakPoint;

			// Add sticky wrapper
			var $stickyWrap = $( '<div id="top-bar-wrap-sticky-wrapper" class="wpex-sticky-top-bar-holder not-sticky"></div>' );
			$stickyTopbar.wrapAll( $stickyWrap );
			var $stickyWrap = $( '#top-bar-wrap-sticky-wrapper' );

			// Get offset
			function getOffset() {
				var $offset = 0;
				if ( self.config.$wpAdminBar ) {
					$offset = $offset + self.config.$wpAdminBar.outerHeight();
				}
				if ( $mobileMenu.is( ':visible' ) ) {
					$offset = $offset + $mobileMenu.outerHeight();
				}
				return $offset;
			}
			var $initOffset = $stickyWrap.offset().top - getOffset();

			// Stick the TopBar
			function setSticky() {

				// Already stuck
				if ( $isSticky ) return;
				
				// Get correct width
				var $stickyWrapWidth = $stickyWrap.width();

				// Add wrap class and toggle sticky class
				$stickyWrap
					.css( 'height', $stickyTopbarHeight )
					.removeClass( 'not-sticky' )
					.addClass( 'is-sticky' );

				// Add CSS to topbar
				$stickyTopbar.css( {
					'top'   : getOffset(),
					'width' : $stickyWrapWidth
				} );

				// Set sticky to true
				$isSticky = true;

			}

			// Unstick the TopBar
			function destroySticky() {

				if ( ! $isSticky ) return;

				// Remove sticky wrap height and toggle sticky class
				$stickyWrap
					.css( 'height', '' )
					.removeClass( 'is-sticky' )
					.addClass( 'not-sticky' );

				// Remove topbar css
				$stickyTopbar.css( {
					'width' : '',
					'top'   : '',
				} );

				// Set sticky to false
				$isSticky = false;

			}

			// On scroll actions for sticky topbar
			function stickyCheck() {

				// Disable on mobile devices
				if ( ! $mobileSupport && ( self.config.$viewportWidth < $brkPoint ) ) {
					return;
				}

				// Set or destroy sticky
				if ( self.config.$windowTop > $initOffset && 0 !== self.config.$windowTop ) {
				 	setSticky();
				} else {
					destroySticky();
				}
			}

			// On resize actions for sticky topbar
			function onResize() {

				// Check if header is disabled on mobile if not destroy on resize
				if ( ! $mobileSupport && ( self.config.$viewportWidth < $brkPoint ) ) {
					destroySticky();
				} else {

					// Set correct width and top value
					if ( $isSticky ) {
						$stickyTopbar.css( {
							'top'   : getOffset(),
							'width' : $stickyWrap.width()
						} );
					} else {
						stickyCheck();
					}

				}

			}

			// Fire on init
			stickyCheck();

			// Fire onscroll event
			$window.scroll( function() {
				stickyCheck();
			} );

			// Fire onResize
			$window.resize( function() {
				onResize();
			} );

		},

		/**
		 * Get correct sticky header offset / Used for header and menu so keep outside
		 *
		 * @since 3.4.0
		 */
		stickyOffset: function() {
			var self          = this;
			var $offset       = 0;
			var $mobileMenu   = $( '#wpex-mobile-menu-fixed-top' );
			var $stickyTopbar = self.config.$stickyTopBar;

			// Offset sticky topbar
			if ( $stickyTopbar ) {
				if ( self.config.$hasStickyTopBarMobile
					|| self.config.$viewportWidth >= wpexLocalize.stickyTopBarBreakPoint
				) {
					$offset = $offset + $stickyTopbar.outerHeight();
				}
			}

			// Offset mobile menu
			if ( $mobileMenu.is( ':visible' ) ) {
				$offset = $offset + $mobileMenu.outerHeight();
			}

			// Offset adminbar
			if ( this.config.$wpAdminBar ) {
				$offset = $offset + this.config.$wpAdminBar.outerHeight();
			}

			// Added offset via child theme
			if ( wpexLocalize.addStickyHeaderOffset ) {
				$offset = $offset + wpexLocalize.addStickyHeaderOffset;
			}

			// Return correct offset
			return $offset;

		},

		/**
		 * New Sticky Header
		 *
		 * @since 3.4.0
		 */
		stickyHeader : function() {
			var $isSticky = false;
			var self      = this;

			// Return if sticky is disabled
			if ( ! self.config.$hasStickyHeader ) return;

			// Define header vars
			var $header      = self.config.$siteHeader;
			var $headerStyle = self.config.$siteHeaderStyle;

			// Add sticky wrap
			var $stickyWrap = $( '<div id="site-header-sticky-wrapper" class="wpex-sticky-header-holder not-sticky"></div>' );
			$header.wrapAll( $stickyWrap );

			// Define main vars for sticky function
			var $window               = self.config.$window;
			var $brkPoint             = wpexLocalize.stickyHeaderBreakPoint;
			var $stickyWrap           = $( '#site-header-sticky-wrapper' );
			var $headerHeight         = self.config.$siteHeaderHeight;
			var $hasShrinkFixedHeader = $header.hasClass( 'shrink-sticky-header' );
			var $mobileSupport        = self.config.$hasStickyMobileHeader;

			// Custom shrink logo
			var $stickyLogo    = wpexLocalize.stickyheaderCustomLogo;
			var $headerLogo    = self.config.$siteLogo;
			var $headerLogoSrc = self.config.$siteLogoSrc;

			// Custom shrink logo retina
			if ( $stickyLogo
				&& wpexLocalize.stickyheaderCustomLogoRetina
				&& self.config.$isRetina
			) {
				$stickyLogo = wpexLocalize.stickyheaderCustomLogoRetina;
			}

			// Add offsets
			var $stickyWrapTop = $stickyWrap.offset().top;
			var $stickyOffset  = self.stickyOffset();
			var $setStickyPos  = $stickyWrapTop - $stickyOffset;

			// Set sticky
			function setSticky() {

				// Already stuck
				if ( $isSticky ) return;

				// Custom Sticky logo
				if ( $stickyLogo && $headerLogo ) {
					$headerLogo.attr( 'src', $stickyLogo );
					self.config.$siteLogoHeight = self.config.$siteLogo.height();
				}

				// Add wrap class and toggle sticky class
				$stickyWrap
					.css( 'height', $headerHeight )
					.removeClass( 'not-sticky' )
					.addClass( 'is-sticky' );

				// Tweak header
				$header.removeClass( 'dyn-styles').css( {
					'top'   : self.stickyOffset(),
					'width' : $stickyWrap.width()
				} );

				// Set sticky to true
				$isSticky = true;

			}

			// Destroy sticky
			function destroySticky() {

				// Already unstuck
				if ( ! $isSticky ) return;

				// Reset logo
				if ( $stickyLogo && $headerLogo ) {
					$headerLogo.attr( 'src', $headerLogoSrc );
					self.config.$siteLogoHeight = self.config.$siteLogo.height();
				}

				// Remove sticky wrap height and toggle sticky class
				$stickyWrap.removeClass( 'is-sticky' ).addClass( 'not-sticky' );

				// Do not remove height on sticky header for shrink header incase animation isn't done yet
				if ( ! $header.hasClass( 'shrink-sticky-header' ) ) {
					$stickyWrap.css( 'height', '' );
				}

				// Reset header
				$header.addClass( 'dyn-styles').css( {
					'width' : '',
					'top'   : ''
				} );

				// Set sticky to false
				$isSticky = false;

			}

			// On scroll function
			function stickyCheck() {

				// Disable on mobile devices
				if ( ! $mobileSupport && ( self.config.$viewportWidth < $brkPoint ) ) {
					return;
				}

				// Add and remove sticky classes and sticky logo
				if ( self.config.$windowTop >= $setStickyPos && 0 !== self.config.$windowTop ) {
				 	setSticky();
				} else {
					destroySticky();
				}

			}

			// On resize function
			function onResize() {

				// Check if header is disabled on mobile if not destroy on resize
				if ( ! $mobileSupport && ( self.config.$viewportWidth < $brkPoint ) ) {
					destroySticky();
				} else {

					// Update sticky
					if ( $isSticky ) {

						// Update Height
						if ( ! $header.hasClass( 'shrink-sticky-header' ) ) {
							$stickyWrap.css( 'height', self.config.$siteHeaderHeight );
						}

						// Update width and top
						$header.css( {
							'top'   : self.stickyOffset(),
							'width' : $stickyWrap.width()
						} );

					}

					// Add sticky
					else {
						stickyCheck();
					}

				}

			} // End onResize

			// Fire on init
			stickyCheck();

			// Fire onscroll event
			$window.scroll( function() {
				stickyCheck();
			} );

			// Fire onResize
			$window.resize( function() {
				onResize();
			} );

		},

		/**
		 * New Shrink Sticky Header
		 *
		 * @since 3.4.0
		 */
		shrinkStickyHeader: function() {

			var $isShrunk = false;

			// Define header element
			var self     = this,
				$header  = self.config.$siteHeader,
				$enabled = $header.hasClass( 'shrink-sticky-header' );

			// Return if shrink header disabled
			if ( ! $enabled ) return;

			// Define window and sticky wrap
			var $window     = self.config.$window,
				$brkPoint   = wpexLocalize.stickyHeaderBreakPoint,
				$stickyWrap = $( '#site-header-sticky-wrapper' );
			if ( ! $stickyWrap.length ) return;

			// Check if enabled on mobile
			var $mobileSupport = self.config.$hasStickyMobileHeader;

			// Get correct header offet
			var $headerHeight       = self.config.$siteHeaderHeight,
				$stickyWrapTop      = $stickyWrap.offset().top,
				$shrinkHeaderOffset = $stickyWrapTop + $headerHeight;

			// Mobile checks
			var $mtStyle = self.config.$mobileMenuToggleStyle;
			if ( $mobileSupport && ( 'icon_buttons' == $mtStyle || 'fixed_top' == $mtStyle ) ) {
				var $hasShrinkHeaderOnMobile = true;
			} else {
				var $hasShrinkHeaderOnMobile = false;
			}

			// Shrink header function
			function shrinkHeader() {

				// Already shrunk or not sticky
				if ( $isShrunk || ! $stickyWrap.hasClass( 'is-sticky' ) ) return;

				// Add shrunk class
				$header.addClass( 'sticky-header-shrunk' );

				// Update shrunk var
				$isShrunk = true;

			}

			// Un-Shrink header function
			function unShrinkHeader() {

				// Not shrunk
				if ( ! $isShrunk ) return;

				// Remove shrunk class
				$header.removeClass( 'sticky-header-shrunk' );

				// Update shrunk var
				$isShrunk = false;

			}

			// On scroll function
			function shrinkCheck() {

				// Disable on mobile devices
				if ( ! $hasShrinkHeaderOnMobile && ( self.config.$viewportWidth < $brkPoint ) ) {
					return;
				}

				// Shrink sticky header
				if ( self.config.$windowTop >= $shrinkHeaderOffset ) {
					shrinkHeader();
				} else {
					unShrinkHeader();
				}

			}

			// On resize function
			function onResize() {

				// Check if header is disabled on mobile if not destroy
				if ( ! $hasShrinkHeaderOnMobile && ( self.config.$viewportWidth < $brkPoint ) ) {
					unShrinkHeader();
				} else {
					shrinkCheck();
				}

			}

			// Fire on init
			shrinkCheck();

			// Fire onscroll event
			$window.scroll( function() {
				shrinkCheck();
			} );

			// Fire onResize
			$window.resize( function() {
				onResize();
			} );

		},

		/**
		 * Sticky Header Menu
		 *
		 * @since 3.4.0
		 */
		stickyHeaderMenu: function() {
			var self           = this;
			var $navWrap       = self.config.$siteNavWrap;
			var $isSticky      = false;
			var $window        = self.config.$window;
			var $mobileSupport = wpexLocalize.hasStickyNavbarMobile;

			// Add sticky wrap
			var $stickyWrap = $( '<div id="site-navigation-sticky-wrapper" class="wpex-sticky-navigation-holder not-sticky"></div>' );
			$navWrap.wrapAll( $stickyWrap );
			$stickyWrap = $( '#site-navigation-sticky-wrapper' );

			// Add offsets
			var $stickyWrapTop = $stickyWrap.offset().top;
			var $stickyOffset  = self.stickyOffset();
			var $setStickyPos  = $stickyWrapTop - $stickyOffset;

			// Shrink header function
			function setSticky() {

				// Already sticky
				if ( $isSticky ) return;

				// Add wrap class and toggle sticky class
				$stickyWrap
					.css( 'height', self.config.$siteNavWrap.outerHeight() )
					.removeClass( 'not-sticky' )
					.addClass( 'is-sticky' );

				// Add CSS to topbar
				$navWrap.css( {
					'top'   : self.stickyOffset(),
					'width' : $stickyWrap.width()
				} );
				
				// Update shrunk var
				$isSticky = true;

			}

			// Un-Shrink header function
			function destroySticky() {

				// Not shrunk
				if ( ! $isSticky ) return;

				// Remove sticky wrap height and toggle sticky class
				$stickyWrap
					.css( 'height', '' )
					.removeClass( 'is-sticky' )
					.addClass( 'not-sticky' );

				// Remove navbar width
				$navWrap.css( {
					'width' : '',
					'top'   : ''
				} );

				// Update shrunk var
				$isSticky = false;

			}

			// Sticky check / enable-disable
			function stickyCheck() {

				// Disable on mobile devices
				if ( self.config.$viewportWidth <= wpexLocalize.stickyNavbarBreakPoint ) {
					return;
				}

				// Sticky menu
				if ( self.config.$windowTop >= $setStickyPos && 0 !== self.config.$windowTop ) {
					setSticky();
				} else {
					destroySticky();
				}

			}

			// On resize function
			function onResize() {

				// Check if sticky is disabled on mobile if not destroy on resize
				if ( self.config.$viewportWidth <= wpexLocalize.stickyNavbarBreakPoint ) {
					destroySticky();
				}

				// Update width
				if ( $isSticky ) {
					$navWrap.css( 'width', $stickyWrap.width() );
				} else {
					stickyCheck();
				}

			}

			// Fire on init
			stickyCheck();

			// Fire onscroll event
			$window.scroll( function() {
				stickyCheck();
			} );

			// Fire onResize
			$window.resize( function() {
				onResize();
			} );

		},

		/**
		 * VCEX Navbar
		 *
		 * @since 3.3.2
		 */
		stickyVcexNavbar: function() {
			var self = this;
			var $nav = $( '.vcex-navbar-sticky' );
			if ( ! $nav.length ) return;

			var $navHeight = $nav.outerHeight();
			var $isSticky  = false;
			var $window    = self.config.$window;

			// Add sticky wrap
			var $stickyWrap = $( '<div class="vcex-navbar-sticky-wrapper not-sticky"></div>' );
			$nav.wrapAll( $stickyWrap );
			$stickyWrap = $( '.vcex-navbar-sticky-wrapper' );

			// Offset
			var $stickyWrapTop = $stickyWrap.offset().top;

			// Shrink header function
			function setSticky() {

				// Already sticky
				if ( $isSticky ) return;

				// Add wrap class and toggle sticky class
				$stickyWrap
					.css( 'height', $navHeight )
					.removeClass( 'not-sticky' )
					.addClass( 'is-sticky' );

				// Add CSS to topbar
				$nav.css( {
					'top'   : '0',
					'width' : $stickyWrap.width()
				} );
				
				// Update shrunk var
				$isSticky = true;

			}

			// Un-Shrink header function
			function destroySticky() {

				// Not shrunk
				if ( ! $isSticky ) return;

				// Remove sticky wrap height and toggle sticky class
				$stickyWrap
					.css( 'height', '' )
					.removeClass( 'is-sticky' )
					.addClass( 'not-sticky' );

				// Remove navbar width
				$nav.css( {
					'width' : '',
					'top'   : ''
				} );

				// Update shrunk var
				$isSticky = false;

			}

			// On scroll function
			function stickyCheck() {
				if ( self.config.$windowTop > $stickyWrapTop && 0 !== self.config.$windowTop ) {
					setSticky();
				} else {
					destroySticky();
				}
			}

			// On resize function
			function onResize() {
				if ( $isSticky ) {
					$nav.css( 'width', $stickyWrap.width() );
				}
			}

			// Fire on init
			stickyCheck();

			// Fire onscroll event
			$window.scroll( function() {
				stickyCheck();
			} );

			// Fire onResize
			$window.resize( function() {
				onResize();
			} );

		},

		/**
		 * Visual Composer tweaks
		 *
		 * @since 3.3.5
		 */
		visualComposer: function() {

			var self = this;

			// On window Load
			self.config.$window.on( 'load', function() {

				// Re-trigger/update things when opening accordions.
				$( '.vc_tta-tabs' ).on( 'afterShow.vc.accordion', function( e ) {
					// Sliders
					$( this ).find( '.wpex-slider' ).each( function() {
						$( this ).sliderPro( 'update' );
					} );
					// Grids
					$( this ).find( '.vcex-isotope-grid' ).each( function() {
						$( this ).isotope( 'layout' );
					} );
				} );

				// Re-trigger slider on tabs change
				$( '.vc_tta-accordion' ).on( 'show.vc.accordion', function() {
					// Sliders
					$( this ).find( '.wpex-slider' ).each( function() {
						$( this ).sliderPro( 'update' );
					} );
					// Grids
					$( this ).find( '.vcex-isotope-grid' ).each( function() {
						$( this ).isotope( 'layout' );
					} );
				} );

			} );

		},

		/**
		 * Parses data to check if a value is defined in the data attribute and if not returns the fallback
		 *
		 * @since 2.0.0
		 */
		parseData: function( val, fallback ) {
			return ( typeof val !== 'undefined' ) ? val : fallback;
		},

		/**
		 * Returns extension from URL
		 */
		getUrlExtension: function( url ) {
			var ext = url.split( '.' ).pop().toLowerCase(),
				extra = ext.indexOf( '?' ) !== -1 ? ext.split( '?' ).pop() : '';
			return ext.replace( extra, '' );
		}


	}; // END wpexTheme

	// Start things up
	wpexTheme.init();

} ) ( jQuery );