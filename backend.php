<?php
/*
 * NorCal Winter Camp 2017
 * backend.php
 *
*/


/*=======================================================
	Is this thing working...
=======================================================*/
//echo '<p>Process begins...</p>';


/*=======================================================
	Check form submission status
	If form submitted validate data
		If data is valid
			format data
			write to file
			return thank you
		If data is NOT valid
			rebuild forms with error notices and data
=======================================================*/
if (isset($_POST['data'])) {
	//echo '<p>Data submitted...</p>';
	//jad_debug($_POST);
	//echo '<p>Start validation...</p>';
	
	// Get data into an array
	$form_data = array();
	parse_str($_POST['data'], $form_data);
	
	//jad_debug($form_data);
	
	// Rudimentary spam checker, kill script if a "comment" has been entered
	if ($form_data['comment'] != '') die;
	
	// Start error array
	$errors = array();
	
	// First name
	if (jad_not_empty($form_data['First_Name'])) {
		// Value was entered, test for alpha
		if (!jad_alpha($form_data['First_Name'])) {
			$errors['First_Name'] = 'Please use letters only.';
		}
	}
	else {
		// No value was entered, set error message
		$errors['First_Name'] = 'Please enter a first name.';
	}
	
	// Last name
	if (jad_not_empty($form_data['Last_Name'])) {
		// Value was entered, test for alpha
		if (!jad_alpha($form_data['Last_Name'])) {
			$errors['Last_Name'] = 'Please use letters only.';
		}
	}
	else {
		// No value was entered, set error message
		$errors['Last_Name'] = 'Please enter a last name.';
	}
	
	// Church name
	if (jad_not_empty($form_data['Church_Name'])) {
		// Value was entered, test for alpha
		if (!jad_alpha($form_data['Church_Name'])) {
			$errors['Church_Name'] = 'Please use letters only.';
		}
	}
	else {
		// No value was entered, set error message
		$errors['Church_Name'] = 'Please enter a church name.';
	}
	
	// Email
	if (jad_not_empty($form_data['Email'])) {
		// Value was entered, test for valid email
		if (!filter_var($form_data['Email'], FILTER_VALIDATE_EMAIL)) {
			$errors['Email'] = 'Please enter a valid email address.';
		}
	}
	else {
		// No value was entered, set error message
		$errors['Email'] = 'Please enter an email address.';
	}
	
	// Expected Students
	if (jad_not_empty($form_data['Expected_Students'])) {
		// Value was entered, test for alpha
		if (!is_numeric($form_data['Expected_Students'])) {
			$errors['Expected_Students'] = 'Please use numbers only.';
		}
	}
	else {
		// No value was entered, set error message
		$errors['Expected_Students'] = 'Please enter the number of expected students.';
	}
	
	if (count($errors) == 0) {
		// No errors, so save data
		//echo '<p>No errors, save data...</p>';
		
		if (jad_save_data($form_data)) {
			// Data successfully saves
			$out = '<div class="alert alert-info"><h2 class="section-heading">Got It!</h2>';
			$out .= '<p>Thanks for signing up! We&#8217;ll be in touch shortly.</p>';
			$out .= '<p>Make sure you <a href="http://s3cdn.crossroadslive.com.s3.amazonaws.com/wintercamp/packet.zip">download the start up packet</a> and if you have any further questions or comments, <a href="mailto:info@norcalwintercamp.com">drop us a line</a>.</p></div>';
			
			echo $out;
		}
		else {
			// Data did not save
			echo '<p>Opps! The data did not get saved. Refresh the page and try again.</p>';
		}
	}
	else {
		//echo '<p>Errors found...</p>';
		//jad_debug($errors);
		
		// Errors found, rebuild form
		echo jad_build_form($errors, $form_data);
	}
}
else {
	// No form submitted, output form
	echo jad_build_form();
}


/*=======================================================
	Validation functions
=======================================================*/
function jad_alpha($incoming) {
	return preg_match('/^[A-Za-z0-9\040]+$/', $incoming);
}

function jad_not_empty($incoming) {
	return strlen($incoming);
}


/*=======================================================
	function jad_save_data
	
	format and save data
=======================================================*/
function jad_save_data($form_data = null) {
	$str = jad_format_data($form_data);
	
	$success = jad_write_data($str);
	
	return $success;
}


/*=======================================================
	function jad_format_data
	
	prepare form data for entry into text file
=======================================================*/
function jad_format_data($form_data = null) {
	$str = '';
	
	$str .= '"'.$form_data['Last_Name'].', '.$form_data['First_Name'].'"';
	$str .= ', '.$form_data['Church_Name'];
	$str .= ', '.$form_data['Email'];
	$str .= ', '.$form_data['Expected_Students'];
	$str .= ', '.date('m-d-Y H:i');
	$str .= "\n";
	
	return $str;
}


