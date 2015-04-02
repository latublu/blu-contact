<?php
/**
 * Blu Contact Form Proto Controller
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

/**
 * Initialize Vars
 */

error_reporting(E_ALL & ~E_NOTICE);

global $bluContact;

$bluContact->setDebug(true);

$results = null;

/**
 * Tests
 */
 
echo "Proto Tests<br />".PHP_EOL;

/**
 * Insert Contact Record
 */

$r = rand(1111, 9999);

$contact_name = 'Foo Bar'.$r;

$contact_email = 'foo.bar.'.$r.'@this.com';
$contact_url = 'this.com';
$contact_phone = '555-555-1212';
$contact_country = 'United States'; 
$contact_age = '99';
$contact_note = 'This is a test '.$r.'.';


$fieldValues = array( 
		'contact_post_ID' => 0, 
		'contact_name' => $contact_name, 
		'contact_firstName' => $contact_firstName, 
		'contact_lastName' => $contact_lastName, 
		'contact_email' => $contact_email, 
		'contact_url' => $contact_url, 
		'contact_phone' => $contact_phone, 
		'contact_country' => $contact_country, 
		'contact_age' => $contact_age, 
		'contact_note' => $contact_note, 
		'contact_remoteAddress' => $contact_remoteAddress, 
		'contact_requestUri' => $contact_requestUri, 
 		'contact_foo' => 'bar', 
       );

if ( $bluContact->insertContact($fieldValues) ) 
{
    $results++;
}

echo "results: ".$results."<br />".PHP_EOL;

?>


