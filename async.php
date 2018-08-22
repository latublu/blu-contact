<?php
/**
 * Blu Contact Form Async Controller
 *
 * 
 */
?>
<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', false);

/** Loads the WordPress Environment and Template */
require( $_SERVER['DOCUMENT_ROOT'] . '/wp-blog-header.php' );

$async = 0;

if ( isset($_POST['async']) ) 
{
	$async = 1;
	
	/**
	Output HTTP Headers
	*/
	
	header('Content-Type: application/json');
	
	status_header(200);
}
else
{

}
?>

<?php
/**
 * Initialize Vars
 */

global $bluContact;

$defaultProcessMessage = "Success. Your info is submitted.";
 
$defaultEmailSubject = 'Contact Form Notification';

$debugThis = (isset($_REQUEST['blu_contact_debug'])) ? $_REQUEST['blu_contact_debug'] : 0;

if ( !$async && $debugThis ) 
{
	echo "Debug on.<br />".PHP_EOL;
} 

global $form;

$form['fieldArr'] = array();
$form['valid'] = 1;
$form['response'] = array();
$form['thePost'] = null;

$valid = 1;

$response = array();

if ( $debugThis ) 
{
	$response['debugArr'] = array();
}

$response['result'] = 0;
$response['resultHtml'] = '';
$response['errors'] = array();
$response['errorHtml'] = '';
$response['processMessage'] = '';

//echo "bluContact->getDefaultFromName: ".$bluContact->getDefaultFromName()."<br />".PHP_EOL;
//echo "bluContact->getDefaultFromAddress: ".$bluContact->getDefaultFromAddress()."<br />".PHP_EOL;

// Posted Form Fields
$form['fieldArr']['blu_contact_ra'] = (isset($_POST['blu_contact_ra'])) ? trim($_POST['blu_contact_ra']) : '';
$form['fieldArr']['cookie'] = (isset($_POST['cookie'])) ? trim($_POST['cookie']) : '';
$form['fieldArr']['ipAddr'] = (isset($_POST['ipAddr'])) ? trim($_POST['ipAddr']) : '';
$form['fieldArr']['firstName'] = (isset($_POST['firstName'])) ? trim($_POST['firstName']) : '';
$form['fieldArr']['lastName'] = (isset($_POST['lastName'])) ? trim($_POST['lastName']) : '';
$form['fieldArr']['name'] = (isset($_POST['name'])) ? trim($_POST['name']) : '';
$form['fieldArr']['email'] = (isset($_POST['email'])) ? trim($_POST['email']) : '';
$form['fieldArr']['url'] = (isset($_POST['url'])) ? trim($_POST['url']) : '';
$form['fieldArr']['phone'] = (isset($_POST['phone'])) ? trim($_POST['phone']) : '';
$form['fieldArr']['country'] = (isset($_POST['country'])) ? trim($_POST['country']) : '';
$form['fieldArr']['age'] = (isset($_POST['age'])) ? trim($_POST['age']) : '';
$form['fieldArr']['note'] = (isset($_POST['note'])) ? trim($_POST['note']) : '';

//echo "fieldArr: <pre>".print_r($form['fieldArr'], true)."</pre>\n";

// Debug Output

if ( $debugThis ) 
{
	$response['debugArr']['_POST'] = $_POST;
	$response['debugArr']['fieldArr'] = $form['fieldArr'];
}

/**
 * Validate
 */

if ( !empty($form['fieldArr']['blu_contact_ra']) ) 
{
	if ( isset($_POST['name']) ) 
	{
		if ( empty($form['fieldArr']['name']) ) 
		{
			$valid = 0;
	
			$response['errors']['name'] = array('code' => 'name_required', 'label' => 'required', 'message' => 'Name required');
		} 
	}
	
	if ( isset($_POST['firstName']) || isset($_POST['lastName']) ) 
	{
		if ( empty($form['fieldArr']['firstName']) ) 
		{
			$valid = 0;
	
			$response['errors']['firstName'] = array('code' => 'firstName_required', 'label' => 'required', 'message' => 'First Name required');
		} 
	
		if ( empty($form['fieldArr']['lastName']) ) 
		{
			$valid = 0;
	
			$response['errors']['lastName'] = array('code' => 'lastName_required', 'label' => 'required', 'message' => 'Last Name required');
		}
	}
	
	if ( isset($_POST['email']) ) 
	{
		if ( empty($form['fieldArr']['email']) ) 
		{
			$valid = 0;
	
			$response['errors']['email'] = array('code' => 'email_required', 'label' => 'required', 'message' => 'Email required');
		} 
	} 
	
	if ( isset($_POST['url']) ) 
	{
		if ( empty($form['fieldArr']['url']) ) 
		{
			$valid = 0;
			
			$response['errors']['url'] = array('code' => 'url_required', 'label' => 'required', 'message' => 'URL required');
		}
	}
	
	$form['valid'] = $valid;
	
	if ( !$valid ) 
	{
		// Output HTML Formatted Error Message
		
		if ( count($response['errors']) ) 
		{
			$response['errorHtml'] .= '<p class="error">'.PHP_EOL;
	
			foreach ($response['errors'] as $e)
			{
				$response['errorHtml'] .= $e['message'].".".PHP_EOL;
			}
	
			$response['errorHtml'] .= '</p>'.PHP_EOL;
		}
	} 
} 

/**
 * Process
 */

