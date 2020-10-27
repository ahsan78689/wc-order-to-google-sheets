<?php
class Wtgs_WC_Order_Data {
	
	public function __construct() {
		/*
		* Hook for WooCommerce Order Process
		*/
		add_action('woocommerce_checkout_order_processed', array( $this, 'email_order_details') );
	}

	/**
	* Fetched WC Order data
	*
	* @param Integer $order_id ID of the WC Order
	*/
	public function email_order_details( $order_id ) {
		// Fetches the order data based on Order_id
		$order = wc_get_order( $order_id );

		// Get order information
		$first_name = $order->get_billing_first_name();
		$last_name = $order->get_billing_last_name();
	    $order_total = $order->get_formatted_order_total();
	    $order_date = $order->get_date_created();
		$timestamp = strtotime( $order_date );
		$date_formated = date( 'M-d-Y', $timestamp );

		// Get admin email
		$admin_email = get_option( 'admin_email' );

		$this->send_email($admin_email,$order_id, $first_name,$last_name, $order_total, $date_formated);
	}

	private function send_email( $admin_email, $order_id, $first_name, $last_name, $order_total, $date ) {
		$to = $admin_email;
		$subject = 'Order Details of order id: '.$order_id;
		$body = "Order ID: {$order_id}<br>Customer Name: {$first_name} {$last_name}<br>Total Order Price: {$order_total}<br>Order Date: {$date}";
		$headers = array('Content-Type: text/html; charset=UTF-8');
		 
		wp_mail( $to, $subject, $body, $headers );
	}
}