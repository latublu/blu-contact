<?php
/**
 * BluContact Async Form Controller
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
?>

<?php
/**
 * Initialize Vars
 */

global $bluContact;

global $wpdb;

$bluDebugThis = isset($_REQUEST['bluDebugThis']) ? $_REQUEST['bluDebugThis'] : false;

if ( $bluDebugThis ) 
{
    $bluContact->setDebug(true);
} 

/**
 * Export
 */
				
$bluContact->exportAllContactRecords();

?>