if ( !empty($form['fieldArr']['blu_contact_ra']) && $valid ) 
{
	error_reporting(E_ALL & ~E_NOTICE);
	
	// Prep for Output
	
	$postAuthor = 1;
	
	$timeStamp = date('F j, Y H:i:s');
	
	$remoteAddress = $_SERVER['REMOTE_ADDR'];
	
	$requestUri = (isset($_POST['requestUri'])) ? $_POST['requestUri'] : $_SERVER['REQUEST_URI'];
	
	$data = array();
	
	if ( strlen($form['fieldArr']['name']) ) 
	{
		$data['Name'] = $form['fieldArr']['name'];
	} 
	elseif ( strlen($form['fieldArr']['firstName']) || strlen($form['fieldArr']['lastName']) )
	{
		$data['First Name'] = $form['fieldArr']['firstName'];
		$data['Last Name'] = $form['fieldArr']['lastName'];
	}
	
	$data['Email'] = $form['fieldArr']['email'];
	$data['Url'] = $form['fieldArr']['url'];
	$data['Phone'] = $form['fieldArr']['phone'];
	$data['Country'] = $form['fieldArr']['country'];
	$data['Age'] = $form['fieldArr']['age'];
	$data['Note'] = $form['fieldArr']['note'];
	$data['Submitted On'] = $timeStamp;
	$data['Submitted By'] = $remoteAddress;
	$data['Submitted Using'] = $requestUri;
	
	$name = $form['fieldArr']['name'];
	$firstName = $form['fieldArr']['firstName'];
	$lastName = $form['fieldArr']['lastName'];
	$email = $form['fieldArr']['email'];
	$url = $form['fieldArr']['url'];
	$phone = $form['fieldArr']['phone'];
	$country = $form['fieldArr']['country'];
	$age = $form['fieldArr']['age'];
	$note = $form['fieldArr']['note'];
	
	$content = <<<END

Name: $name
Email: $email
URL: $url
Country: $country
Age: $age

Submitted On: $timeStamp
Submitted By: $remoteAddress
Submitted Using: $requestUri
END;

	// Send Email Notification
	
	$sendEmailResult = 0;
	
	if ( $sendEmailNotification = $bluContact->getOption('blucontact_sendEmailNotification') ) 
	{
		$sendNotificationTo = $bluContact->getOption('blucontact_sendNotificationTo', 'ARRAY');
		
		if  ( is_array($sendNotificationTo) && count($sendNotificationTo) )  
		{
			$fromName = '';
			$fromAddress = '';
	
			$emailSubject = $bluContact->getOption('blucontact_emailNotificationSubject');
	
			if ( empty($emailSubject) ) 
			{
				$emailSubject = $defaultEmailSubject;
			} 
	
			$emailBody = stripslashes($content);
			
			$toName = '';
			
			foreach ($sendNotificationTo as $toAddress)
			{
				$sendEmailResult += $bluContact->sendEmail($toName,$toAddress,$fromName,$fromAddress,$emailSubject,$emailBody,'TEXT');
			}
		} 
	} 
	
	if ( $debugThis ) 
	{
		$response['debugArr']['sendEmailResult'] = $sendEmailResult;
	}
	
	if ( strlen($form['fieldArr']['name']) ) 
	{
	    $thePostTitlePrefix = $form['fieldArr']['name'];
	} 
	else 
	{
	    $thePostTitlePrefix = $form['fieldArr']['firstName'].' '.$form['fieldArr']['lastName'];
	}
	
	// Insert Contact Post
	
	if ( $bluContact->useContactPostType ) 
	{
		$thePost = array(
			'post_title' => 'Contact: '.$thePostTitlePrefix.', at '.$timeStamp,
			'post_content' => $content,
			'post_status' => 'publish',
			'post_date' => date('Y-m-d H:i:s'),
			'post_author' => $postAuthor,
			'post_type' => 'contact',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
	
		if ( $debugThis ) 
		{
			$response['debugArr']['thePost'] = $thePost;
		}

		$thePostId = wp_insert_post($thePost, $wpError);
	
		if ( $debugThis ) 
		{
			$response['debugArr']['thePostId'] = $thePostId;
		}
	
		if ( $thePostId ) 
		{
			$thePostMetaId = add_post_meta($thePostId, 'data', json_encode($data));
		
			$response['debugArr']['thePostMetaId'] = $thePostMetaId;
		} 
	} 
	
	// Insert Contact Record
	
	$bluContactRecordInserted = 0;
	
	$fieldValues = array( 
			'contact_post_ID' => 0, 
			'contact_name' => $name, 
			'contact_firstName' => $firstName, 
			'contact_lastName' => $lastName, 
			'contact_email' => $email, 
			'contact_url' => $url, 
			'contact_phone' => $phone, 
			'contact_country' => $country, 
			'contact_age' => $age, 
			'contact_note' => $note, 
			'contact_remoteAddress' => $remoteAddress, 
			'contact_requestUri' => $requestUri, 
		   );

	if ( $bluContact->insertContact($fieldValues) ) 
	{
		$bluContactRecordInserted = 1;
		
		$response['debugArr']['contactInserted'] = true;
	}
	
	// Results
	
	if ( $sendEmailResult || $thePostId || $bluContactRecordInserted ) 
	{
		$response['result'] = 1;
		
		$processMessage = $bluContact->getOption('blucontact_formSuccessMessage');
	
		if ( empty($processMessage) ) 
		{
			$processMessage = $defaultProcessMessage;
		} 
		
		$response['processMessage'] = $processMessage; 
		
		$response['resultHtml'] = '<h4>'.$response['processMessage'].'</h4>'.PHP_EOL;
		
		// Clear Field Array
		
		$form['fieldArr'] = array();
	}

} 

?>

<?php
/**
 * Response, Json Encoding & Output
 */
 
$form['response'] = $response;

if ( $debugThis ) 
{
	
}

if ( isset($_POST['async']) ) 
{
	$response_json = json_encode($response);

	echo $response_json;
}
?>
