		
	// prepare ajaxForm options object 
	var ajaxFormOptions_Default = { 
		method:       'POST', 
		url:        '/wp-content/plugins/blu-contact/async.php', 
		dataType:	'json',
		data: { async: '1',
		        bluDebugThis: $('#bluDebugThis').fieldValue(),
		        requestUri: location.href
		      },
		beforeSubmit: function(arr, $form, options) { 
			
			console.log( 'contact form ajaxForm beforeSubmit' );
			
			if ( $('#bluDebugThis').fieldValue() == 1 ) {
				console.log( 'debug on' );
			}
			
			//console.log( $form );
			
			// The array of form data takes the following form: 
			// [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ] 
			
			console.log( arr );
			
			//$('form .button-ani').html(button_ani_img_tag);
 
			// return false to cancel submit                  
    		return true; 
		},
		success:    function(json, status) { 
			
			console.log( 'contact form ajaxForm success' );
			console.log( 'status: '+status );
			console.log( json );
			
			//$('form .button-ani').html(spacer_ani_img_tag);
				
			if ( json.debug ) {
			
				$('form #debug').html(json.debug);
				
			} else {
				
				$('form #debug').html('');
			
			}
			
			if ( json.errorHtml ) {
			
				$('form #error').html(json.errorHtml);
				
				$.scrollTo( 'form#contact', 400, {offset:-172}  );
				
			} else {
				
				$('form #error').html('');
			
			}
			
			if ( json.result ) {
			
				$('form #result').html(json.resultHtml);
				
				$('form #error').html('');
				
				$.scrollTo( 'form#contact', 800, {offset:-172}  );
				
			} else {
				
				$('form #result').html('');
			
			}
			
		},
		error:    function(e) { 
			
			console.log( 'contact form ajaxForm error' );
			console.log( e.status+' '+e.statusText );
			console.log( e );
			
			//$('form .button-ani').html(spacer_ani_img_tag);
			
		} 
	}; 
 
	// pass options to ajaxForm 
	$( "form#contact" ).ajaxForm(ajaxFormOptions_Default);
