<div style="clear: both;"></div>

<?php
global $bluContact;

$error = 0;
$errorMessage = '';
$updated = 0;
$updatedMessage = '';

//$bluContact->setDebug(1);

if ( @$_POST['updateBluContactAction'] == 'update'  ) 
{
	//echo "<br/>_POST: <pre>".print_r($_POST, true)."</pre>";
	
	// Validate
        
	if ( $error ) 
	{
		$errorMessage = 'Error updating settings - ' . $errorMessage;
	} 
	
	// Process
	
	if ( !$error && function_exists('update_option') ) 
	{
		if ( isset($_POST['blucontact_debug']) && $bluContact->setOption('blucontact_debug', $_POST['blucontact_debug']) ) 
		{
        	$updated++;
		} 
		
		if ( isset($_POST['blucontact_formSuccessMessage']) && $bluContact->setOption('blucontact_formSuccessMessage', $_POST['blucontact_formSuccessMessage']) ) 
		{
        	$updated++;
		} 
		
		if ( isset($_POST['blucontact_sendEmailNotification']) && $bluContact->setOption('blucontact_sendEmailNotification', $_POST['blucontact_sendEmailNotification']) ) 
		{
        	$updated++;
		} 
		
		if ( isset($_POST['blucontact_sendNotificationTo']) && $bluContact->setOption('blucontact_sendNotificationTo', $_POST['blucontact_sendNotificationTo']) ) 
		{
        	$updated++;
		} 
		
		if ( isset($_POST['blucontact_emailNotificationSubject']) && $bluContact->setOption('blucontact_emailNotificationSubject', $_POST['blucontact_emailNotificationSubject']) ) 
		{
        	$updated++;
		} 
        
        if ( $updated ) 
        {
        	$updatedMessage = 'Settings updated successfully.';
        } 
               
	}
}

$blucontact_debug = $bluContact->getOption('blucontact_debug');

$blucontact_formSuccessMessage = $bluContact->getOption('blucontact_formSuccessMessage');

$blucontact_sendEmailNotification = $bluContact->getOption('blucontact_sendEmailNotification');

$blucontact_sendNotificationTo = $bluContact->getOption('blucontact_sendNotificationTo');

//$blucontact_sendNotificationTo_array = $bluContact->getOption('blucontact_sendNotificationTo', 'ARRAY');
//echo "<br/>blucontact_sendNotificationTo_array: <pre>".print_r($blucontact_sendNotificationTo_array, true)."</pre>";

$blucontact_emailNotificationSubject = $bluContact->getOption('blucontact_emailNotificationSubject');

?>

<div class="wrap">
	
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	
		<h2>
			<?php echo $bluContact->pluginShortName; ?> Settings
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
		elseif ( $updated )
		{
		?>
			<div id="settings-updated" class="updated"> 
				<p>
					<strong><?php echo $updatedMessage; ?></strong>
				</p>
			</div>
		<?php
		}
		?>
		
		<input type="hidden" name="updateBluContactAction" value="update" />
		
		<table class="form-table">
			<tbody>
				
				<tr>
				<th scope="row">
					<h3>Form</h3>
				</th>
				<td>
				<td>
				</tr>
				
				<tr>
				<th scope="row">
					<label for="blucontact_emailNotificationSubject">Successful Submission Message</label>
				</th>
				<td>
					
					<input type="text" id="blucontact_formSuccessMessage" name="blucontact_formSuccessMessage" size="50" maxlength="100" value="<?php echo $blucontact_formSuccessMessage; ?>" />
					
					<p class="description">
					Message displayed when form submitted successfully.
					</p>
					
				</td>
				</tr>
				
				<tr>
				<th scope="row">
					<h3>Email Notification</h3>
				</th>
				<td>
				<td>
				</tr>
				
				<tr>
				<th scope="row">
					<label for="blucontact_sendEmailNotification">Send Email Notification</label>
				</th>
				<td>
					<?php
					if ( $blucontact_sendEmailNotification == true )
					{
					?>
						<input type="checkbox" id="blucontact_sendEmailNotification" name="blucontact_sendEmailNotification" value="true" checked="checked" />
					<?php
					} else {
					?>
						<input type="checkbox" id="blucontact_sendEmailNotification" name="blucontact_sendEmailNotification" value="true" />
					<?php
					}
					?>
					
					<p class="description">
					If checked, a notification is sent when contact form is submitted.
					</p>
					
				</td>
				</tr>
								
				<tr>
				<th scope="row">
					<label for="blucontact_sendEmailNotificationTo">Send Notification To</label>
				</th>
				<td>
					
					<textarea id="blucontact_sendNotificationTo" name="blucontact_sendNotificationTo" rows="6" cols="60"><?php echo $blucontact_sendNotificationTo; ?></textarea>
					
					<p class="description">
					Email addresses that receives notification. One email address per line.
					</p>
					
				</td>
				</tr>
				
				<tr>
				<th scope="row">
					<label for="blucontact_emailNotificationSubject">Send Notification Subject</label>
				</th>
				<td>
					
					<input type="text" id="blucontact_emailNotificationSubject" name="blucontact_emailNotificationSubject" size="50" maxlength="100" value="<?php echo $blucontact_emailNotificationSubject; ?>" />
					
					<p class="description">
					Subject line of email notification.
					</p>
					
				</td>
				</tr>
				
				<tr>
				<th scope="row">
					<h3>Dev</h3>
				</th>
				<td>
				<td>
				</tr>
				
				<tr>
				<th scope="row">
					<label for="blucontact_debug">Debug</label>
				</th>
				<td>
					<?php
					if ( $blucontact_debug == true )
					{
					?>
						<input type="checkbox" id="blucontact_debug" name="blucontact_debug" value="true" checked="checked" />
					<?php
					} else {
					?>
						<input type="checkbox" id="blucontact_debug" name="blucontact_debug" value="true" />
					<?php
					}
					?>
					
					<p class="description">
					If checked, debug output will be displayed...
					</p>
					
				</td>
				</tr>
				
			</tbody>
		</table>

		<div class="submit">
			<input id="updateBluContactSettings" name="updateBluContactSettings" class="button button-primary" type="submit" value="<?php _e('Update Settings', 'BluContact') ?>" />
		</div>
	
	</form>
	
 </div>
