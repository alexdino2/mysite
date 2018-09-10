<?php
/*
 * Extends core PMS_Submenu_Page base class to create and add custom functionality
 * for the payments section in the admin section
 *
 */
Class PMS_Submenu_Page_Payments extends PMS_Submenu_Page {


    /*
     * Method that initializes the class
     *
     */
    public function init() {

        // Hook the output method to the parent's class action for output instead of overwriting the
        // output method
        add_action( 'pms_output_content_submenu_page_' . $this->menu_slug, array( $this, 'output' ) );

        // Process different actions within the page
        add_action( 'init', array( $this, 'process_data' ) );

    }


    /*
     * Method that processes data on payment admin pages
     *
     */
    public function process_data() {

        // Verify correct nonce
        if( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'pms_payment_nonce' ) )
            return;

        // Get current actions
        $action = !empty( $_REQUEST['pms-action'] ) ? $_REQUEST['pms-action'] : '';

        if( empty($action) )
            return;


        if( $action == 'delete_payment' ) {

            // Get payment id
            $payment_id = ( !empty( $_REQUEST['payment_id'] ) ? (int)$_REQUEST['payment_id'] : 0 );

            $payment = pms_get_payment( $payment_id );

            if( $payment->remove() )
                $this->add_admin_notice( __( 'Payment successfully deleted.', 'paid-member-subscriptions' ), 'updated' );

        }

    }


    /*
     * Method to output content in the custom page
     *
     */
    public function output() {

        include_once 'views/view-page-payments-list-table.php';

    }


	/*
     * Method that adds Screen Options to Payments page
     *
     */
	public function add_screen_options() {

		$args = array(
			'label' => 'Payments per page',
			'default' => 10,
			'option' => 'pms_payments_per_page'
		);

		add_screen_option( 'per_page', $args );

	}

}

$pms_submenu_page_payments = new PMS_Submenu_Page_Payments( 'paid-member-subscriptions', __( 'Payments', 'paid-member-subscriptions' ), __( 'Payments', 'paid-member-subscriptions' ), 'manage_options', 'pms-payments-page', 20 );
$pms_submenu_page_payments->init();