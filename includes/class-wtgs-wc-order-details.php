<?php
class WTGS_WC_Order_Details {

	public function __construct() {
		/**
		 * Hook for WooCommerce Order Process
		 */
		add_action('woocommerce_checkout_order_processed', array( $this, 'process_wc_order') );

		/**
		 * Hook for testing data
		 */
		// add_action('woocommerce_thankyou', array( $this, 'process_wc_order') );
	}

	public function process_wc_order($order_id) {
		// Fetches the order data based on $order_id
		$data = $this->order_data($order_id);

		// Sends order data to Google Sheets
		$this->send_order_data($data);
	}

	/**
	 * Fetches WC Order data
	 *
	 * @param Integer $order_id ID of the WC Order
	 * @return Array $data WC Order details
	 */
	public function order_data( $order_id ) {
		$order = wc_get_order( $order_id );

		// Get order basic information
		$first_name = $order->get_billing_first_name();
		$last_name = $order->get_billing_last_name();
	    $order_total = $order->get_total();
	    $order_date = $order->get_date_created();
		$timestamp = strtotime( $order_date );
		$date_formated = date( 'M-d-Y', $timestamp );


		// Get admin email
		$admin_email = get_option( 'admin_email' );

		$data = array(
			"order_id"      => $order_id,
			"customer_name"	=> $first_name .' '. $last_name,
			"order_price"	=> $order_total,
			"order_date"	=> $date_formated
		);

		return $data;
	}

	/**
	 * Sends order data to Google Sheets
	 */
	public function send_order_data($data) {
		$apicall = new WTGS_APICall();
		$apicall->post( $data );
	}
}