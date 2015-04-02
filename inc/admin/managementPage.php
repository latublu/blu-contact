<div style="clear: both;"></div>

<?php
global $bluContact;

$formAction = $_SERVER['REQUEST_URI'];
$formAction = '/wp-content/plugins/blu-contact/export.php';

$error = 0;
$errorMessage = '';
$processed = 0;
$processedMessage = '';

//$bluContact->setDebug(1);

$blucontact_debug = $bluContact->getOption('blucontact_debug');

//echo "<br/>_REQUEST: <pre>".print_r($_REQUEST, true)."</pre>";

if ( @$_POST['bluContactAction'] == 'export'  ) 
{
	echo "<br/>_POST: <pre>".print_r($_POST, true)."</pre>";
	
	// Validate
        
	
	// Process
	
	if ( !$error ) 
	{
		global $wpdb;
		
		$prefix = $wpdb->prefix;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		$contactTableName = $wpdb->prefix.'blu_contact';
		
		$startingId = 1;
		
		$results = $wpdb->get_results( $wpdb->prepare( 
			"SELECT      *
			FROM        $contactTableName blu_contact
			WHERE       blu_contact.contact_ID >= %s 
			ORDER BY    blu_contact.contact_post_ID
			",
			$startingId
			), 
			'ARRAY_A'
		); 
				
		if ( empty($results) ) 
		{
			echo "no results<br />".PHP_EOL;
		}
		else
		{
			$fieldNames = array_keys($results[0]);
			
			echo "fieldNames: <pre>".print_r($fieldNames, true)."</pre>";
			
			echo "results: <pre>".print_r($results, true)."</pre>";
		}
    	    
	}
}

?>

<div class="wrap">
	
	<form method="post" action="<?php echo $formAction; ?>">
	
		<h2>
			<?php echo $bluContact->pluginShortName; ?> Export
		</h2>
		
		<?php
		if ( $error )
		{
		?>
			<div id="settings-error" class="error"> 
				<p>
					<strong><?php echo $errorMessage; ?></strong>
				</p>
			</div>
		<?php
		}
		elseif ( $processed )
		{
		?>
			<div id="settings-updated" class="updated"> 
				<p>
					<strong><?php echo $processedMessage; ?></strong>
				</p>
			</div>
		<?php
		}
		?>
		
		<input type="hidden" name="bluContactAction" value="export" />
		
		<?php
		if ( isset($_REQUEST['bluDebugThis']) ) 
		{
		?>
			<input type="hidden" name="bluDebugThis" value="1" />
		<?php
		}
		?>

		<div class="submit">
			<input id="exportBluContact" name="exportBluContact" class="button button-primary" type="submit" value="<?php _e('Export', 'BluContact') ?>" />
		</div>
	
	</form>
	
 </div>
