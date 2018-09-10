<?php
/**
 * Migrates old Redux options to the Theme Customizer
 *
 * @package Total WordPress Theme
 * @subpackage Updates
 * @version 3.3.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class WPEX_Migrate_Redux {

	// Vars
	private $redux_options;

	/**
	 * Start things up
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// Get old redux options
		$this->redux_options = get_option( 'wpex_options' );

		// If there aren't any options (first time installs ) set customizer to complete
		if ( empty( $this->redux_options) ) {
			update_option( 'wpex_customizer_migration_complete', 'completed' );
		}

		// Otherwise lets run the migration function
		else {

			// Migrate settings
			$this->migrate_settings();

			// Migrate admin login
			$this->migrate_admin_login();

			// Save option to prevent migration from running again
			update_option( 'wpex_customizer_migration_complete', 'completed' );

			// Clear the customizer cache just incase
			remove_theme_mod( 'wpex_customizer_css_cache' );
			remove_theme_mod( 'wpex_customizer_typography_cache' );
		}

	}

	/**
	 * Migrate main settings
	 *
	 * @return array
	 */
	public function migrate_settings() {

		// Make sure vc extensions are enabled
		set_theme_mod( 'extend_visual_composer', true );

		// Migrate theme skin over
		if ( $skin = get_option( 'theme_skin' ) ) {
			set_theme_mod( 'theme_skin', $skin );
		}

		// Move the custom sidebars over
		if ( $sidebars = get_theme_mod( 'redux-widget-areas' ) ) {
			set_theme_mod( 'widget_areas', $sidebars );
		}

		// Get options
		$options = get_option( 'wpex_options' );

		// Get old sections & loop through theme to set new theme mods
		$sections = $this->redux_array();

		if ( $sections ) {

			foreach ( $sections as $section ) {

				$fields = $section['fields'];

				foreach( $fields as $field ) {

					// Main Vars
					$id = isset( $field['id'] ) ? $field['id'] : '';
					$type = isset( $field['type'] ) ? $field['type'] : '';
					$mode = isset( $field['mode'] ) ? $field['mode'] : '';
					$val = isset( $options[$id] ) ? $options[$id] : '';

					// Continute if info type
					if ( 'info' == $type || 'multi-info' == $type ) {
						continue;
					}

					// Alter values
					if ( 'vc_row_bottom_margin' == $id ) {
						if ( ! $val ) {
							$val = '0px';
						}
					}

					// Text
					if( in_array( $type, array( 'text', 'select', 'textarea', 'color', 'button_set', 'editor' ) ) ) {
						if ( 'site_theme' == $id ) {
							update_option( 'site_theme', $val );
						} elseif ( 'blog_cats_exclude' == $id ) {
							if ( $val ) {
								$val = implode( ',', $val );
								set_theme_mod( $id, $val );
							}
						} elseif ( 'custom_admin_login' == $id || 'admin_login_logo' == $id || 'admin_login_logo_height' == $id || 'admin_login_logo_url' == $id || 'admin_login_background_color' == $id || 'admin_login_background_img' == $id || 'admin_login_background_style' == $id || 'admin_login_form_background_color' == $id || 'admin_login_form_background_opacity' == $id || 'admin_login_form_text_color' == $id || 'admin_login_form_top' == $id) {
							// Do nothing
						} else {
							set_theme_mod( $id, $val );
						}
					}

					// Padding
					elseif( 'spacing' == $type && 'padding' == $mode ) {

						if ( $val ) {
							$top = ! empty( $val['padding-top'] ) ? $val['padding-top'] : '0';
							$bottom = ! empty( $val['padding-bottom'] ) ? $val['padding-bottom'] : '0';
							$right = ! empty( $val['padding-right'] ) ? $val['padding-right'] : '0';
							$left = ! empty( $val['padding-left'] ) ? $val['padding-left'] : '0';
							$val = $top .' '. $right .' '. $bottom .' '. $left;
							set_theme_mod( $id, $val );
						}

					}
					// Sorter
					elseif( 'sorter' == $type ) {
						$blocks = $val;
						if ( isset( $blocks['enabled'] ) ) {
							$blocks = $blocks['enabled'];
							unset($blocks['placebo']);
							$blocks = array_keys( $blocks );
							$blocks = implode( ',', $blocks );
							set_theme_mod( $id, $blocks );
						} else {
							set_theme_mod( $id, '' );
						}
					}

					// Link Color
					elseif( 'link_color' == $type ) {
						$regular = isset( $val['regular'] ) ? $val['regular'] : '';
						$hover = isset( $val['hover'] ) ? $val['hover'] : '';
						$active = isset( $val['active'] ) ? $val['active'] : '';
						if ( $regular ) {
							set_theme_mod( $id, $regular );
						}
						if ( $hover ) {
							set_theme_mod( $id .'_hover', $hover );
						}
					}

					// Switch
					elseif( 'switch' == $type ) {
						if( '0' == $val ) {
							$val = false;
						} else {
							$val = true;
						}
						set_theme_mod( $id, $val );
					}

					// Image
					elseif( 'media' == $type ) {
						$val = isset( $val['url'] ) ? $val['url'] : '';
						set_theme_mod( $id, $val );
					}

					// Image Select
					elseif ( 'image_select' == $type ) {
						if( '1' == $val ) {
							$val = false;
						}
						set_theme_mod( $id, $val );
					}

					// Gradient
					elseif ( 'color_gradient' == $type ) {
						$from = isset ( $val['from'] ) ? $val['from'] : '';
						$to = isset ( $val['to'] ) ? $val['to'] : '';
						set_theme_mod( $id, $from );
					}

					// Social
					elseif( 'sortable' == $type && 'top_bar_social_options' == $id ) {
						$array = array();
						if ( is_array( $val ) ) {
						foreach ( $val as $key => $value ) {
							if( 'github-alt' == $key ) {
								$key = 'github';
							} elseif( 'vimeo-square' == $key ) {
								$key = 'vimeo';
							} elseif( 'google-plus' == $key ) {
								$key = 'googleplus';
							}
							$array[$key] = $value;
							set_theme_mod( 'top_bar_social_profiles', $array );
						}
					}

					// Font
					} elseif( 'typography' == $type && 'load_custom_font_1' != $id ) {

						// Get Font
						$font = $val;

						// Remove "font" from ID
						$id = str_replace( '_font', '', $id );
						$id = str_replace( '_typography', '', $id );

						// Standardize id's
						if ( 'breadcrumbs_typography' == $id ) {
							$id = 'breadcrumbs_font';
						} elseif ( 'sidebar_widget_title_typography' == $id ) {
							$id = 'sidebar_widget_title_font';
						} elseif ( 'footer_widget_title_typography' == $id ) {
							$id = 'footer_widget_title_font';
						}

						// Get Font Options
						$family = isset( $font['font-family'] ) ? $font['font-family'] : '';
						$size = isset( $font['font-size'] ) ? intval( $font['font-size'] ) : '';
						$weight = isset( $font['font-weight'] ) ? $font['font-weight'] : '';
						$style = isset( $font['font-style'] ) ? $font['font-style'] : '';
						$color = isset( $font['color'] ) ? $font['color'] : '';
						$letter_spacing = isset( $font['letter-spacing'] ) ? intval( $font['letter-spacing'] ) : '';
						$line_height = isset( $font['line-height'] ) ? intval( $font['line-height'] ) : '';

						// Create array to update theme mod
						$array = array();

						// Update theme mods
						if( $family == 'inherit' ) {
							$array['font-family'] = '';
						} elseif( $family ) {
							$array['font-family'] = $family;
						}

						if( $size == 'inherit' ) {
							$array['font-size'] = '';
						} elseif( $size) {
							$array['font-size'] = $size;
						}

						if( $weight == 'inherit' ) {
							$array['font-weight'] = '';
						} elseif( $weight ) {
							$array['font-weight'] = $weight;
						}

						if( $style == 'inherit' ) {
							$array['font-style'] = '';
						} elseif( $style ) {
							$array['font-style'] = $style;
						}

						if( $color == 'inherit' ) {
							$array['color'] = '';
						} elseif( $color ) {
							$array['color'] = $color;
						}

						if( $letter_spacing == 'inherit' ) {
							$array['letter-spacing'] = '';
						} elseif( $letter_spacing ) {
							$array['letter-spacing'] = $letter_spacing;
						}

						set_theme_mod( $id .'_typography', $array );

					}

					// Alter Id's
					if ( 'footer_col' == $id ) {
						remove_theme_mod( 'footer_col' );
						set_theme_mod( 'footer_widgets_columns', $val );
					} elseif( 'footer_copyright' == $id ) {
						remove_theme_mod( 'footer_copyright' );
						set_theme_mod( 'footer_bottom', $val );
					} elseif( 'extend_visual_composer_extension' == $id ) {
						remove_theme_mod( 'extend_visual_composer_extension' );
						set_theme_mod( 'extend_visual_composer', $val );
					} elseif( 'post_series' == $id ) {
						remove_theme_mod( 'post_series' );
						set_theme_mod( 'post_series_enable', $val );
					} elseif( 'testimonial_entry_image_height' == $id ) {
						remove_theme_mod( 'testimonial_entry_image_height' );
						set_theme_mod( 'testimonials_entry_image_height', $val );
					} elseif( 'testimonial_entry_image_width' == $id ) {
						remove_theme_mod( 'testimonial_entry_image_width' );
						set_theme_mod( 'testimonials_entry_image_width', $val );
					} elseif( 'footer_reveal' == $id ) {
						set_theme_mod( 'footer_reveal', false );
					}

				}
			}
		}


	}

	/**
	 * Migrate the admin login settings
	 *
	 * @return array
	 */
	public function migrate_admin_login() {

		$options = get_option( 'wpex_options' );
		$admin_login_options = array(
			'custom_admin_login',
			'admin_login_logo',
			'admin_login_logo_height',
			'admin_login_logo_url',
			'admin_login_background_color',
			'admin_login_background_img',
			'admin_login_background_style',
			'admin_login_form_background_color',
			'admin_login_form_background_opacity',
			'admin_login_form_text_color',
			'admin_login_form_top',
		);
		$array = array();
		foreach ( $admin_login_options as $id ) {
			$val = $options[$id];
			if ( 'custom_admin_login' == $id ) {
				$id = 'enabled';
			}
			if ( 'admin_login_background_img' == $id || 'admin_login_logo' == $id ) {
				if ( ! empty ( $val['url'] ) ) {
					$val = $val['url'];
				} else {
					$val = '';
				}
			}
			$id = str_replace( 'admin_login_', '', $id );
			$array[$id] = $val;
		}
		set_theme_mod( 'login_page_design', $array );

	}

	/**
	 * Holds the array of old redux settings
	 *
	 * @return array
	 */
	public function redux_array() {
		$sections[] = array(
			'id' => 'general',
			'fields' => array(
				array( 'id' => 'theme_branding', 'type' => 'text' ),
				array( 'id' => 'logo_icon', 'type' => 'select' ),
				array( 'id' => 'logo_icon_right_margin', 'type' => 'text' ),
				array( 'id' => 'logo_icon_color', 'type' => 'color' ),
				array( 'id' => 'custom_logo', 'type' => 'media' ),
				array( 'id' => 'retina_logo', 'type' => 'media' ),
				array( 'id' => 'retina_logo_height', 'type' => 'text' ),
				array( 'id' => 'favicon', 'type' => 'media' ),
				array( 'id' => 'iphone_icon', 'type' => 'media' ),
				array( 'id' => 'ipad_icon', 'type' => 'media' ),
				array( 'id' => 'iphone_icon_retina', 'type' => 'media' ),
				array( 'id' => 'ipad_icon_retina', 'type' => 'media' ),
				array( 'id' => 'tracking', 'type' => 'textarea' ),
			),
		);
		$sections[] = array(
			'id' => 'layout',
			'fields' => array(
				array( 'id' => 'main_layout_style', 'type' => 'select' ),
				array( 'id' => 'boxed_dropdshadow', 'type' => 'switch' ),
				array( 'id' => 'boxed_padding', 'type' => 'text' ),
				array( 'id' => 'main_container_width', 'type' => 'text' ),
				array( 'id' => 'left_container_width', 'type' => 'text' ),
				array( 'id' => 'sidebar_width', 'type' => 'text' ),
			),
		);
		// Responsive
		$sections[] = array(
			'id' => 'responsive',
			'fields' => array(
				array( 'id' => 'responsive', 'type' => 'switch' ),
				array( 'id' => 'mobile_menu_style', 'type' => 'select' ),
				array( 'id' => 'mobile_menu_sidr_direction', 'type' => 'select' ),
				array( 'id' => 'multi-info', 'type' => 'info' ),
				array( 'id' => 'tablet_landscape_main_container_width', 'type' => 'text' ),
				array( 'id' => 'tablet_landscape_left_container_width', 'type' => 'text' ),
				array( 'id' => 'tablet_landscape_sidebar_width', 'type' => 'text' ),
				array( 'id' => 'tablet_main_container_width', 'type' => 'text' ),
				array( 'id' => 'tablet_left_container_width', 'type' => 'text' ),
				array( 'id' => 'tablet_sidebar_width', 'type' => 'text' ),
				array( 'id' => 'mobile_landscape_main_container_width', 'type' => 'text' ),
				array( 'id' => 'mobile_portrait_main_container_width', 'type' => 'text' ),
			),
		);
		// Background
		$sections[] = array(
			'id' => 'background',

			'fields' => array(
				array( 'id' => 'background_color', 'type' => 'color' ),
				array( 'id' => 'background_image_toggle', 'type' => 'switch' ),
				array( 'id' => 'background_image',
					'url' => true ),
				array( 'id' => 'background_style', 'type' => 'select' ),
				array( 'id' => 'background_pattern', 'type' => 'image_select' ),
			),
		);
		// Typography
		$sections[] = array(
			'id' => 'typography',
			'fields' => array(
				array( 'id' => 'body_font', 'type' => 'typography' ),
				array( 'id' => 'headings_font', 'type' => 'typography' ),
				array( 'id' => 'logo_font', 'type' => 'typography' ),
				array( 'id' => 'menu_font', 'type' => 'typography' ),
				array( 'id' => 'menu_dropdown_font', 'type' => 'typography' ),
				array( 'id' => 'page_header_font', 'type' => 'typography' ),
				array( 'id' => 'breadcrumbs_typography', 'type' => 'typography' ),
				array( 'id' => 'sidebar_widget_title_typography', 'type' => 'typography' ),
				array( 'id' => 'footer_widget_title_typography', 'type' => 'typography' ),
				array( 'id' => 'load_custom_font_1', 'type' => 'typography' ),
			),
		);
		$sections[] = array(
			'id' => 'styling_site_header',
			'fields' => array(
				array( 'id' => 'header_background', 'type' => 'color' ),
				array( 'id' => 'logo_color', 'type' => 'color' ),
				array( 'id' => 'logo_hover_color', 'type' => 'color' ),
				array( 'id' => 'search_button_background', 'type' => 'color_gradient' ),
				array( 'id' => 'search_button_color', 'type' => 'color' ),
			),
		);
		$sections[] = array(
			'id' => 'styling_navigation',
			'fields' => array(
				array( 'id' => 'menu_background', 'type' => 'color' ),
				array( 'id' => 'menu_borders', 'type' => 'color' ),
				array( 'id' => 'menu_link_color', 'type' => 'link_color' ),
				array( 'id' => 'menu_link_hover_background', 'type' => 'color' ),
				array( 'id' => 'menu_link_active_background', 'type' => 'color' ),
				array( 'id' => 'dropdown_menu_background', 'type' => 'color' ),
				array( 'id' => 'dropdown_menu_borders', 'type' => 'color' ),
				array( 'id' => 'dropdown_menu_link_color', 'type' => 'link_color' ),
				array( 'id' => 'dropdown_menu_link_hover_bg', 'type' => 'color_gradient' ),
				array( 'id' => 'mega_menu_title', 'type' => 'color' ),
			),
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'mobile_menu_icon_background', 'type' => 'link_color' ),
				array( 'id' => 'mobile_menu_icon_border', 'type' => 'link_color' ),
				array( 'id' => 'mobile_menu_icon_color', 'type' => 'link_color' ),
				array( 'id' => 'mobile_menu_icon_size', 'type' => 'text' ),
				array( 'id' => 'mobile_menu_sidr_background', 'type' => 'color' ),
				array( 'id' => 'mobile_menu_sidr_borders', 'type' => 'color' ),
				array( 'id' => 'mobile_menu_links', 'type' => 'link_color' ),
				array( 'id' => 'mobile_menu_sidr_search_bg', 'type' => 'color' ),
				array( 'id' => 'mobile_menu_sidr_search_color', 'type' => 'color' ),
			),
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'page_header_background', 'type' => 'color' ),
				array( 'id' => 'page_header_title_color', 'type' => 'color' ),
				array( 'id' => 'page_header_top_border', 'type' => 'color' ),
				array( 'id' => 'page_header_bottom_border', 'type' => 'color' ),
				array( 'id' => 'breadcrumbs_text_color', 'type' => 'color' ),
				array( 'id' => 'breadcrumbs_seperator_color', 'type' => 'color' ),
				array( 'id' => 'breadcrumbs_link_color', 'type' => 'link_color' ),
			)
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'sidebar_background', 'type' => 'color' ),
				array( 'id' => 'sidebar_border', 'type' => 'border' ),
				array( 'id' => 'sidebar_headings_color', 'type' => 'color' ),
				array( 'id' => 'sidebar_text_color', 'type' => 'color' ),
				array( 'id' => 'sidebar_link_color', 'type' => 'link_color' ),
				array( 'id' => 'sidebar_borders_color', 'type' => 'color' ),
			),
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'footer_background', 'type' => 'color' ),
				array( 'id' => 'footer_border', 'type' => 'border' ),
				array( 'id' => 'footer_color', 'type' => 'color' ),
				array( 'id' => 'footer_headings_color', 'type' => 'color' ),
				array( 'id' => 'footer_borders', 'type' => 'color' ),
				array( 'id' => 'footer_link_color', 'type' => 'link_color' ),
				array( 'id' => 'bottom_footer_background', 'type' => 'color' ),
				array( 'id' => 'bottom_footer_border', 'type' => 'border' ),
				array( 'id' => 'bottom_footer_color', 'type' => 'color' ),
				array( 'id' => 'bottom_footer_link_color', 'type' => 'link_color' ),
			),
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'link_color', 'type' => 'color' ),
				array( 'id' => 'theme_button_bg', 'type' => 'color_gradient' ),
				array( 'id' => 'theme_button_color', 'type' => 'color' ),
				array( 'id' => 'theme_button_hover_bg', 'type' => 'color_gradient' ),
				array( 'id' => 'theme_button_hover_color', 'type' => 'color' ),
			),
		);
		// ogglebar
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'toggle_bar', 'type' => 'switch' ),
				array( 'id' => 'toggle_bar_page', 'type' => 'select' ),
				array( 'id' => 'toggle_bar_visibility', 'type' => 'select' ),
				array( 'id' => 'toggle_bar_animation', 'type' => 'select' ),
				array( 'id' => 'toggle_bar_btn_bg', 'type' => 'color' ),
				array( 'id' => 'toggle_bar_btn_color', 'type' => 'color' ),
				array( 'id' => 'toggle_bar_btn_hover_bg', 'type' => 'color' ),
				array( 'id' => 'toggle_bar_btn_hover_color', 'type' => 'color' ),
				array( 'id' => 'toggle_bar_bg', 'type' => 'color' ),
				array( 'id' => 'toggle_bar_color', 'type' => 'color' ),
			)
		);
		// Top Bar
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'top_bar', 'type' => 'switch' ),
				array( 'id' => 'top_bar_style', 'type' => 'select' ),
				array( 'id' => 'top_bar_visibility', 'type' => 'select' ),
				array( 'id' => 'top_bar_content', 'type' => 'editor' ),
				array( 'id' => 'top_bar_social', 'type' => 'switch' ),
				array( 'id' => 'top_bar_social_alt', 'type' => 'editor' ),
				array( 'id' => 'top_bar_social_target', 'type' => 'select' ),
				array( 'id' => 'top_bar_social_style', 'type' => 'select' ),
				array( 'id' => 'top_bar_social_options', 'type' => 'sortable' ),
				array( 'id' => 'top_bar_bg', 'type' => 'color' ),
				array( 'id' => 'top_bar_border', 'type' => 'color' ),
				array( 'id' => 'top_bar_text', 'type' => 'color' ),
				array( 'id' => 'top_bar_link_color', 'type' => 'link_color' ),
				array( 'id' => 'top_bar_social_color', 'type' => 'color' ),
				array( 'id' => 'top_bar_social_hover_color', 'type' => 'color' ),
			),
		);
		// Header
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'header_style', 'type' => 'select' ),
				array( 'id' => 'fixed_header', 'type' => 'switch' ),
				array( 'id' => 'fixed_header_opacity', 'type' => 'text' ),
				array( 'id' => 'main_search', 'type' => 'switch' ),
				array( 'id' => 'menu_search_style', 'type' => 'select' ),
				array( 'id' => 'search_dropdown_top_border', 'type' => 'color' ),
				array( 'id' => 'main_search_overlay_top_margin', 'type' => 'text' ),
				array( 'id' => 'header_height', 'type' => 'text' ),
				array( 'id' => 'header_top_padding', 'type' => 'text' ),
				array( 'id' => 'header_bottom_padding', 'type' => 'text' ),
				array( 'id' => 'logo_max_width', 'type' => 'text' ),
				array( 'id' => 'logo_top_margin', 'type' => 'text' ),
				array( 'id' => 'logo_bottom_margin', 'type' => 'text' ),
				array( 'id' => 'header_aside', 'type' => 'editor' ),
				array( 'id' => 'multi-info', 'type' => 'info' ),
				array( 'id' => 'menu_arrow_down', 'type' => 'switch' ),
				array( 'id' => 'menu_arrow_side', 'type' => 'switch' ),
				array( 'id' => 'menu_dropdown_top_border', 'type' => 'switch' ),
				array( 'id' => 'menu_dropdown_top_border_color', 'type' => 'color' ),
				array( 'id' => 'page_header_style', 'type' => 'select' ),

			),
		);
		// Portfolio
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'portfolio_enable', 'type' => 'switch' ),
				array( 'id' => 'portfolio_page', 'type' => 'select' ),
				array( 'id' => 'portfolio_archive_layout', 'type' => 'select' ),
				array( 'id' => 'portfolio_archive_grid_style', 'type' => 'select' ),
				array( 'id' => 'portfolio_archive_grid_equal_heights', 'type' => 'switch' ),
				array( 'id' => 'portfolio_entry_columns', 'type' => 'select' ),
				array( 'id' => 'portfolio_archive_posts_per_page', 'type' => 'text' ),
				array( 'id' => 'portfolio_entry_overlay_style', 'type' => 'select' ),
				array( 'id' => 'portfolio_entry_details', 'type' => 'switch' ),
				array( 'id' => 'portfolio_entry_excerpt_length', 'type' => 'text' ),
				array( 'id' => 'portfolio_single_layout', 'type' => 'select' ),
				array( 'id' => 'portfolio_single_media', 'type' => 'switch' ),
				array( 'id' => 'portfolio_comments', 'type' => 'switch' ),
				array( 'id' => 'portfolio_next_prev', 'type' => 'switch' ),
				array( 'id' => 'portfolio_related', 'type' => 'switch' ),
				array( 'id' => 'portfolio_related_columns', 'type' => 'select' ),
				array( 'id' => 'portfolio_related_count', 'type' => 'text' ),
				array( 'id' => 'portfolio_related_title', 'type' => 'text' ),
				array( 'id' => 'portfolio_related_excerpts', 'type' => 'switch' ),
				array( 'id' => 'portfolio_admin_icon', 'type' => 'select' ),
				array( 'id' => 'portfolio_labels', 'type' => 'text' ),
				array( 'id' => 'portfolio_slug', 'type' => 'text' ),
				array( 'id' => 'portfolio_cat_labels', 'type' => 'text' ),
				array( 'id' => 'portfolio_cat_slug', 'type' => 'text' ),
				array( 'id' => 'portfolio_tag_labels', 'type' => 'text' ),
				array( 'id' => 'portfolio_tag_slug', 'type' => 'text' ),
				array( 'id' => 'portfolio_custom_sidebar', 'type' => 'switch' ),
				array( 'id' => 'breadcrumbs_portfolio_cat', 'type' => 'switch' ),
				array( 'id' => 'portfolio_search', 'type' => 'switch' ),
			),
		);
		//Staff
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'staff_enable', 'type' => 'switch' ),
				array( 'id' => 'staff_page', 'type' => 'select' ),
				array( 'id' => 'staff_archive_layout', 'type' => 'select' ),
				array( 'id' => 'staff_archive_grid_style', 'type' => 'select' ),
				array( 'id' => 'staff_archive_grid_equal_heights', 'type' => 'switch' ),
				array( 'id' => 'staff_entry_columns', 'type' => 'select' ),
				array( 'id' => 'staff_archive_posts_per_page', 'type' => 'text' ),
				array( 'id' => 'staff_entry_overlay_style', 'type' => 'select' ),
				array( 'id' => 'staff_entry_details', 'type' => 'switch' ),
				array( 'id' => 'staff_entry_excerpt_length', 'type' => 'text' ),
				array( 'id' => 'staff_entry_social', 'type' => 'switch' ),
				array( 'id' => 'staff_single_layout', 'type' => 'select' ),
				array( 'id' => 'staff_single_media', 'type' => 'switch' ),
				array( 'id' => 'staff_comments', 'type' => 'switch' ),
				array( 'id' => 'staff_next_prev', 'type' => 'switch' ),
				array( 'id' => 'staff_related', 'type' => 'switch' ),
				array( 'id' => 'staff_related_columns', 'type' => 'select' ),
				array( 'id' => 'staff_related_count', 'type' => 'text' ),
				array( 'id' => 'staff_related_title', 'type' => 'text' ),
				array( 'id' => 'staff_related_excerpts', 'type' => 'switch' ),
				array( 'id' => 'staff_admin_icon', 'type' => 'select' ),
				array( 'id' => 'staff_labels', 'type' => 'text' ),
				array( 'id' => 'staff_slug', 'type' => 'text' ),
				array( 'id' => 'staff_cat_labels', 'type' => 'text' ),
				array( 'id' => 'staff_cat_slug', 'type' => 'text' ),
				array( 'id' => 'staff_tag_labels', 'type' => 'text' ),
				array( 'id' => 'staff_tag_slug', 'type' => 'text' ),
				array( 'id' => 'staff_custom_sidebar', 'type' => 'switch' ),
				array( 'id' => 'breadcrumbs_staff_cat', 'type' => 'switch' ),
				array( 'id' => 'staff_search', 'type' => 'switch' ),
			)
		);
		// testimonials
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'testimonials_enable', 'type' => 'switch' ),
				array( 'id' => 'testimonials_page', 'type' => 'select' ),
				array( 'id' => 'testimonials_archive_layout', 'type' => 'select' ),
				array( 'id' => 'testimonials_entry_columns', 'type' => 'select' ),
				array( 'id' => 'testimonials_archive_posts_per_page', 'type' => 'text' ),
				array( 'id' => 'testimonial_post_style', 'type' => 'select' ),
				array( 'id' => 'testimonials_single_layout', 'type' => 'select' ),
				array( 'id' => 'testimonials_comments', 'type' => 'switch' ),
				array( 'id' => 'testimonials_admin_icon', 'type' => 'select' ),
				array( 'id' => 'testimonials_labels', 'type' => 'text' ),
				array( 'id' => 'testimonials_slug', 'type' => 'text' ),
				array( 'id' => 'testimonials_cat_labels', 'type' => 'text' ),
				array( 'id' => 'testimonials_cat_slug', 'type' => 'text' ),
				array( 'id' => 'testimonials_search', 'type' => 'switch' ),
				array( 'id' => 'testimonial_custom_sidebar', 'type' => 'switch' ),
				array( 'id' => 'breadcrumbs_testimonials_cat', 'type' => 'switch' ),
			),
		);
		// WooCommerce
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'woo_menu_icon', 'type' => 'switch' ),
				array( 'id' => 'woo_menu_icon_amount', 'type' => 'switch' ),
				array( 'id' => 'woo_menu_icon_style', 'type' => 'select' ),
				array( 'id' => 'woo_menu_icon_custom_link', 'type' => 'text' ),
				array( 'id' => 'woo_shop_overlay_top_margin', 'type' => 'text' ),
				array( 'id' => 'woo_custom_sidebar', 'type' => 'switch' ),
				array( 'id' => 'woo_shop_slider', 'type' => 'text' ),
				array( 'id' => 'woo_shop_posts_per_page', 'type' => 'text' ),
				array( 'id' => 'woo_shop_layout', 'type' => 'select', ),
				array( 'id' => 'woocommerce_shop_columns', 'type' => 'select' ),
				array( 'id' => 'woo_category_description_position', 'type' => 'select' ),
				array( 'id' => 'woo_shop_title', 'type' => 'switch' ),
				array( 'id' => 'woo_shop_sort', 'type' => 'switch' ),
				array( 'id' => 'woo_shop_result_count', 'type' => 'switch' ),
				array( 'id' => 'multi-info', 'type' => 'info' ),
				array( 'id' => 'woo_shop_single_title', 'type' => 'text' ),
				array( 'id' => 'woo_product_layout', 'type' => 'select' ),
				array( 'id' => 'woocommerce_upsells_count', 'type' => 'text' ),
				array( 'id' => 'woocommerce_upsells_columns', 'type' => 'select' ),
				array( 'id' => 'woocommerce_related_count', 'type' => 'text' ),
				array( 'id' => 'woocommerce_related_columns', 'type' => 'select' ),
				array( 'id' => 'woo_product_meta', 'type' => 'switch' ),
				array( 'id' => 'woo_product_tabs_headings', 'type' => 'switch' ),
				array( 'id' => 'woo_next_prev', 'type' => 'switch' ),
				array( 'id' => 'multi-info', 'type' => 'info' ),
				array( 'id' => 'woocommerce_cross_sells_count', 'type' => 'text' ),
				array( 'id' => 'woocommerce_cross_sells_columns', 'type' => 'select' ),
				array( 'id' => 'shop_button_background', 'type' => 'color_gradient' ),
				array( 'id' => 'shop_button_color', 'type' => 'color' ),
				array( 'id' => 'onsale_bg', 'type' => 'color_gradient' ),
				array( 'id' => 'woo_product_title_link_color', 'type' => 'link_color' ),
				array( 'id' => 'woo_single_price_color', 'type' => 'color' ),
				array( 'id' => 'woo_stars_color', 'type' => 'color' ),
				array( 'id' => 'woo_single_tabs_active_border_color', 'type' => 'color' ),
			),
		);
		// Blog
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'blog_page', 'type' => 'select' ),
				array( 'id' => 'blog_cats_exclude', 'type' => 'select' ),
				array( 'id' => 'blog_style', 'type' => 'select' ),
				array( 'id' => 'blog_grid_columns', 'type' => 'select' ),
				array( 'id' => 'blog_grid_style', 'type' => 'select' ),
				array( 'id' => 'blog_archives_layout', 'type' => 'select' ),
				array( 'id' => 'blog_pagination_style', 'type' => 'select' ),
				array( 'id' => 'category_descriptions', 'type' => 'switch' ),
				array( 'id' => 'blog_entry_composer', 'type' => 'sorter' ),
				array( 'id' => 'blog_entry_image_lightbox', 'type' => 'switch' ),
				array( 'id' => 'blog_entry_image_hover_animation', 'type' => 'select' ),
				array( 'id' => 'blog_exceprt', 'type' => 'switch' ),
				array( 'id' => 'blog_excerpt_length', 'type' => 'text' ),
				array( 'id' => 'blog_entry_readmore', 'type' => 'switch' ),
				array( 'id' => 'blog_entry_readmore_text', 'type' => 'text' ),
				array( 'id' => 'blog_entry_author_avatar', 'type' => 'switch' ),
				array( 'id' => 'blog_single_layout', 'type' => 'select' ),
				array( 'id' => 'blog_single_header', 'type' => 'select' ),
				array( 'id' => 'blog_single_header_custom_text', 'type' => 'text' ),
				array( 'id' => 'blog_single_composer', 'type' => 'sorter' ),
				array( 'id' => 'blog_post_image_lightbox', 'type' => 'switch' ),
				array( 'id' => 'blog_thumbnail_caption', 'type' => 'switch' ),
				array( 'id' => 'blog_related_title', 'type' => 'text' ),
				array( 'id' => 'blog_related_columns', 'type' => 'select' ),
				array( 'id' => 'blog_related_count', 'type' => 'text' ),
				array( 'id' => 'blog_related_excerpt', 'type' => 'switch' ),
				array( 'id' => 'blog_related_excerpt_length', 'type' => 'text' ),
				array( 'id' => 'category_description_position', 'type' => 'select' ),
				array( 'id' => 'breadcrumbs_blog_cat', 'type' => 'switch' ),
				array( 'id' => 'post_series', 'type' => 'switch' ),
				array( 'id' => 'post_series_labels', 'type' => 'text' ),
				array( 'id' => 'post_series_slug', 'type' => 'text' ),
			),
		);
		// Images
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'image_resizing', 'type' => 'switch' ),
				array( 'id' => 'retina', 'type' => 'switch' ),
				array( 
					"id" => "blog_entry_image_width",
					"type" => "text" ),
				array( "id" => "blog_entry_image_height",
					"type" => "text" ),
				array( 
					"id" => "blog_post_image_width",
					"type" => "text" ),
				array( "id" => "blog_post_image_height",
					"type" => "text" ),
				array( 
					"id" => "blog_post_full_image_width",
					"type" => "text" ),
				array( "id" => "blog_post_full_image_height",
					"type" => "text" ),
				array( 
					"id" => "blog_related_image_width",
					"type" => "text" ),

				array( "id" => "blog_related_image_height",
					"type" => "text" ),
				array( "id" => "portfolio_entry_image_width",
					"type" => "text" ),
				array( "id" => "portfolio_entry_image_height",
					"type" => "text" ),
				array( 
					"id" => "staff_entry_image_width",
					"type" => "text" ),
				array( "id" => "staff_entry_image_height",
					"type" => "text" ),
				array( 
					"id" => "testimonial_entry_image_width",
					"type" => "text" ),
				array( "id" => "testimonial_entry_image_height",
					"type" => "text" ),
				array( "id" => "woo_entry_width",
					"type" => "text" ),
				array( "id" => "woo_entry_height",
					"type" => "text" ),
				array( "id" => "woo_post_image_width",
					"type" => "text" ),
				array( "id" => "woo_post_image_height",
					"type" => "text" ),
				array( "id" => "woo_cat_entry_width",
					"type" => "text" ),
				array( "id" => "woo_cat_entry_height",
					"type" => "text" ),
				array( "id" => "gallery_image_width",
					"type" => "text" ),
				array( "id" => "gallery_image_height",
					"type" => "text" ),
			)
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'error_page_redirect', 'type' => 'switch' ),
				array( 'id' => 'error_page_title', 'type' => 'text' ),
				array( 'id' => 'error_page_text', 'type' => 'editor' ),
				array( 'id' => 'error_page_styling', 'type' => 'switch' ),
			),
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'footer_reveal', 'type' => 'switch' ),
				array( 'id' => 'multi-info', 'type' => 'info' ),
				array( 'id' => 'callout', 'type' => 'switch' ),
				array( 'id' => 'callout_visibility', 'type' => 'select' ),
				array( 'id' => 'callout_text', 'type' => 'editor' ),
				array( 'id' => 'callout_link', 'type' => 'text' ),
				array( 'id' => 'callout_link_txt', 'type' => 'text' ),
				array( 'id' => 'footer_callout_bg', 'type' => 'color' ),
				array( 'id' => 'footer_callout_border', 'type' => 'color' ),
				array( 'id' => 'footer_callout_color', 'type' => 'color' ),
				array( 'id' => 'footer_callout_link_color', 'type' => 'link_color' ),
				array( 'id' => 'footer_callout_button_bg', 'type' => 'color_gradient' ),
				array( 'id' => 'footer_callout_button_color', 'type' => 'color' ),
				array( 'id' => 'footer_callout_button_hover_bg', 'type' => 'color_gradient' ),
				array( 'id' => 'footer_callout_button_hover_color', 'type' => 'color' ),
				array( 'id' => 'callout_button_target', 'type' => 'select' ),
				array( 'id' => 'callout_button_rel', 'type' => 'select' ),
				array( 'id' => 'callout_button_border_radius', 'type' => 'text' ),
				array( 'id' => 'multi-info', 'type' => 'info' ),
				array( 'id' => 'footer_widgets', 'type' => 'switch' ),
				array( 'id' => 'footer_col', 'type' => 'select' ),
				array( 'id' => 'multi-info', 'type' => 'info' ),
				array( 'id' => 'footer_copyright', 'type' => 'switch' ),
				array( 'id' => 'footer_copyright_text', 'type' => 'editor' ),
				array( 'id' => 'multi-info', 'type' => 'info' ),
				array( 'id' => 'scroll_top', 'type' => 'switch' ),
				array( 'id' => 'scroll_top_border_radius', 'type' => 'text' ),
				array( 'id' => 'scroll_top_bg', 'type' => 'link_color' ),
				array( 'id' => 'scroll_top_border', 'type' => 'link_color' ),
				array( 'id' => 'scroll_top_color', 'type' => 'link_color' ),
			)
		);
		// Visual Composer
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'visual_composer_theme_mode', 'type' => 'switch' ),
				array( 'id' => 'extend_visual_composer_extension', 'type' => 'switch' ),
				array( 'id' => 'vc_row_bottom_margin', 'type' => 'text' ),
				array( 'id' => 'vcex_text_separator_two_border_color', 'type' => 'color' ),
				array( 'id' => 'vcex_text_tab_two_bottom_border', 'type' => 'color' ),
				array( 'id' => 'vcex_carousel_arrows', 'type' => 'color' ),
				array( 'id' => 'vcex_pricing_featured_default', 'type' => 'color' ),
				array( 'id' => 'vcex_grid_filter_active_color', 'type' => 'color' ),
				array( 'id' => 'vcex_grid_filter_active_bg', 'type' => 'color' ),
				array( 'id' => 'vcex_grid_filter_active_border', 'type' => 'color' ),
				array( 'id' => 'vcex_recent_news_date_bg', 'type' => 'color' ),
				array( 'id' => 'vcex_recent_news_date_color', 'type' => 'color' ),
				array( 'id' => 'vcex_icon_box_hover_color', 'type' => 'color' ),
			)
		);

		$sections[] = array(
			'fields' => array(
				array( 'id' => 'custom_admin_login', 'type' => 'switch' ),
				array( 'id' => 'admin_login_logo', 'type' => 'media' ),
				array( 'id' => 'admin_login_logo_height', 'type' => 'text' ),
				array( 'id' => 'admin_login_logo_url', 'type' => 'text' ),
				array( 'id' => 'admin_login_background_color', 'type' => 'color' ),
				array( 'id' => 'admin_login_background_img', 'type' => 'media' ),
				array( 'id' => 'admin_login_background_style', 'type' => 'select' ),
				array( 'id' => 'admin_login_form_background_color', 'type' => 'color' ),
				array( 'id' => 'admin_login_form_background_opacity', 'type' => 'text' ),
				array( 'id' => 'admin_login_form_text_color', 'type' => 'color' ),
				array( 'id' => 'admin_login_form_top', 'type' => 'text' ),
			),
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'social_share_position', 'type' => 'select' ),
				array( 'id' => 'social_share_heading', 'type' => 'text' ),
				array( 'id' => 'social_share_style', 'type' => 'select' ),
				array( 'id' => 'social_share_blog_posts', 'type' => 'switch' ),
				array( 'id' => 'social_share_blog_entries', 'type' => 'switch' ),
				array( 'id' => 'social_share_pages', 'type' => 'switch' ),
				array( 'id' => 'social_share_portfolio', 'type' => 'switch' ),
				array( 'id' => 'social_share_staff', 'type' => 'switch' ),
				array( 'id' => 'social_share_woo', 'type' => 'switch' ),
				array( 'id' => 'social_share_sites', 'type' => 'checkbox' ),
			),
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'lightbox_skin', 'type' => 'select' ),
				array( 'id' => 'lightbox_thumbnails', 'type' => 'switch' ),
				array( 'id' => 'lightbox_arrows', 'type' => 'switch' ),
				array( 'id' => 'lightbox_mousewheel', 'type' => 'switch' ),
				array( 'id' => 'lightbox_titles', 'type' => 'switch' ),
				array( 'id' => 'lightbox_fullscreen', 'type' => 'switch' ),
			),
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'sidebar_headings', 'type' => 'select' ),
				array( 'id' => 'footer_headings', 'type' => 'select' ),
				array( 'id' => 'breadcrumbs', 'type' => 'switch' ),
				array( 'id' => 'breadcrumbs_position', 'type' => 'select' ),
				array( 'id' => 'breadcrumbs_home_title', 'type' => 'text' ),
				array( 'id' => 'breadcrumbs_title_trim', 'type' => 'text' ),
				array( 'id' => 'remove_posttype_slugs', 'type' => 'switch' ),
			),
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'page_single_layout', 'type' => 'select' ),
				array( 'id' => 'pages_custom_sidebar', 'type' => 'switch' ),
				array( 'id' => 'search_custom_sidebar', 'type' => 'switch' ),
				array( 'id' => 'shortcodes_tinymce', 'type' => 'switch' ),
				array( 'id' => 'custom_wp_gallery', 'type' => 'switch' ),
				array( 'id' => 'blog_dash_thumbs', 'type' => 'switch' ),
				array( 'id' => 'page_comments', 'type' => 'switch' ),
				array( 'id' => 'widget_icons', 'type' => 'switch' ),
				array( 'id' => 'page_featured_image', 'type' => 'switch' ),
				array( 'id' => 'trim_custom_excerpts', 'type' => 'switch' ),
				array( 'id' => 'search_posts_per_page', 'type' => 'text' ),
				array( 'id' => 'posts_meta_options', 'type' => 'checkbox' ),
			),
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'minify_js', 'type' => 'switch' ),
			),
		);
		$sections[] = array(
			'fields' => array(
				array( 'id' => 'custom_css', 'type' => 'textarea' ),
			),
		);
		return $sections;

	}

}
new WPEX_Migrate_Redux;