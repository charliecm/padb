<?php
/**
 * Favourite Artist
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
if (!isset($_POST['artistID']) || !isset($_POST['isFav'])) {
  http_response_code(400);
  print json_encode([
    'error' => 'Missing parameters.'
  ]);
  exit;
}

$member_id = $_SESSION['user_id'];
$artist_id = intval($_POST['artistID']);
$is_fav = $_POST['isFav'] === 'true';

ob_start();
$db = db_connect();

if ($is_fav) {
  // Add artist to favourites
  $res = $db->query("INSERT INTO favoriteArtists (memberID, artistID) VALUES ($member_id, $artist_id)");
  if (!$res || $db->affected_rows <= 0) {
    // https://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html
    if ($db->errno === 1062) {
      print json_encode([
        'message' => 'Artist is already favourited.'
      ]);
      exit;
    }
    http_response_code(500);
    print json_encode([
      'message' => 'Database error:' . $db->error
    ]);
    exit;
  }
  print json_encode([
    'message' => 'Artist is favourited.',
    'isFav' => TRUE
  ]);
  exit;
}

// Remove artist from favourites
$res = $db->query("DELETE FROM favoriteArtists WHERE memberID = $member_id AND artistID = $artist_id");
if (!$res) {
  http_response_code(500);
  print json_encode([
    'message' => 'Database error:' . $db->error
  ]);
  exit;
}
print json_encode([
  'message' => 'Artist is no longer favourited.',
  'isFav' => FALSE
]);
exit;
