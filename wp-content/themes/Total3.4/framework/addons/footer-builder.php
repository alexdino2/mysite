<?php
/**
 * Footer Builder Addon
 *
 * @package Total WordPress theme
 * @subpackage Framework
 * @version 3.4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class WPEX_Footer_Builder {

	/**
	 * Start things up
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Define footer ID
		$this->footer_builder_id = apply_filters( 'wpex_footer_builder_page_id', wpex_get_mod( 'footer_builder_page_id' ) );

		// Add admin page
		add_action( 'admin_menu', array( $this, 'add_page' ), 20 );

		// Register admin options
		add_action( 'admin_init', array( $this, 'register_page_options' ) );

		// Run actions and filters if footer_builder ID is defined
		if ( $this->footer_builder_id ) {

			// Do not register footer sidebars
			add_filter( 'wpex_register_footer_sidebars', '__return_false' );

			// Alter the footer on init
			add_action( 'wp', array( $this, 'alter_footer' ) );

			// Remove all actions on footer builder page
			add_action( 'wp_head', array( $this, 'remove_actions' ) );

			// Include ID for Visual Composer custom CSS
			add_filter( 'wpex_vc_css_ids', array( $this, 'wpex_vc_css_ids' ) );

			// Remove sidebar on footer builder page
			add_filter( 'wpex_post_layout_class', array( $this, 'remove_sidebar_on_footer_builder' ) );

			// Remove footer customizer settings
			add_filter( 'wpex_customizer_panels', array( $this, 'remove_customizer_footer_panel' ) );

		}

	}

	/**
	 * Add sub menu page
	 *
	 * @since 2.0.0
	 */
	public function add_page() {
		add_submenu_page(
			WPEX_THEME_PANEL_SLUG,
			esc_html__( 'Footer Builder', 'total' ),
			esc_html__( 'Footer Builder', 'total' ),
			'administrator',
			WPEX_THEME_PANEL_SLUG .'-footer-builder',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Function that will register admin page options
	 *
	 * @since 2.0.0
	 */
	public function register_page_options() {

		// Register settings
		register_setting( 'wpex_footer_builder', 'footer_builder', array( $this, 'sanitize' ) );

		// Add main section to our options page
		add_settings_section( 'wpex_footer_builder_main', false, array( $this, 'section_main_callback' ), 'wpex-footer-builder-admin' );

		// Custom Page ID
		add_settings_field(
			'footer_builder_page_id',
			esc_html__( 'Footer Builder page', 'total' ),
			array( $this, 'content_id_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main'
		);

		// Footer Bottom
		add_settings_field(
			'footer_builder_footer_bottom',
			esc_html__( 'Footer Bottom', 'total' ),
			array( $this, 'footer_builder_footer_bottom_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main'
		);

		// Fixed Footer
		add_settings_field(
			'fixed_footer',
			esc_html__( 'Fixed Footer', 'total' ),
			array( $this, 'fixed_footer_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main'
		);

		// Footer Reveal
		add_settings_field(
			'footer_reveal',
			esc_html__( 'Footer Reveal', 'total' ),
			array( $this, 'footer_reveal_field_callback' ),
			'wpex-footer-builder-admin',
			'wpex_footer_builder_main'
		);

	}

	/**
	 * Sanitization callback
	 *
	 * @since 2.0.0
	 */
	public static function sanitize( $options ) {

		// Update footer builder page ID
		if ( ! empty( $options['content_id'] ) ) {
			set_theme_mod( 'footer_builder_page_id', $options['content_id'] );
		} else {
			remove_theme_mod( 'footer_builder_page_id' );
		}

		// Footer Bottom - Disabled by default
		if ( empty( $options['footer_builder_footer_bottom'] ) ) {
			remove_theme_mod( 'footer_builder_footer_bottom' );
		} else {
			set_theme_mod( 'footer_builder_footer_bottom', 1 );
		}

		// Update fixed footer - Disabled by default
		if ( empty( $options['fixed_footer'] ) ) {
			remove_theme_mod( 'fixed_footer' );
		} else {
			set_theme_mod( 'fixed_footer', 1 );
		}

		// Update footer Reveal - Disabled by default
		if ( empty( $options['footer_reveal'] ) ) {
			remove_theme_mod( 'footer_reveal' );
		} else {
			set_theme_mod( 'footer_reveal', true );
		}

		// Dont save anything in the options table
		$options = '';
		return;
	}

	/**
	 * Main Settings section callback
	 *
	 * @since 2.0.0
	 */
	public static function section_main_callback( $options ) {
		// Leave blank
	}

	/**
	 * Fields callback functions
	 *
	 * @since 2.0.0
	 */

	// Footer Builder Page ID
	public static function content_id_field_callback() { ?>

		<?php
		// Get footer builder page ID
		$page_id = wpex_get_mod( 'footer_builder_page_id' ); ?>

		<?php
		// Display dropdown of pages
		wp_dropdown_pages( array(
			'echo'             => true,
			'selected'         => $page_id,
			'name'             => 'footer_builder[content_id]',
			'id'               => 'wpex-footer-builder-select',
			'show_option_none' => esc_html__( 'None - Display Widgetized Footer', 'total' ),
		) ); ?>
		<br />

		<p class="description"><?php esc_html_e( 'Select your custom page for your footer layout.', 'total' ) ?></p>

		<br />

		<div id="wpex-footer-builder-edit-links">

			<a href="<?php echo admin_url( 'post.php?post='. $page_id .'&action=edit' ); ?>" class="button" target="_blank">
				<?php esc_html_e( 'Backend Edit', 'total' ); ?>
			</a>

			<?php if ( WPEX_VC_ACTIVE ) { ?>

				<a href="<?php echo admin_url( 'post.php?vc_action=vc_inline&post_id='. $page_id .'&post_type=page' ); ?>" class="button" target="_blank">
					<?php esc_html_e( 'Frontend Edit', 'total' ); ?>
				</a>

			<?php } ?>

			<a href="<?php echo get_permalink( $page_id ); ?>" class="button" target="_blank">
				<?php esc_html_e( 'Preview', 'total' ); ?>
			</a>

		</div>
		
	<?php }

	/**
	 * Footer Bottom Callback
	 *
	 * @since 2.0.0
	 */
	public static function footer_builder_footer_bottom_field_callback() {

		// Get theme mod val
		$val = wpex_get_mod( 'footer_builder_footer_bottom', false );
		$val = $val ? 'on' : false; ?>
		
			<input type="checkbox" name="footer_builder[footer_builder_footer_bottom]" id="wpex-footer-builder-footer-bottom" <?php checked( $val, 'on' ); ?>>
		<?php
	}

	/**
	 * Fixed Footer Callback
	 *
	 * @since 2.0.0
	 */
	public static function fixed_footer_field_callback() {

		// Get theme mod val
		$val = wpex_get_mod( 'fixed_footer', false );
		$val = $val ? 'on' : false; ?>
		
			<input type="checkbox" name="footer_builder[fixed_footer]" id="wpex-footer-builder-fixed" <?php checked( $val, 'on' ); ?>>
		<?php
	}

	/**
	 * Footer Reveal Callback
	 *
	 * @since 2.0.0
	 */
	public static function footer_reveal_field_callback() {

		// Get theme mod val
		$val = wpex_get_mod( 'footer_reveal' );
		$val = $val ? 'on' : false; ?>

			<input type="checkbox" name="footer_builder[footer_reveal]" id="wpex-footer-builder-reveal" <?php checked( $val, 'on' ); ?>>

		<?php
	}

	/**
	 * Settings page output
	 *
	 * @since 2.0.0
	 */
	public static function create_admin_page() { ?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Footer Builder', 'total' ); ?></h2>
			<p>
				<?php echo esc_html__( 'By default the footer consists of a simple widgetized area. For more complex layouts you can use the option below to select a page which will hold the content and layout for your site footer. Selecting a custom footer will remove all footer functions (footer widgets and footer customizer options) so you can create an entire footer using the Visual Composer and not load that extra functions.', 'total' ); ?>
			</p>
			<form method="post" action="options.php">
				<?php settings_fields( 'wpex_footer_builder' ); ?>
				<?php do_settings_sections( 'wpex-footer-builder-admin' ); ?>
				<?php submit_button(); ?>
			</form>
			<script>
				( function( $ ) {
					"use strict";

					// Hide/Show fields
					var	$select       = $( '#wpex-footer-builder-select' ),
						$fieldsTohide = $( '#wpex-footer-builder-fixed, #wpex-footer-builder-footer-bottom, #wpex-footer-builder-reveal' ),
						$footerLinks  = $( '#wpex-footer-builder-edit-links' );

					// Check initial val
					if ( ! $select.val() ) {
						$fieldsTohide.closest( 'tr' ).hide();
						$footerLinks.hide();
					}

					// Check on change
					$( $select ).change(function () {
						if ( ! $( this ).val() ) {
							$fieldsTohide.closest( 'tr' ).hide();
							$footerLinks.hide();
						} else {
							$fieldsTohide.closest( 'tr' ).show();
							$footerLinks.show();
						}
					} );
				} ) ( jQuery );
			</script>
		</div><!-- .wrap -->
	<?php }

	/**
	 * Remove the footer and add custom footer if enabled
	 *
	 * @since 2.0.0
	 */
	public function alter_footer() {

		// Remove theme footer
		remove_action( 'wpex_hook_wrap_bottom', 'wpex_footer', 10 );

		// Remove all actions in footer hooks
		$hooks = wpex_theme_hooks();
		if ( isset( $hooks['footer']['hooks'] ) ) {
			foreach( $hooks['footer']['hooks'] as $hook ) {
				remove_all_actions( $hook, false );
			}
		}

		// Re add callout
		add_action( 'wpex_hook_footer_before', 'wpex_footer_callout' );

		// Re-add footer bottom if enabled
		if ( wpex_get_mod( 'footer_builder_footer_bottom', false ) ) {
			add_action( 'wpex_hook_footer_after', 'wpex_footer_bottom' );
		}

		// Re add reveal if enabled
		if ( get_theme_mod( 'footer_reveal' ) ) {
			add_action( 'wpex_hook_footer_before', 'wpex_footer_reveal_open', 0 );
			add_action( 'wpex_hook_footer_after', 'wpex_footer_reveal_close', 99 );
		}

		// Add builder footer
		add_action( 'wpex_hook_wrap_bottom', array( $this, 'get_part' ), 10 );

	}

	/**
	 * Add footer builder to array of ID's with CSS to load site-wide
	 *
	 * @since 2.0.0
	 */
	public function wpex_vc_css_ids( $ids ) {
		$ids[] = $this->footer_builder_id;
		return $ids;
	}

	/**
	 * Remove all theme actions
	 *
	 * @since 2.0.0
	 */
	public function remove_actions() {
		if ( is_page( $this->footer_builder_id ) ) {
			wpex_remove_actions();
		}
	}

	/**
	 * Remove the footer and add custom footer if enabled
	 *
	 * @since 2.0.0
	 */
	public static function remove_customizer_footer_panel( $panels ) {
		unset( $panels['footer_widgets'] );
		if ( ! wpex_get_mod( 'footer_builder_footer_bottom', false ) ) {
			unset( $panels['footer_bottom'] );
		}
		return $panels;
	}

	/**
	 * Make Footer builder page full-width (no sidebar)
	 *
	 * @since 2.0.0
	 */
	public function remove_sidebar_on_footer_builder( $class ) {

		// Set the footer builder to "full-width" by default
		if ( is_page( $this->footer_builder_id ) ) {
			$class = 'full-width';
		}

		// Return correct class
		return $class;

	}

	/**
	 * Gets the footer builder template part if the footer is enabled
	 *
	 * @since 2.0.0
	 */
	public static function get_part() {
		if ( wpex_global_obj( 'has_footer' ) ) {
			get_template_part( 'partials/footer/footer-builder' );
		}
	}

}
$wpex_footer_builder = new WPEX_Footer_Builder();