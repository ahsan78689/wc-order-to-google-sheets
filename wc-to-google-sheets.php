<?php
/**
Plugin Name: WC to Google Sheets
Plugin URI: https://uptek.com
Description: A simple plugin to generate a new row in Google Sheets when a new order is placed.
Version: 1.0.0
Author: Ahsan Asif
Text Domain: wtgs
*/

// if ( ! defined( 'WPINC' ) ){
// 	die;
// }

// require plugin_dir_path( __FILE__ ) . 'includes/class-wtgs-wc-order-details.php';
// require plugin_dir_path ( __FILE__ ) . 'includes/class-wtgs-apicall.php';

// require plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

// $order_data = new WTGS_WC_Order_Details();

require __DIR__ . '/vendor/autoload.php';

// if (php_sapi_name() != 'cli') {
//     throw new Exception('This application must be run on the command line.');
// }

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAuthConfig( __DIR__ . '/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $client->setApprovalPrompt("consent");
    $client->setApprovalPrompt('force');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'tokens.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


// Get the API client and construct the service object.
// $client = getClient();
// $service = new Google_Service_Sheets($client);

// // Prints the Order id, Customer Name, Order Total and Order Date in the spreadsheet:
// // https://docs.google.com/spreadsheets/d/1VJCAxvw6k6H_YwQNdTBF4jiaFGjM8x77WtNLSmWnwrg/edit
// $spreadsheetId = '1VJCAxvw6k6H_YwQNdTBF4jiaFGjM8x77WtNLSmWnwrg';
// $range = 'Sheet1!A2:D';
// $response = $service->spreadsheets_values->get($spreadsheetId, $range);
// $values = $response->getValues();

// if (empty($values)) {
//     print "No data found.\n";
// } else {
//     print "Name, Major:\n";
//     foreach ($values as $row) {
//         // Print columns A and E, which correspond to indices 0 and 4.
//         printf("%s, %s\n", $row[0], $row[4]);
//     }
// }

require plugin_dir_path( __FILE__ ) . 'includes/class-wtgs-google-sheets-API-call.php';

$googleSheet = new WTGS_Google_Sheets_API_Call();
$authCode = $googleSheet->wtgs_getAuthCode();
// Needs to create a menu page where there is an authcode generation link
// header('Location: ' . filter_var($authCode, FILTER_SANITIZE_URL));


$client = new Google_client();
// $client->authenticate($_GET['code']);
// $accessToken = $client->getAccessToken();
// $accessToken = $googleSheet->wtgs_verifyAuthCode($code);
// echo $accessToken;

$token = $googleSheet->wtgs_getAccessToken($code);

// echo $code;
// $access_token = $client->getAccessToken();
// print_r($token);

if (!$token) {
	echo 'AuthCode Error';
} else {

}

