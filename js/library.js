$(document).ready(function(){
/*
 * NorCal Winter Camp
 * library.js
 *
*/
	
	// Load sign up form
	$('#formTarget').load('backend.php');
	
	// Handle sign up form submission and response
	$('#sign-up').on('submit', "#rsvp", function(event) {
		// stop form from submitting normally
		event.preventDefault();
		
		// get some values from elements on the page:
		var $form = $( this ),
			form_data = $form.serialize(),
			url = $form.attr('action');
		//alert(form_data);

		// Send the data using post
		var posting = $.post(url, {data:form_data});

		// Put the results in a div
		posting.done(function(data) {
			//alert(data);
			$("#formTarget").empty().append(data);
		});
	});

//End document ready stuff
});