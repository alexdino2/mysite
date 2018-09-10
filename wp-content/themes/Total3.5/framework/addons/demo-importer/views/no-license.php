<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="wrap wpex-demo-import-wrap">

	<h1><?php esc_html_e( 'Demo Importer', 'total' ); ?></h1>
	
	<div class="notice notice-error">
		<p style="font-size:1.1em"><?php esc_html_e( 'You must activate your theme license to access demos.', 'total' ); ?></p>
		<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=wpex-panel-theme-license' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Activate your license', 'total' ); ?></a></p>
	</div>

</div>