/*=======================================================
	function jad_write_data
	
	Writes form data to text file
=======================================================*/
function jad_write_data($data) {
	$file_path = dirname(__FILE__).'/';
	
	$fh = fopen($file_path.'rsvps.txt', 'a+');
	if ($fh) {
		if (fwrite($fh, $data)) {
			fclose($fh);
			return true;
		}
		else {
			fclose($fh);
			return false;
		}
	}
	
	return false;
}


/*=======================================================
	function jad_debug
=======================================================*/
function jad_debug($incoming, $output_type='print_r') {
	echo '<pre>';
	if ($output_type == 'var_dump') {
		var_dump($incoming);
	}
	else {
		print_r($incoming);
	}
	echo '</pre>';
}


/*=======================================================
	function jad_build_form
=======================================================*/
function jad_build_form($errors = null, $form_data = null) {
	// Degbugging
	//jad_debug($form_data);
	//jad_debug($errors);
	
	
	$out = '<h2 class="section-heading">Sign Up Now!</h2>';
	//$out .= '<hr class="primary">';
	$out .= '<p>Are you and your church interested in being a part of winter camp with us? Fantastic!
	Give us some details and we&#8217;ll be in touch. (Note: All of the form fields are required.)</p>';
	
	if (count($errors) > 0) {
		$out .= '<p class="alert alert-danger"><b>Rats!</b> It seems like the form needs your attention.</p>';
	}
	
	// Start Form
	$out .= '<form id="rsvp" class="text-left" method="post" action="'.$_SERVER['PHP_SELF'].'">';
	
	// First Name
	$out .= '<div class="form-group';
	if (isset($errors['First_Name'])) {
		$out .= ' has-error';
	}
	$out .= '"><label for="First_Name" class="control-label">First Name:</label>';
	$value = ($form_data != null) ? $form_data['First_Name'] : '';
	$out .= '<input type="text" class="form-control" id="First_Name" name="First_Name" placeholder="First Name" value="'.$value.'" />';
	if (isset($errors['First_Name'])) {
		$out .= '<span class="help-block">'.$errors['First_Name'].'</span>';
	}
	$out .= '</div>';
	
	// Last Name
	$out .= '<div class="form-group';
	if (isset($errors['Last_Name'])) {
		$out .= ' has-error';
	}
	$out .= '"><label for="Last_Name" class="control-label">Last Name:</label>';
	$value = ($form_data != null) ? $form_data['Last_Name'] : '';
	$out .= '<input type="text" class="form-control" id="Last_Name" name="Last_Name" placeholder="Last Name" value="'.$value.'" />';
	if (isset($errors['Last_Name'])) {
		$out .= '<span class="help-block">'.$errors['Last_Name'].'</span>';
	}
	$out .= '</div>';
	
	// Church Name
	$out .= '<div class="form-group';
	if (isset($errors['Church_Name'])) {
		$out .= ' has-error';
	}
	$out .= '"><label for="Church_Name" class="control-label">Church Name:</label>';
	$value = ($form_data != null) ? $form_data['Church_Name'] : '';
	$out .= '<input type="text" class="form-control" id="Church_Name" name="Church_Name" placeholder="Church Name" value="'.$value.'" />';
	if (isset($errors['Church_Name'])) {
		$out .= '<span class="help-block">'.$errors['Church_Name'].'</span>';
	}
	$out .= '</div>';
	
	// Email
	$out .= '<div class="form-group';
	if (isset($errors['Email'])) {
		$out .= ' has-error';
	}
	$out .= '"><label for="Email" class="control-label">Email:</label>';
	$value = ($form_data != null) ? $form_data['Email'] : '';
	$out .= '<input type="text" class="form-control" id="Email" name="Email" placeholder="Email" value="'.$value.'" />';
	if (isset($errors['Email'])) {
		$out .= '<span class="help-block">'.$errors['Email'].'</span>';
	}
	$out .= '</div>';
	
	// Expected Students
	$out .= '<div class="form-group';
	if (isset($errors['Expected_Students'])) {
		$out .= ' has-error';
	}
	$out .= '"><label for="Expected_Students" class="control-label">Expected Students:</label>';
	$value = ($form_data != null) ? $form_data['Expected_Students'] : '';
	$out .= '<input type="text" class="form-control" id="Expected_Students" name="Expected_Students" placeholder="Expected Students" value="'.$value.'" />';
	if (isset($errors['Expected_Students'])) {
		$out .= '<span class="help-block">'.$errors['Expected_Students'].'</span>';
	}
	$out .= '</div>';
	
	// Basic spam catcher
	$out .= '<div id="comment"><label for="comment">Do Not Fill This Out</label><textarea name="comment" id="comment" rows="1" cols="1"></textarea></div>';
	
	// Submit
	$out .= '<div class="form-group"><button type="submit" class="btn btn-primary btn-xl center-block">Submit</button></div>';
	
	// Close form
	$out .= '</form>';
	
	// Close Containers
	$out .= '</div>';

	return $out;

// End jad_build_form
}


// End PHP code
?>