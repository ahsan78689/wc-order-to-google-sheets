<?php
class Wtgs_WC_Order_Data {
	
	public function __construct() {
		add_action('woocommerce_order_status_processing', array( $this, 'email_order_details') );
	}

	public function email_order_details( $order_id ) {
		$order = wc_get_order( $order_id );
		$first_name = $order->get_billing_first_name();
		$last_name = $order->get_billing_last_name();
	    $order_total = $order->get_formatted_order_total();
	    $order_date = $order->get_date_created();

		$timestamp = strtotime( $order_date );
		$date_formated = date( 'M-d-Y', $timestamp );
		$user_id = get_current_user_id();
		$user_info = get_userdata( $user_id );
		$user_name = $user_info->display_name;
		$user_email = $user_info->user_email;

		$this->send_email($user_email,$order_id, $first_name,$last_name, $order_total, $date_formated);
	}

	private function send_email( $user_email, $order_id, $first_name, $last_name, $order_total, $date ) {
		$to = $user_email;
		$subject = 'Order Details of order id: '.$order_id;
		$body = "Order ID: {$order_id}<br>Customer Name: {$first_name} {$last_name}<br>Total Order Price: {$order_total}<br>Order Date: {$date}";
		$headers = array('Content-Type: text/html; charset=UTF-8');
		 
		wp_mail( $to, $subject, $body, $headers );
	}
}