<?php
/**
 * Update User Preferences
 * TODO: Handle exceptions.
 */

require('../../private/functions.php');

header('Content-type: application/json');

// Check if user is logged in
if (!is_logged_in()) {
  http_response_code(403);
  print json_encode([
    'error' => 'User is not logged in.'
  ]);
  exit;
}

// Check if POST request
if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(400);
  print json_encode([
    'error' => 'Call needs to be a POST request.'
  ]);
  exit;
}

ob_start();
$db = db_connect();
$user_id = $_SESSION['user_id'];
$res = $db->query("SELECT preferences FROM members WHERE memberID = $user_id");
$old_prefs = json_decode($res->fetch_assoc()['preferences'] ?? [], true);
$new_prefs = [];

function set_pref($id) {
  global $new_prefs, $old_prefs;
  if (isset($_POST[$id])) {
    $new_prefs[$id] = $_POST[$id] === 'false' ? FALSE : TRUE;
  } else {
    $new_prefs[$id] = (isset($old_prefs[$id]) && $old_prefs[$id] === FALSE) ? FALSE : TRUE;
  }
}

// Merge preferences
set_pref('toSee');
set_pref('seen');
set_pref('favs');
set_pref('recent');

// Update preferences
$stmt = $db->prepare("UPDATE members SET preferences = ?");
$prefs = json_encode($new_prefs);
$stmt->bind_param('s', $prefs);
$stmt->execute();
if (!$stmt) {
  http_response_code(500);
  print json_encode([
    'message' => 'Database error:' . $stmt->error
  ]);
  exit;
}
print json_encode([
  'message' => 'Updated preferences.',
  'preferences' => $new_prefs
]);
