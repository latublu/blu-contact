var bluContactAjaxFormSubmitBlock = 0;

(function ( $ ) {

	var bluContactFormSelector = 'form#blu-contact';
	var bluContactSubmitVal = 'Submit';
	
	// Initialize display of error messages
	
	$( bluContactFormSelector ).find('span.error').hide().html('');
	
	// Clear display of field specific errors on focus of input field
	
	$( bluContactFormSelector+' input' ).focus(function() {
		
		$(this).parent().find('span.error').hide().html('');
						
		$(this).parent().removeClass('error');
		$(this).removeClass('error');
		
	});	
	
	// prepare ajaxForm options object 
	var ajaxFormOptions_Default = { 
		method:      'POST', 
		url:        '/wp-content/plugins/blu-contact/async.php', 
		dataType:	'json',
		data: { async: '1',
				bluDebug: $('#blu_contact_debug').fieldValue(),
				requestUri: location.href
			  },
		beforeSubmit: function(arr, $form, options) { 
			
			bluContactAjaxFormSubmitBlock = 1;
		
			//console.log( 'blu-contact form ajaxForm beforeSubmit' );
		
			if ( $('#blu_contact_debug').fieldValue() == 1 ) {
				console.log( 'blu-contact form debug on' );
			}
		
			//console.log( $form );
			
			// Clear error messages
			
			$form.find('#blu-contact-error').hide().html('');
			
			$form.find('span.error').hide().html('');
						
			$form.find('input.error').removeClass('error');
			
			// Change state of submit variable
			
			$('input#blu-contact-submit').addClass('clicked');
			
			bluContactSubmitVal = $('input#blu-contact-submit').val();
			
			$('input#blu-contact-submit').val('Please wait...');
			
			// The array of form data takes the following form: 
			// [ { name: 'name', value: 'foo' }, { name: 'email', value: 'bar' } ] 
		
			console.log( arr );
		
			//$('form .button-ani').html(button_ani_img_tag);

			// return false to cancel submit                  
			return true; 
		},
		success:    function(json, status) { 
		
			console.log( 'blu contact form ajaxForm success' );
			console.log( 'status: '+status );
			console.log( json );
		
			//$('form .button-ani').html(spacer_ani_img_tag);
			
			if ( json.debug ) {
		
				$(bluContactFormSelector+' #blu-contact-debug').html('<pre>'+JSON.stringify(json.debug, null, 2)+'</pre>').show();
			
			} else {
			
				$(bluContactFormSelector+' #blu-contact-debug').html('').hide();
		
			}
					
			if ( json.errors ) {
						
				$.each(json.errors, function(key, value) {
					var thisInput = $('input[name="'+key+'"]');
					
					if ( thisInput.length ) {
						thisInput.addClass('error');
						thisInput.parent().addClass('error');
						thisInput.parent().find('span.error.lbl').show().html(value.label);
						thisInput.parent().find('span.error.msg').show().html(value.message);
					}
				});
				
				$(bluContactFormSelector+' #blu-contact-error').html(json.errorHtml).show();
			
				$.scrollTo( bluContactFormSelector, 400, {offset:-172}  );
				
				$('input#blu-contact-submit').val('Please Try Again');
							
			} else {
			
				$(bluContactFormSelector+' #blu-contact-error').html('').hide();
				
				$('input#blu-contact-submit').val(bluContactSubmitVal);
		
			}
		
			if ( json.result ) {
		
				$(bluContactFormSelector+' #blu-contact-response').html(json.resultHtml).show();
			
				$(bluContactFormSelector+' #blu-contact-error').html('').hide();
			
				$.scrollTo( bluContactFormSelector, 800, {offset:-172}  );
				
				$(bluContactFormSelector+' .blu-contact-fields').hide();
								
				$('input#blu-contact-submit').val('Success. Thank You!');
							
			} else {
			
				$(bluContactFormSelector+' #blu-contact-response').html('').hide();
		
			}
			
			$('input#blu-contact-submit').removeClass('clicked');
			
			bluContactAjaxFormSubmitBlock = 0;
		
		},
		error:    function(e) { 
		
			console.log( 'blu-contact form ajaxForm error' );
			console.log( e.status+' '+e.statusText );
			console.log( e );
		
			//$('form .button-ani').html(spacer_ani_img_tag);
		
		} 
	}; 
	
    // bind to the form's submit event 
    $('body').on('submit', bluContactFormSelector, function() { 
        
        if (!bluContactAjaxFormSubmitBlock) {
        	$(this).ajaxSubmit(ajaxFormOptions_Default); 
 		}
 		
        return false; 
    }); 

}( jQuery ));		
