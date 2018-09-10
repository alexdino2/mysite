<?php
/**
 * Visual Composer configuration file
 *
 * @package Total WordPress Theme
 * @subpackage VC Functions
 * @version 3.3.5
 */

global $wpex_visual_composer_config;

class WPEX_Visual_Composer {
	private $remove_design_options;

	/**
	 * Start things up
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// Definitions
		define( 'WPEX_VCEX_DIR', WPEX_FRAMEWORK_DIR .'visual-composer/' );
		define( 'WPEX_VCEX_DIR_URI', WPEX_FRAMEWORK_DIR_URI .'visual-composer/' );

		// Include helper functions and classes
		require_once( WPEX_FRAMEWORK_DIR .'visual-composer/vc-helpers.php' );

		// Check if design options are enabled
		$this->remove_design_options = apply_filters( 'wpex_remove_vc_design_options', true );

		// Delete design options
		if ( $this->remove_design_options ) {
			delete_option( 'wpb_js_use_custom' );
		}

		// Admin only functions
		if ( is_admin() ) {

			// Remove design options tab
			if ( $this->remove_design_options ) {
				add_filter( 'vc_settings_page_show_design_tabs', '__return_false' );
			}

			// Theme mode
			$theme_mode = wpex_get_mod( 'visual_composer_theme_mode', true );

			// Disable theme mode if the VC is activated
			if ( $theme_mode && vc_license()->isActivated() ) {
				set_theme_mod( 'visual_composer_theme_mode', false );
				$theme_mode = false;
			}
			

			// Disable updater and add extra notices to VC license tab
			if ( $theme_mode ) {

				// Set in theme mode & disable updater
				if ( function_exists( 'vc_set_as_theme' ) ) {
					$disable_updater = true;
					vc_set_as_theme( $disable_updater );
				}

				// Add admin notice on product license tab
				add_action( 'admin_notices', array( $this, 'vc_license_tab_notice' ) );

				// Is this still needed?
				add_action( 'admin_init', array( $this, 'disable_updater' ), 99 );


			}

		}

		// Run on init
		add_action( 'init', array( $this, 'init' ), 20 );

		// Add grid-builder modules => must load early on
		require_once( WPEX_VCEX_DIR .'shortcodes/post_video.php' );
		require_once( WPEX_VCEX_DIR .'shortcodes/post_meta.php' );

		// Tweak scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_composer_front_css' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_remove_styles' ) );
		add_action( 'wp_footer', array( $this, 'remove_footer_scripts' ) );
		add_action( 'admin_enqueue_scripts',  array( $this, 'admin_scripts' ) );

		// Load Visual Composer meta CSS for footer builder, topbar, etc.
		add_action( 'wpex_head_css', array( $this,'vc_css_ids' ) );

		// Alter the allowed font tags and fonts
		add_filter( 'vc_font_container_get_allowed_tags', array( $this, 'font_container_tags' ) );
		add_filter( 'vc_font_container_get_fonts_filter', array( $this, 'font_container_fonts' ) );

		// Alter default templates
		add_filter( 'vc_load_default_templates', array( $this, 'default_templates' ) );

		// Remove VC welcome screen
		add_action( 'admin_menu', array( __class__, 'remove_welcome' ), 999 );
		remove_action( 'vc_activation_hook', 'vc_page_welcome_set_redirect' );
		remove_action( 'init', 'vc_page_welcome_redirect' );
		remove_action( 'admin_init', 'vc_page_welcome_redirect' );

		// Register accent colors
		add_filter( 'wpex_accent_texts', array( $this, 'accent_texts' ) );
		add_filter( 'wpex_accent_borders', array( $this, 'accent_borders' ) );
		add_filter( 'wpex_accent_backgrounds', array( $this, 'accent_backgrounds' ) );

		// Add new parameter types
		if ( function_exists( 'vc_add_shortcode_param' ) ) {
			vc_add_shortcode_param( 'vcex_font_family_select', array( $this, 'vcex_font_family_select' ) );
		}

	}

	/**
	 * Functions that run on init
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Remove purchase notice
		wpex_remove_class_filter( 'admin_notices', 'Vc_License', 'adminNoticeLicenseActivation', 10 );

		// Override editor logo
		add_filter( 'vc_nav_front_logo', array( $this, 'nav_logo' ) );

		// Remove templatera notice
		remove_action( 'admin_notices', 'templatera_notice' );

		// Set defaults for admin
		if ( function_exists( 'vc_set_default_editor_post_types' ) ) {
			vc_set_default_editor_post_types( array( 'page', 'portfolio', 'staff' ) );
		}

		// Set defaults for editor
		if ( function_exists( 'vc_editor_set_post_types ') ) {
			$types = vc_settings()->get( 'content_types' );
			if ( empty( $types  ) ) {
				vc_editor_set_post_types( array( 'page', 'portfolio', 'staff' ) );
			}
		}

		// Array of elements to remove
		$elements = array(
			'vc_teaser_grid',
			'vc_posts_grid',
			'vc_posts_slider',
			'vc_carousel',
			'vc_gallery',
			'vc_wp_text',
			'vc_wp_pages',
			'vc_wp_links',
			'vc_wp_categories',
			'vc_wp_meta',
			'vc_images_carousel',
		);

		// Add filter for child theme tweaking
		$elements = apply_filters( 'wpex_vc_remove_elements', $elements );

		// Loop through elements to remove and remove them
		if ( is_array( $elements ) ) {
			foreach ( $elements as $element ) {
				vc_remove_element( $element );
			}
		}

		// Add custom params
		if ( function_exists( 'vc_add_param' ) ) {
			
			// Add param to tabs
			vc_add_param( 'vc_tabs', array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Style', 'total' ),
				'param_name' => 'style',
				'value' => array(
					esc_html__( 'Default', 'total' ) => 'default',
					esc_html__( 'Alternative #1', 'total' ) => 'alternative-one',
					esc_html__( 'Alternative #2', 'total' ) => 'alternative-two',
				),  
			) );

			// Add param Tours
			vc_add_param( 'vc_tour', array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Style', 'total' ),
				'param_name' => 'style',
				'value' => array(
					esc_html__( 'Default', 'total' ) => 'default',
					esc_html__( 'Alternative #1', 'total' ) => 'alternative-one',
					esc_html__( 'Alternative #2', 'total' ) => 'alternative-two',
				),
				
			) );

		}

		// Include custom modules
		if ( function_exists( 'vc_lean_map' )
			&& class_exists( 'WPBakeryShortCode' )
			&& wpex_get_mod( 'extend_visual_composer', true )
		) {
			$this->total_custom_vc_shortcodes();
		}

	}

	/**
	 * Add admin notice on product license tab
	 *
	 * @since 3.3.3
	 */
	public function vc_license_tab_notice() {
		$screen = get_current_screen();
		if ( 'visual-composer_page_vc-updater' == $screen->id ) {
			echo '<div class="error"><p><strong>'. esc_html__( 'Activating the Visual Composer plugin is 100% optional and NOT required to function correctly with the theme.', 'wpex' ) .'</strong></p></div>';
		}
	}

