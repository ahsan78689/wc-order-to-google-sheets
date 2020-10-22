<?php
class WTGS_APICall {
	private $method;
	private $url;
	private $data;

	public function __construct() {
		$this->method = 'POST';
		$this->url = 'https://hooks.zapier.com/hooks/catch/1954644/oq4cdg7/';
	}

	private function call( $method, $url, $data ){
		if (!$data) {
			return false;
		}

		// $ssl_certi = 'C:\Users\MSI\Local Sites\uptekdemotest\cacert.pem';
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://hooks.zapier.com/hooks/catch/1954644/oq4cdg7",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $data,
			// CURLOPT_CAINFO, $ssl_certi,
			// CURLOPT_CAPATH, $ssl_certi,
			CURLOPT_HTTPHEADER => array(
			"Content-Type: application/x-www-form-urlencoded"
			),
		));

		$result = curl_exec($curl);
		if($result === false) {
		    echo 'Curl error: ' . curl_error($curl);
		} else {
		    echo 'Operation completed without any errors';
		}
		curl_close($curl);
		return $result;
	}

	public function post( $data ){
		$make_api_call = $this->call($this->method, $this->url, json_encode( $data ));
		echo $make_api_call;
	}

}