<?php
class WTGS_Google_Sheets_API_Call {

	private $client;
	function __construct() {
		$this->client = new Google_client();
		$this->client->setApplicationName('WC to GS');
		$this->client->setScopes( array('https://www.googleapis.com/auth/drive.readonly', 'https://www.googleapis.com/auth/spreadsheets') );
		// $this->client->setScopes( Google_Service_Sheets::SPREADSHEETS_READONLY );
		$fileUrl = __DIR__ . '/credentials.json';
		$this->client->setAuthConfig($fileUrl);
		$this->client->setAccessType('offline');
		$this->client->setPrompt('select_account consent');
		$this->client->setApprovalPrompt('force');

	}

	function wtgs_getAuthCode() {
		$authUrl = $this->client->createAuthUrl();
		return $authUrl;
	}

	function wtgs_getAccessToken($authCode) {
		try {
			$tokenPath = __DIR__ . '/token.json';
			if ( file_exists($tokenPath) ) {
				$accessToken = json_decode(file_get_contents($tokenPath), true);
				echo $accessToken;
				$this->client->setAccessToken($accessToken);

				} else {
				// $this->client->authenticate($_GET['code']);
				// file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
				// return false;
				}
				if ($this->client->isAccessTokenExpired()) {
					// Refresh the token if possible, else fetch a new one.
					if ($this->client->getRefreshToken()) {
						$this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
					} else {
						return false;
					}

					if ( file_exists(dirname($tokenPath)) ) {
						unlink($tokenPath);
					}
					// Save the token to a file.
					if (!file_exists(dirname($tokenPath))) {
						mkdir(dirname($tokenPath), 0700, true);
					}
					file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
				}
			

			// echo $tokenPath;
			file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
		} catch (Exception $e) {
			return false;
		}

		return $accessToken['access_token'];
	}

	function wtgs_verifyAuthCode($authCode) {
		try {
			$tokenPath = __DIR__ . '/token.json';
			$accessToken = $this->client->fetchAccessTokenWithAuthCode(trim($authCode));
			$this->client->setAccessToken($accessToken);

			// Check to see if there was an error.
			if (array_key_exists('error', $accessToken) ) {
				echo "false";
				return false;
			}

			if (!file_exists(dirname($tokenPath))) {
				mkdir(dirname($tokenPath), 0700, true);
			}
			file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
		} catch (Exception $e) {
			echo $e;
			return false;
		}

		return $accessToken;
	}

	function wtgs_getClientObject() {
		$tokenPath = __DIR__ . '/token.json';
		if( file_exists($tokenPath) ){
			// echo "1";
			$accessToken = json_decode(file_get_contents($tokenPath), true);
			// echo "2";
			$this->client->setAccessToken($accessToken);
			// echo "3";
		} else {
			// echo "4";
			return false;
		}

		// if there is no previous token or it's expired.
		if ($this->client->isAccessTokenExpired()) {
			// echo "5";
			// Refresh the token if possible, else fetch a new one.
			if ($this->client->getRefreshToken()) {
				// echo "6";
				$this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
				// echo "7";
			} else {
				// echo "8";
				return false;
				// echo "9";
			}
			// Save the token to a file.
			if (!file_exists(dirname($tokenPath))) {
				// echo "10";
				mkdir(dirname($tokenPath), 0700, true);
				// echo "11";
			}
			// echo "12";
			file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
			// echo "13";
		}
		// echo "14";
		return $this->client;
	}

	function wtgs_createSpreadsheet($name=false) {

	}

	function wtgs_updateSpreadSheetData($spreadsheetId, $range, $values, $valueInputOption="USER_ENTERED") {
		// $spreadsheetId = '1VJCAxvw6k6H_YwQNdTBF4jiaFGjM8x77WtNLSmWnwrg';
		// $valueInputOption = "RAW";
		// $range = 'Sheet1!A2:D';
		// $values = [
		// 	[
		// 		"column 1", "column 2", "column 3"
		// 	],
		// 	[
		// 		"value 1", "value 2", "value 3"
		// 	]
		// ];
		$client = $this->wtgs_getClientObject();

		if ($client) {
			$service = new Google_Service_Sheets($client);
			$body = new Google_Service_Sheets_ValueRange(['values' => $values ]);
			$params = [
				'valueInputOption' => $valueInputOption
			];

			$result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
			return $result->getUpdatedRange();
		}

	}

	function wtgs_appendSpreadSheetData($spreadsheetId, $values, $valueInputOption="RAW", $range='SHEET1') {
		$client = $this->wtgs_getClientObject();

		if ($client) {
			$service = new Google_Service_Sheets($client);

			// ID of the SpreadSheet to update.

			// A1 notation of a range to search for a logical table of data.
			// Values will be appended after the last row of the table.

			$params = [
				'valueInputOption' => $valueInputOption
			];

			// Assign values to desired properties of 'requestBody':
			$requestBody = new Google_Service_Sheets_ValueRange([
				'values' => $values
			]);

			$response = $service->spreadsheets_values->append($spreadsheedId, $range, $requestBody, $params);

			// Change code below to process the 'response' object:
			return $response->getUpdate()->getUpdateRange();
		}
	}


	function wtgs_deleteSpreadSheetData($spreadsheedId, $range=false) {
		$client = $this->wtgs_getClientObject();
		$service = new Google_Service_Sheets($client);

		$deleteOperation = array(
			'range' => array(
				// The very first sheet on worksheet
				'sheedId' => 0,
				'dimension' => 'ROWS',
				'startIndex' => 0,
				'endIndex' => (0)

			)
		);

		$deletableRow[] = new Google_Service_Sheets_Request(array('deleteDimension' => $deleteOperation));
		// Assign values to desired properties of 'requestBody':
		$requestBody = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(
			[
				"requests" => $deletableRow
			]
		);

		$response = $service->spreadsheets->bathcUpdate($spreadsheetId, $requestBody);
		echo '<pre>', var_export($response, true), '</pre>', "\n";
	}

	function wtgs_clearSpreadSheetData($spreadsheetId, $range) {
		$client = $this->wtgs_getClientObject();
		$service = new Google_Service_Sheets($client);

		$requestBody = new Google_Service_Sheets_ClearValuesRequest();

		$response = $service->spreadsheets_values->clear($spreadsheetId, $range, $requestBody);

		echo '<pre>', var_export($response, true), '</pre>', "\n";

	}


}