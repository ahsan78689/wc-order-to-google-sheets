<?php
/**
Plugin Name: WC to Google Sheets
Plugin URI: https://uptek.com
Description: A simple plugin to generate a new row in google sheets when a new order is received.
Version: 1.0.0
Author: Ahsan
Text Domain: wtgs
*/

if ( ! defined( 'WPINC' ) ){
	die;
}

require plugin_dir_path( __FILE__ ) . 'includes/class-wtgs-wc-order-details.php';
$order_data = new Wtgs_WC_Order_Details();

// require_once plugin_dir_path ( __FILE__ ) . 'includes/class-wtgs-apicall.php';
// $zapier = new Wtgs_APICall();