	/**
	 * Disables the VC updater function
	 *
	 * @since 3.0.0
	 */
	public function disable_updater() {

		// Define filter names
		if ( version_compare( WPB_VC_VERSION, '4.4', '<=' ) ) {
			$pre_upgrade_filter = 'upgradeFilterFromEnvato';
		} else {
			$pre_upgrade_filter = 'preUpgradeFilter';
		}

		// Get Globals
		global $GLOBALS;

		/**
		 * Remove update pre_download filter
		 *
		 * @see Vc_Updater
		 *
		 */
		if ( ! empty ( $GLOBALS['wp_filter']['upgrader_pre_download'] ) ) {
			$filters = $GLOBALS['wp_filter']['upgrader_pre_download'];
			if ( ! empty( $filters ) && is_array( $filters ) ) {
				foreach ( $filters as $priority => $filter ) {
					foreach ( $filter as $identifier => $function ) {
						if ( is_array( $function )
							and is_a( $function['function'][0], 'Vc_Updater' )
							and $pre_upgrade_filter === $function['function'][1]
						) {
							remove_filter( 'upgrader_pre_download', array ( $function['function'][0], $pre_upgrade_filter ), $priority );
						}
					}
				}
			}
		}

		/**
		 * Remove Updater message in plugins list
		 *
		 * @see Vc_Updating_Manager
		 *
		 */
		if ( function_exists( 'vc_plugin_name' ) ) {
			$tag = 'in_plugin_update_message-' . vc_plugin_name();
			if ( ! empty ( $GLOBALS['wp_filter'][$tag] ) ) {
				$filters = $GLOBALS['wp_filter'][$tag];
				if ( ! empty ( $filters ) && is_array( $filters ) ) {
					foreach ( $filters as $priority => $filter ) {
						foreach ( $filter as $identifier => $function ) {
							if ( is_array( $function )
								and is_a( $function['function'][0], 'Vc_Updating_Manager' )
								and 'check_update' === $function['function'][1]
							) {
								remove_filter( $tag, array ( $function['function'][0], 'check_update' ), $priority );
							}
						}
					}
				}
			}
		}

	}

