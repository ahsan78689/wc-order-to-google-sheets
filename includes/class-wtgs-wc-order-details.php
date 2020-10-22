<?php
class Wtgs_WC_Order_Details {
	
	public function __construct() {
		add_action('woocommerce_order_status_processing', array( $this, 'order_details') );
		// add_action('woocommerce_thankyou', array( $this, 'order_details') );
	}

	public function order_details( $order_id ) {
		$order = wc_get_order( $order_id );
		$first_name = $order->get_billing_first_name();
		$last_name = $order->get_billing_last_name();
	    $order_total = $order->get_total();
	    $order_date = $order->get_date_created();

		$timestamp = strtotime( $order_date );
		$date_formated = date( 'M-d-Y', $timestamp );
		$user_id = get_current_user_id();
		$user_info = get_userdata( $user_id );
		$user_name = $user_info->display_name;
		$user_email = $user_info->user_email;

		$data = array(
			"order_id"      => $order_id,
			"customer_name"	=> $first_name .' '. $last_name,
			"order_price"	=> $order_total,
			"order_date"	=> $date_formated
		);

		require plugin_dir_path ( __FILE__ ) . 'class-wtgs-apicall.php';
		$apicall = new Wtgs_APICall();
		$apicall->post( $data );

	}

}