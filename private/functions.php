<?php
/**
 * Utility Functions
 */

/**
 * Attempts connection to database.
 * @return mysqli
 */
function db_connect() {
	require('db-config.php');
	$db = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	if ($errno = $db->connect_errno) {
		die("<p>There was a problem connecting to the database: $errno</p>");
	}
	return $db ?? null;
}

/**
 * Checks and starts a session.
 */
function start_session() {
	if (session_status() == PHP_SESSION_NONE) session_start();
}

/**
 * Checks if user is logged in, otherwise redirect to login page.
 */
function check_login() {
	start_session();
	if (!isset($_SESSION['user_id'])) {
		$_SESSION['redirect'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		header("Location: login.php");
		exit;
	}
}

/**
 * Returns whether the user is logged in or not.
 * @return boolean True if user is logged in.
 */
function is_logged_in() {
	start_session();
	return isset($_SESSION['user_id']);
}

/**
 * Redirects to HTTPS if URL not secured.
 */
function ensure_https() {
	if ($_SERVER['HTTPS'] !== 'on') {
		header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit;
	}
}

/**
 * Outputs an input value for prefill.
 * @param string $name Input name.
 * @param string $type Input type.
 * @param string $select Select option value for 'select' and 'radio' input types.
 */
function form_fill_input($inputs, $name, $type, $select = null) {
	if ($inputs !== null && isset($inputs[$name]['value'])) {
		$value = $inputs[$name]['value'];
		switch($type) {
			case 'text':
				// Output value attribute in <input>
				echo sprintf(' value="%s"', htmlspecialchars($value));
				break;
			case 'textarea':
				// Output raw value
				echo htmlspecialchars($value);
				break;
			case 'select':
				// Output 'selected' attribute in <option>
				if (isset($select) && (string)$value === (string)$select) {
					echo ' selected';
				}
				break;
			case 'radio':
				// Output 'checked' attribute in radio <input>
				if (isset($select) && (string)$value === (string)$select) {
					echo ' checked';
				}
				break;
		}
	}
}

/**
 * Outputs error text(s).
 * @param string $names Input name(s) to display error message(s) for.
 */
function form_display_error($inputs, ...$names) {
	if ($inputs === null) return;
	$count = 0;
	foreach ($names as $name) {
		if (isset($inputs[$name]['error'])) {
			if ($count === 0) echo '<p class="form-error">';
			if ($count > 0) echo ' '; // Add space between multiple error messages
			echo htmlspecialchars($inputs[$name]['error']);
			$count++;
		}
	}
	if ($count > 0) echo '</p>';
}

/**
 * Outputs input error class if input has an error.
 * @param string $name Input name to check error for.
 */
function form_highlight_error($inputs, $name) {
	if ($inputs !== null && isset($inputs[$name]['error'])) {
		echo ' form-input--error';
	}
}

function get_artist_name($fname, $lname) {
  $output = $fname;
  if (!empty($lname)) {
    $output .= ' ' . $lname;
  }
  return utf8_encode($output);
}

function get_artwork_title($name) {
  return utf8_encode($name);
}


?>