	/**
	 * Override editor logo
	 *
	 * @since 3.0.0
	 */
	public static function nav_logo() {
		return '<div id="vc_logo" class="vc_navbar-brand">'. esc_html__( 'Visual Composer', 'total' ) .'</div>';
	}

	/**
	 * Removes "About" page in the Visual Composer
	 *
	 * @since  2.1.0
	 */
	public static function remove_welcome() {
		remove_submenu_page( 'vc-general', 'vc-welcome' );
	}

	/**
	 * Load js_composer_front CSS eaerly on for easier modification
	 *
	 * @since  2.1.3
	 */
	public static function load_composer_front_css() {
		wp_enqueue_style( 'js_composer_front' );
	}

	/**
	 * Load and remove stylesheets
	 *
	 * @since 2.0.0
	 */
	public function load_remove_styles() {

		// Add Scripts
		wp_enqueue_style(
			'wpex-visual-composer',
			WPEX_CSS_DIR_URI .'wpex-visual-composer.css',
			array(
				'wpex-style',
				'js_composer_front'
			),
			WPEX_THEME_VERSION
		);

		wp_enqueue_style(
			'wpex-visual-composer-extend',
			WPEX_CSS_DIR_URI .'wpex-visual-composer-extend.css',
			array(
				'wpex-style',
				'js_composer_front'
			),
			WPEX_THEME_VERSION
		);

		/* Remove Scripts to fix Customizer issue with jQuery UI
		 * Fixed in WP 4.4
		 * @deprecated 3.3.0
		if ( is_customize_preview() ) {
			wp_deregister_script( 'wpb_composer_front_js' );
			wp_dequeue_script( 'wpb_composer_front_js' );
		}*/

		// Remove unwanted scripts
		if ( apply_filters( 'wpex_remove_vc_design_options', true ) ) {
			wp_deregister_style( 'js_composer_custom_css' );
		}
		wp_deregister_style( 'font-awesome' );
		wp_dequeue_style( 'font-awesome' );

	}

	/**
	 * Remove scripts hooked in too late for me to remove on wp_enqueue_scripts
	 *
	 * @since  2.1.0
	 */
	public static function remove_footer_scripts() {

		// JS
		wp_dequeue_script( 'vc_pageable_owl-carousel' );
		wp_dequeue_script( 'vc_grid-js-imagesloaded' );

		// Styles conflict with Total owl carousel styles
		wp_deregister_style( 'vc_pageable_owl-carousel-css-theme' );
		wp_dequeue_style( 'vc_pageable_owl-carousel-css-theme' );
		wp_deregister_style( 'vc_pageable_owl-carousel-css' );
		wp_dequeue_style( 'vc_pageable_owl-carousel-css' );

	}

	/**
	 * Admin Scripts
	 *
	 * @since 1.6.0
	 */
	public static function admin_scripts() {
		wp_enqueue_style( 'vcex-admin-css', WPEX_VCEX_DIR_URI .'assets/wpex-vc-admin.css' );
	}

	/**
	 * Adds tags to the font_container param
	 *
	 * @since 2.1.0
	 */
	public static function font_container_tags( $tags ) {
		$tags['span'] = 'span';
		return $tags;
	}

	/**
	 * Adds fonts to the font_container param
	 *
	 * @since  2.1.0
	 */
	public static function font_container_fonts( $fonts ) {

		// Add blank option
		$new_fonts[''] = esc_html__( 'Default', 'total' );

		// Merge arrays
		$fonts = array_merge( $new_fonts, $fonts );

		// Get Google fonts
		$google_fonts = wpex_google_fonts_array();
		$google_fonts = array_combine( $google_fonts, $google_fonts );

		// Merge fonts
		$fonts = array_merge( $fonts, $google_fonts );

		// Return fonts
		return $fonts;

	}

