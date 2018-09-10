<?php
/**
 * Theme License Activation and De-activation
 *
 * @package Total WordPress theme
 * @subpackage Framework
 * @version 4.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Can be used to hide the license panel
// Disabling the license panel will not give you the benefits of having an active license
// The purpose is if you network activate your license on a multisite you can use this
// conditional to hide the panel on subsites if wanted.
if ( ! apply_filters( 'wpex_show_license_panel', true ) ) {
	return;
}

// Start Class
class WPEX_Theme_License {

	protected $site_url;

	/**
	 * Start things up
	 *
	 * @since 4.5
	 */
	public function __construct() {
		$this->site_url = $this->get_site_url();
		add_action( 'admin_menu', array( $this, 'add_theme_license_page' ) );
		add_action( 'wp_ajax_wpex_theme_license_form', array( $this, 'license_form_ajax' ) );
	}

	/**
	 * Add sub menu page
	 *
	 * @since 4.5
	 */
	public function get_site_url() {
		return rawurlencode( trim( site_url() ) );
	}

	/**
	 * Add sub menu page
	 *
	 * @since 4.5
	 */
	public function add_theme_license_page() {
		add_submenu_page(
			WPEX_THEME_PANEL_SLUG,
			esc_html__( 'Theme License', 'total' ),
			esc_html__( 'Theme License', 'total' ),
			'administrator',
			WPEX_THEME_PANEL_SLUG .'-theme-license',
			array( $this, 'theme_license_page' )
		);
	}

	/**
	 * Settings page output
	 *
	 * @since 4.5
	 */
	public function theme_license_page() {

		$license = wpex_get_theme_license(); ?>

		<div id="wpex-admin-page" class="wrap wpex-theme-license-page">
			
			<h1><?php esc_html_e( 'Theme License', 'total' ); ?></h1>
	
			<?php if ( $license ) { ?>
				<div class="wpex-admin-ajax-notice notice updated">
					<p><?php esc_html_e( 'Congratulations. Your theme license is active.', 'total' ); ?></p>
				</div>
			<?php } else { ?>
				<div class="wpex-admin-ajax-notice notice"></div>
			<?php } ?>
			
			<div class="wpex-theme-license-box wpex-boxed-shadow">

				<h2><?php esc_html_e( 'Verify your License', 'total' ); ?></h2>
				
				<p class="wpex-top-note"><?php echo wp_kses_post( __( 'Enter your purchase code below and click the activate button or hit enter. You can learn how to find your purchase code <a target="_blank" href="https://www.wpexplorer-themes.com/envato-purchase-code/">here</a>.', 'total' ) ); ?></p>

				<form method="post" id="wpex-theme-license-form">
					
					<?php if ( $license ) { ?>
			
						<input type="text" id="wpex_license" name="license" placeholder="<?php echo esc_attr( $license ); ?>" value="<?php echo esc_attr( $license ); ?>" readonly="readonly" autocomplete="off" />

					<?php } else { ?>
							
						<input type="text" id="wpex_license" name="license" placeholder="<?php esc_html_e( 'Enter your purchase code here.', 'total' ); ?>" autocomplete="off" />

					<?php } ?>
					
					<?php wp_nonce_field( 'wpex_theme_license_form_nonce', 'wpex_theme_license_form_nonce' ); ?>

					<p class="submit">

						<?php
						$submit_classes = 'primary button-hero ';
						$submit_classes .= $license ? 'deactivate' : 'activate';
						$activate_txt   = __( 'Activate your license', 'total' );
						$deactivate_txt = __( 'Deactivate your license', 'total' );
						submit_button(
							$license ? $deactivate_txt : $activate_txt,
							$submit_classes,
							'submit',
							false,
							array(
								'data-activate'   => $activate_txt,
								'data-deactivate' => $deactivate_txt,
							)
						); ?>
						
						<img src="<?php echo get_admin_url() ?>/images/spinner.gif" class="wpex-spinner" width="20" height="20" alt="<?php esc_html( 'Spinner', 'total' ); ?>" />

					</p>

				</form>
				
				<p class="description"><?php echo wp_kses_post( __( 'A purchase code (license) is only valid for a <strong>Single Domain</strong>. Are you already using this theme on another domain? Purchase <a target="_blank" href="https://themeforest.net/item/total-responsive-multipurpose-wordpress-theme/6339019?ref=WPExplorer&license=regular&open_purchase_for_item_id=6339019">new license here</a> to get your new purchase code. If you are running a multi-site network you only need to activate your license on the main site.', 'total' ) ); ?></p>

			</div><!-- .wpex-theme-license-box -->
			
		</div><!-- .wrap -->

	<?php }

	/**
	 * Activate License
	 *
	 * @since 4.5
	 */
	public function activate_license( $license, $response ) {
		$remote_url = add_query_arg( array(
			'market'     => 'envato',
			'product_id' => '6339019',
			'license'    => $license,
			'url'        => $this->site_url,
		), 'https://wpexplorer-themes.com/activate-license/' );
		$remote_response = wp_remote_get( $remote_url );
		if ( is_wp_error( $remote_response ) ) {
			$response['message'] = $response->get_error_message();
		} else {
			$remote_response_code = wp_remote_retrieve_response_code( $remote_response );
			$response['response_code'] = $remote_response_code;
			if ( 200 == $remote_response_code ) {
				$result = json_decode( wp_remote_retrieve_body( $remote_response ) );
				if ( 'active' == $result->status ) {
					$response['success'] = true;
					$response['message'] = esc_html__( 'Congratulations. Your theme license is active.', 'total' );
					$response['messageClass'] = 'updated';
					update_option( 'active_theme_license', $license );
				} else {
					if ( 'invalid' == $result->status ) {
						$response['message'] = esc_html__( 'This license code is not valid.', 'total' );
					} elseif ( 'duplicate' == $result->status ) {
						$response['message'] = esc_html__( 'This license is already in use.', 'total' );
					} elseif ( isset( $result->error ) ) {
						$response['message'] = $result['error'];
					}
					$response['messageClass'] = 'notice-error';
				}
			} else {
				$response['message'] = esc_html( 'Can not connect to the verification server at this time. Please make sure outgoing connections are enabled on your server and try again. If it still does not work please wait a few minutes and try again.', 'total' );
			}
		}
		return $response;
	}

	/**
	 * Deactivate License
	 *
	 * @since 4.5
	 */
	public function deactivate_license( $license, $response ) {
		$remote_url = add_query_arg( array(
			'market'  => 'envato',
			'license' => $license,
			'url'     => $this->site_url,
		), 'https://wpexplorer-themes.com/deactivate-license/' );
		$remote_response = wp_remote_get( $remote_url );
		if ( is_wp_error( $remote_response ) ) {
			$response['message'] = $response->get_error_message();
		} else {
			$remote_response_code = wp_remote_retrieve_response_code( $remote_response );
			$response['response_code'] = $remote_response_code;
			if ( 200 == $remote_response_code ) {
				$result = json_decode( wp_remote_retrieve_body( $remote_response ) );
				if ( 'success' == $result->status ) {
					delete_option( 'active_theme_license', false );
					$response['message'] = esc_html__( 'The license has been deactivated successfully.', 'total' );
					$response['messageClass'] = 'notice-warning';
					$response['success'] = true;
				} elseif ( $result->message ) {
					$response['message'] = $result->message;
				} else {
					$response['message'] = $result;
				}
			} else {
				$response['message'] = esc_html( 'Can not connect to the verification server at this time, please try again in a few minutes.', 'total' );
			}
		}
		return $response;
	}

	/**
	 * License form ajax
	 *
	 * @since 4.5
	 */
	public function license_form_ajax() {

		if ( ! wp_verify_nonce( $_POST['nonce'], 'wpex_theme_license_form_nonce' ) ) {
			die();
		}

		$response = array(
			'message'       => '',
			'messageClass'  => 'notice-error',
			'success'       => false,
			'response_code' => '',
		);
		$license = isset( $_POST['license'] ) ? wp_strip_all_tags( trim( $_POST['license'] ) ) : '';
		$process = isset( $_POST['process'] ) ? $_POST['process'] : '';

		if ( 'deactivate' == $process ) {
			$response = $this->deactivate_license( $license, $response );
			wp_send_json( $response );
		}

		elseif ( 'activate' == $process ) {

			if ( empty( $license ) ) {
				$response['message']      = esc_html__( 'Please enter a license.', 'total' );
				$response['messageClass'] = 'notice-warning';
			} else {
				$response = $this->activate_license( $license, $response );
			}

			wp_send_json( $response );

		}

		die();

	}

}
new WPEX_Theme_License();