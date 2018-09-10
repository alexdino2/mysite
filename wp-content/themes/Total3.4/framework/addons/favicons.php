<?php
/**
 * Adds favicons and mobile icon meta to the wp_head
 *
 * @package Total WordPress theme
 * @subpackage Framework
 * @version 3.3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
if ( ! class_exists( 'WPEX_Favicons' ) ) {
	class WPEX_Favicons {

		/**
		 * Start things up
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_page' ) );
			add_action( 'admin_init', array( $this,'register_page_options' ) );
			add_action( 'admin_enqueue_scripts',array( $this,'scripts' ) );
			add_action( 'wp_head', array( $this, 'output_favicons' ) );
		}

		/**
		 * Add sub menu page
		 *
		 * @since 1.6.0
		 */
		public function add_page() {
			add_submenu_page(
				WPEX_THEME_PANEL_SLUG,
				esc_html__( 'Favicons', 'total' ),
				esc_html__( 'Favicons', 'total' ),
				'administrator',
				WPEX_THEME_PANEL_SLUG .'-favicons',
				array( $this, 'create_admin_page' )
			);
		}

		/**
		 * Load scripts
		 *
		 * @since 1.6.0
		 */
		public static function scripts( $hook ) {

			// Only load scripts when needed
			if ( WPEX_ADMIN_PANEL_HOOK_PREFIX . '-favicons' != $hook ) {
				return;
			}

			// Media Uploader
			wp_enqueue_media();
			wp_enqueue_script(
				'wpex-media-uploader-field',
				WPEX_FRAMEWORK_DIR_URI .'addons/assets/admin-fields/media-uploader.js',
				array( 'media-upload' ),
				false,
				true
			);

			// CSS
			wp_enqueue_style( 'wpex-admin', WPEX_FRAMEWORK_DIR_URI .'addons/assets/admin-fields/admin.css' );

		}

		/**
		 * Function that will register admin page options.
		 *
		 * @since 1.6.0
		 */
		public function register_page_options() {

			// Register Setting
			register_setting( 'wpex_favicons', 'wpex_favicons', array( $this, 'sanitize' ) );

			// Add main section to our options page
			add_settings_section( 'wpex_favicons_main', false, array( $this, 'section_main_callback' ), 'wpex-favicons' );

			// Favicon
			add_settings_field(
				'wpex_favicon',
				esc_html__( 'Favicon', 'total' ),
				array( $this, 'favicon_callback' ),
				'wpex-favicons',
				'wpex_favicons_main'
			);

			// iPhone
			add_settings_field(
				'wpex_iphone_icon',
				esc_html__( 'Apple iPhone Icon ', 'total' ),
				array( $this, 'iphone_icon_callback' ),
				'wpex-favicons',
				'wpex_favicons_main'
			);

			// Ipad
			add_settings_field(
				'wpex_ipad_icon',
				esc_html__( 'Apple iPad Icon ', 'total' ),
				array( $this, 'ipad_icon_callback' ),
				'wpex-favicons',
				'wpex_favicons_main'
			);

			// iPhone Retina
			add_settings_field(
				'wpex_iphone_icon_retina',
				esc_html__( 'Apple iPhone Retina Icon ', 'total' ),
				array( $this, 'iphone_icon_retina_callback' ),
				'wpex-favicons',
				'wpex_favicons_main'
			);

			// iPad Retina
			add_settings_field(
				'wpex_ipad_icon_retina',
				esc_html__( 'Apple iPad Retina Icon ', 'total' ),
				array( $this, 'ipad_icon_retina_callback' ),
				'wpex-favicons',
				'wpex_favicons_main'
			);

		}

		/**
		 * Sanitization callback
		 *
		 * @since 1.6.0
		 */
		public static function sanitize( $options ) {

			// Set all options to theme_mods
			if ( is_array( $options ) && ! empty( $options ) ) {
				foreach ( $options as $key => $value ) {
					if ( ! empty( $value ) ) {
						set_theme_mod( $key, $value );
					} else {
						remove_theme_mod( $key );
					}
				}
			}

			// Set options to nothing since we are storing in the theme mods
			$options = '';
			return;
		}

		/**
		 * Main Settings section callback
		 *
		 * @since 1.6.0
		 */
		public static function section_main_callback() {
			// Leave blank
		}

		/**
		 * Returns correct value for preview
		 *
		 * @since 1.6.0
		 */
		private static function sanitize_val( $val, $instance = 'mod' ) {
			if ( 'image' == $instance && is_numeric( $val ) ) {
				$val = wp_get_attachment_image_src( $val, 'full' );
				$val = $val[0];
			} elseif( is_numeric( $val ) ) {
				$val = absint( $val );
			} else {
				$val = esc_url( $val );
			}
			return $val;
		}

		/**
		 * Fields callback functions
		 *
		 * @since 1.6.0
		 */

		// Favicon
		public function favicon_callback() {
			$val     = wpex_get_mod( 'favicon' );
			$val     = $this->sanitize_val( $val );
			$preview = $this->sanitize_val( $val, 'image' ); ?>
			<input type="text" name="wpex_favicons[favicon]" value="<?php echo esc_attr( $val ); ?>" class="wpex-image-input">
			<input class="wpex-media-upload-button button-secondary" name="login_page_design_bg_img_button" type="button" value="<?php esc_attr_e( 'Upload', 'total' ); ?>" />
			<p class="description">32x32</p>
			<div class="wpex-media-live-preview" data-image-size="32">
				<?php if ( $preview ) { ?>
					<img src="<?php echo esc_url( $preview ); ?>" alt="<?php esc_attr_e( 'Preview Image', 'total' ); ?>" style="width:32px;height:32px;" />
				<?php } ?>
			</div>
		<?php }

		// iPhone
		public function iphone_icon_callback() {
			$val	 = wpex_get_mod( 'iphone_icon' );
			$val     = $this->sanitize_val( $val );
			$preview = $this->sanitize_val( $val, 'image' ); ?>
			<input type="text" name="wpex_favicons[iphone_icon]" value="<?php echo esc_attr( $val ); ?>" class="wpex-image-input">
			<input class="wpex-media-upload-button button-secondary" name="login_page_design_bg_img_button" type="button" value="<?php esc_attr_e( 'Upload', 'total' ); ?>" />
			<p class="description">57x57</p>
			<div class="wpex-media-live-preview" data-image-size="57">
				<?php if ( $preview ) { ?>
					<img src="<?php echo esc_url( $preview ); ?>" alt="<?php esc_attr_e( 'Preview Image', 'total' ); ?>" style="width:57px;height:57px;" />
				<?php } ?>
			</div>
		<?php }


		// iPad
		public function ipad_icon_callback() {
			$val	 = wpex_get_mod( 'ipad_icon' );
			$val     = $this->sanitize_val( $val );
			$preview = $this->sanitize_val( $val, 'image' ); ?>
			<input type="text" name="wpex_favicons[ipad_icon]" value="<?php echo esc_attr( $val ); ?>" class="wpex-image-input">
			<input class="wpex-media-upload-button button-secondary" name="login_page_design_bg_img_button" type="button" value="<?php esc_attr_e( 'Upload', 'total' ); ?>" />
			<p class="description">76x76</p>
			<div class="wpex-media-live-preview" data-image-size="76">
				<?php if ( $preview ) { ?>
					<img src="<?php echo esc_url( $preview ); ?>" alt="<?php esc_attr_e( 'Preview Image', 'total' ); ?>" style="width:76px;height:76px;" />
				<?php } ?>
			</div>
		<?php }

		// iPhone Retina
		public function iphone_icon_retina_callback() {
			$val	 = wpex_get_mod( 'iphone_icon_retina' );
			$val     = $this->sanitize_val( $val );
			$preview = $this->sanitize_val( $val, 'image' ); ?>
			<input type="text" name="wpex_favicons[iphone_icon_retina]" value="<?php echo esc_attr( $val ); ?>" class="wpex-image-input">
			<input class="wpex-media-upload-button button-secondary" name="login_page_design_bg_img_button" type="button" value="<?php esc_attr_e( 'Upload', 'total' ); ?>" />
			<p class="description">120x120</p>
			<div class="wpex-media-live-preview" data-image-size="120">
				<?php if ( $preview ) { ?>
					<img src="<?php echo esc_url( $preview ); ?>" alt="<?php esc_attr_e( 'Preview Image', 'total' ); ?>" style="width:120px;height:120px;" />
				<?php } ?>
			</div>
		<?php }

		// iPad Retina
		public function ipad_icon_retina_callback() {
			$val	 = wpex_get_mod( 'ipad_icon_retina' );
			$val     = $this->sanitize_val( $val );
			$preview = $this->sanitize_val( $val, 'image' ); ?>
			<input type="text" name="wpex_favicons[ipad_icon_retina]" value="<?php echo esc_attr( $val ); ?>" class="wpex-image-input">
			<input class="wpex-media-upload-button button-secondary" name="login_page_design_bg_img_button" type="button" value="<?php esc_attr_e( 'Upload', 'total' ); ?>" />
			<p class="description">152x152</p>
			<div class="wpex-media-live-preview" data-image-size="152">
				<?php if ( $preview ) { ?>
					<img src="<?php echo esc_url( $preview ); ?>" alt="<?php esc_attr_e( 'Preview Image', 'total' ); ?>" style="width:152px;height:152px;" />
				<?php } ?>
			</div>
		<?php }

		/**
		 * Settings page output
		 *
		 * @since 1.6.0
		 */
		public static function create_admin_page() {
			// Remove useless option since we are saving data to theme_mods
			delete_option( 'wpex_favicons' ); ?>
			<div class="wrap">
				<h2 style="padding-right:0;"><?php echo esc_html__( 'Favicons', 'total' ); ?></h2>
				<form method="post" action="options.php">
					<?php settings_fields( 'wpex_favicons' ); ?>
					<?php do_settings_sections( 'wpex-favicons' ); ?>
					<?php submit_button(); ?>
				</form>
			</div><!-- .wrap -->
		<?php }

		/**
		 * Settings page output
		 *
		 * @since 1.6.0
		 */
		public function output_favicons() {

			// Favicon - Standard
			if ( $icon = wpex_get_mod( 'favicon' ) ) {
				echo "\r\n" . '<link rel="shortcut icon" href="'. esc_url( $this->sanitize_val( $icon, 'image' ) ) .'">';
			}

			// Apple iPhone Icon - 57px
			if ( $icon = wpex_get_mod( 'iphone_icon' ) ) {
				echo "\r\n" . '<link rel="apple-touch-icon-precomposed" href="'. esc_url( $this->sanitize_val( $icon, 'image' ) ) .'">';
			}

			// Apple iPad Icon - 76px
			if ( $icon = wpex_get_mod( 'ipad_icon' ) ) {
				echo "\r\n" . '<link rel="apple-touch-icon-precomposed" sizes="76x76" href="'. esc_url( $this->sanitize_val( $icon, 'image' ) ) .'">';
			}

			// Apple iPhone Retina Icon - 120px
			if ( $icon = wpex_get_mod( 'iphone_icon_retina' ) ) {
				echo "\r\n" . '<link rel="apple-touch-icon-precomposed" sizes="120x120" href="'. esc_url( $this->sanitize_val( $icon, 'image' ) ) .'">';
			}

			// Apple iPad Retina Icon - 114px
			if ( $icon = wpex_get_mod( 'ipad_icon_retina' ) ) {
				echo "\r\n" . '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="'. esc_url( $this->sanitize_val( $icon, 'image' ) ) .'">';
			}

		}

	}
}
$wpex_favicons = new WPEX_Favicons();