	/**
	 * Adds border accents for WooCommerce styles
	 *
	 * @since  2.1.0
	 */
	public static function accent_texts( $texts ) {
		return array_merge( array(
			'.wpex-carousel-woocommerce .wpex-carousel-entry-details',
		), $texts );
	}

	/**
	 * Adds border accents for WooCommerce styles
	 *
	 * @since  2.1.0
	 */
	public static function accent_borders( $borders ) {
		return array_merge( array(
			'.vcex-heading-bottom-border-w-color' => array( 'bottom' ),
			'.wpb_tabs.tab-style-alternative-two .wpb_tabs_nav li.ui-tabs-active a' => array( 'bottom' ),
		), $borders );
	}

	/**
	 * Adds border accents for WooCommerce styles
	 *
	 * @since  2.1.0
	 */
	public static function accent_backgrounds( $backgrounds ) {
		return array_merge( array(
			'.vcex-skillbar-bar',
			'.vcex-icon-box.style-five.link-wrap:hover',
			'.vcex-icon-box.style-four.link-wrap:hover',
			'.vcex-recent-news-date span.month',
			'.vcex-pricing.featured .vcex-pricing-header',
			'.vcex-testimonials-fullslider .sp-button:hover',
			'.vcex-testimonials-fullslider .sp-selected-button',
			'.vcex-social-links a:hover',
			'.vcex-testimonials-fullslider.light-skin .sp-button:hover',
			'.vcex-testimonials-fullslider.light-skin .sp-selected-button',
			'.vcex-divider-dots span',
		), $backgrounds );
	}

	/**
	 * Maps custom shortcodes for the VC
	 *
	 * @since  2.1.0
	 */
	public static function total_custom_vc_shortcodes() {

		// Define dir
		$dir = WPEX_VCEX_DIR .'shortcodes/';

		// Array of new modules to add to the VC
		$vcex_modules = apply_filters( 'vcex_builder_modules', array(
			'spacing' => $dir .'spacing.php',
			'divider' => $dir .'divider.php',
			'divider_dots' => $dir .'divider_dots.php',
			'heading' => $dir .'heading.php',
			'button' => $dir .'button.php',
			'icon_box' => $dir .'icon_box.php',
			'teaser' => $dir .'teaser.php',
			'feature' => $dir .'feature.php',
			'callout' => $dir .'callout.php',
			'list_item' => $dir .'list_item.php',
			'bullets' => $dir .'bullets.php',
			'pricing' => $dir .'pricing.php',
			'skillbar' => $dir .'skillbar.php',
			'icon' => $dir .'icon.php',
			'milestone' => $dir .'milestone.php',
			'social_links' => $dir .'social_links.php',
			'navbar' => $dir .'navbar.php',
			'searchbar' => $dir .'searchbar.php',
			'login_form' => $dir .'login_form.php',
			'newsletter_form' => $dir .'newsletter_form.php',
			'image_swap' => $dir .'image_swap.php',
			'image_galleryslider'  => $dir .'image_galleryslider.php',
			'image_flexslider' => $dir .'image_flexslider.php',
			'image_carousel' => $dir .'image_carousel.php',
			'image_grid' => $dir .'image_grid.php',
			'recent_news' => $dir .'recent_news.php',
			'blog_grid' => $dir .'blog_grid.php',
			'blog_carousel' => $dir .'blog_carousel.php',
			'post_type_grid' => $dir .'post_type_grid.php',
			//'post_type_list' => $dir .'post_type_list.php', // @todo finish module
			'post_type_archive' => $dir .'post_type_archive.php',
			'post_type_slider' => $dir .'post_type_slider.php',
			'post_type_carousel' => $dir .'post_type_carousel.php',
			'testimonials_grid' => array(
				'file' => $dir .'testimonials_grid.php',
				'condition' => WPEX_TESTIMONIALS_IS_ACTIVE,
			),
			'testimonials_slider' => array(
				'file' => $dir .'testimonials_slider.php',
				'condition' => WPEX_TESTIMONIALS_IS_ACTIVE,
			),
			'portfolio_grid' => array(
				'file' => $dir .'portfolio_grid.php',
				'condition' => WPEX_PORTFOLIO_IS_ACTIVE,
			),
			'portfolio_carousel' => array(
				'file' => $dir .'portfolio_carousel.php',
				'condition' => WPEX_PORTFOLIO_IS_ACTIVE,
			),
			'staff_grid' => array( 
				'file' => $dir .'staff_grid.php',
				'condition' => WPEX_STAFF_IS_ACTIVE,
			),
			'staff_carousel' => array(
				'file' => $dir .'staff_carousel.php',
				'condition' => WPEX_STAFF_IS_ACTIVE,
			),
			'staff_social' => array(
				'file' => $dir .'staff_social.php',
				'condition' => WPEX_STAFF_IS_ACTIVE,
			),
			'woocommerce_carousel' => array(
				'file' => $dir .'woocommerce_carousel.php',
				'condition' => WPEX_WOOCOMMERCE_ACTIVE,
			),
			'terms_grid' => $dir .'terms_grid.php',
		) );

		// Load mapping files
		if ( ! empty( $vcex_modules ) ) {
			foreach ( $vcex_modules as $key => $val ) {
				if ( is_array( $val ) ) {
					$condition = isset( $val['condition'] ) ? $val['condition'] : true;
					if ( $condition ) {
						require_once( $val['file'] );
					}
				} else {
					require_once( $val );
				}
			}
		}

	}

