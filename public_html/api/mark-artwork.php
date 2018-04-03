<?php
/**
 * Mark Artwork
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

// Check POST parameters
if (!isset($_POST['artworkID']) || !isset($_POST['status'])) {
  http_response_code(400);
  print json_encode([
    'error' => 'Missing parameters.'
  ]);
  exit;
}

ob_start();
$db = db_connect();
$member_id = $_SESSION['user_id'];
$artwork_id = intval($_POST['artworkID']);
$status = $_POST['status'] === 'To See' ? 'To See' :
  ($_POST['status'] === 'Seen' ? 'Seen' : NULL);

if ($status === NULL) {
  // Unmark artwork
  $res = $db->query("DELETE FROM marks WHERE memberID = $member_id AND artworkID = $artwork_id");
  if (!$res) {
    http_response_code(500);
    print json_encode([
      'message' => 'Database error:' . $db->error
    ]);
    exit;
  }
  print json_encode([
    'message' => 'Artwork is no longer marked.',
    'status' => NULL
  ]);
  exit;
}

// Mark artwork
$res = $db->query("REPLACE INTO marks (memberID, artworkID, status) VALUES ($member_id, $artwork_id, '$status')");
if (!$res || $db->affected_rows <= 0) {
  http_response_code(500);
  print json_encode([
    'message' => 'Database error:' . $db->error
  ]);
  exit;
}
print json_encode([
  'message' => "Artist is marked as: $status",
  'status' => $status
]);
exit;
