<?php
/**
 * Utility Functions
 */

/**
 * Returns the site title suffix.
 * @return string Suffix.
 */
function get_site_title_suffix() {
  return ' / PADb';
}

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

/**
 * Returns URL parameters based on key-value pairs.
 * @param array $params Parameters array.
 * @return string URL parameters.
 */
function get_url_params($params) {
  $url = '?';
  $count = 0;
  foreach ($params as $key=>$value) {
    if ($count++ > 0) $url .= '&';
    $url .= "$key=" . urlencode($value);
  }
  return $url;
}

/**
 * Returns sanitized text with proper encoding.
 * @param string $text Text to sanitize.
 * @return string Sanitized text.
 */
function get_sanitized_text($text) {
  return htmlspecialchars(utf8_encode($text));
}

/**
 * Returns full artist name.
 * @param string $fname First name.
 * @param string $lname Last name.
 * @return string Full artist name.
 */
function get_artist_name($fname, $lname) {
  $output = $fname;
  if (!empty($lname)) {
    $output .= ' ' . $lname;
  }
  return get_sanitized_text($output);
}

/**
 * Returns artwork type as a plural.
 * @param string $type Type value.
 * @return string Artwork type as plural.
 */
function get_artwork_type_plural($type) {
  $type = strtolower($type);
  switch ($type) {
    case 'banners':
      break;
    default:
      $type .= 's';
      break;
  }
  return $type;
}

/**
 * Checks if URL is an external link.
 * https://stackoverflow.com/a/22964930
 * @param string $url URL to test.
 * @return boolean TRUE if URL is external.
 */
function is_url_external($url) {
  $components = parse_url($url);
  return !empty($components['host']) && strcasecmp($components['host'], 'example.com');
}

/**
 * Returns URL of cached image.
 * TODO: Cache invalidation.
 * @param string $url URL of source image.
 * @return string URL of locally cached image.
 */
function get_image_cache($url) {
  if (!is_url_external($url)) return $url;
  // $ext = image_type_to_extension(exif_imagetype($url));
  $local = 'images/cache/' . md5($url); // . $ext;
  if (file_exists($local)) return $local;
  if (copy($url, $local)) return $local;
  return NULL;
}

/**
 * Returns artwork photo.
 * @param string $url Artwork photo URL.
 * @return string Sanitized artwork photo or placeholder URL.
 */
function get_artwork_photo($url) {
  return get_sanitized_text(get_image_cache($url) ?? 'images/artwork.png');
}

/**
 * Returns artist photo.
 * @param string $url Artist photo URL.
 * @return string Sanitized artist photo or placeholder URL.
 */
function get_artist_photo($url) {
  return get_sanitized_text(get_image_cache($url) ?? 'images/artist.png');
}

// Include other functions
require_once('functions.pagination.php');
require_once('functions.recent-artworks.php');
require_once('functions.marked-artworks.php');
require_once('functions.favorite-artists.php');
?>