	/**
	 * Load VC CSS
	 *
	 * @since 2.0.0
	 */
	public static function vc_css_ids( $css ) {
		if ( $ids = wpex_global_obj( 'vc_css_ids' ) ) {
			foreach ( $ids as $id ) {
				if ( function_exists( 'is_shop' ) && is_shop() ) {
					$condition = true;
				} else {
					$condition = ( $id == wpex_global_obj( 'post_id' ) ) ? false : true;
				}
				if ( $condition && $vc_css = get_post_meta( $id, '_wpb_shortcodes_custom_css', true ) ) {
					$css .='/*VC META CSS*/'. $vc_css;
				}
			}
		}
		return $css;
	}

	/**
	 * Add font-family select param type
	 *
	 * @since 2.0.0
	 */
	public static function vcex_font_family_select( $settings, $value ) {
		$output = '<select name="'
				. $settings['param_name']
				. '" class="wpb_vc_param_value wpb-input wpb-select '
				. $settings['param_name']
				. ' ' . $settings['type'] .'">'
				. '<option value="" '. selected( $value, '', false ) .'>'. esc_html__( 'Default', 'total' ) .'</option>';
		// Custom fonts
		if ( function_exists( 'wpex_add_custom_fonts' ) ) {
			$fonts = wpex_add_custom_fonts();
			if ( $fonts && is_array( $fonts ) ) {
				$output .= '<optgroup label="'. esc_html__( 'Custom Fonts', 'total' ) .'">';
				foreach ( $fonts as $font ) {
					$output .= '<option value="'. esc_html( $font ) .'" '. selected( $value, $font, false ) .'>'. esc_html( $font ) .'</option>';
				}
				$output .= '</optgroup>';
			}
		}
		// Get Standard font options
		if ( $std_fonts = wpex_standard_fonts() ) {
			$output .= '<optgroup label="'. esc_html__( 'Standard Fonts', 'total' ) .'">';
				foreach ( $std_fonts as $font ) {
					$output .= '<option value="'. esc_html( $font ) .'" '. selected( $font, $value, false ) .'>'. esc_html( $font ) .'</option>';
				}
			$output .= '</optgroup>';
		}
		// Google font options
		if ( $google_fonts = wpex_google_fonts_array() ) {
			$output .= '<optgroup label="'. esc_html__( 'Google Fonts', 'total' ) .'">';
				foreach ( $google_fonts as $font ) {
					$output .= '<option value="'. esc_html( $font ) .'" '. selected( $font, $value ) .'>'. esc_html( $font ) .'</option>';
				}
			$output .= '</optgroup>';
		}
		$output .= '</select>';
		return $output;
	}

	/**
	 * Alter default templates
	 *
	 * @since 3.2.0
	 */
	public static function default_templates( $templates ) {
		return array(); // remove all default templates
	}
	
}
$wpex_visual_composer_config = new WPEX_Visual_Composer();