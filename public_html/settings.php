<?php
/**
 * Settings
 */

require('../private/functions.php');

if (!is_logged_in()) {
  // Redirect if user is not logged in
  header('Location: index.php' );
}

$page_id = 'settings';
$page_title = 'Settings' . get_site_title_suffix();
$inputs = null;
$has_errors = false;
$db = db_connect();

if (isset($_POST['name'])) {
  // Validate name
  $name = $_POST['name'] ?? '';
  $inputs['name'] = [ 'value' => $name ];
  if (empty($name)) {
    $inputs['name']['error'] = 'Please enter your name.';
    $has_errors = true;
  }
  if (!$has_errors) {
    // Update name in DB
    $user_id = $_SESSION['user_id'];
    $stmt = $db->prepare("UPDATE members SET name = ? WHERE memberID = $user_id");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    if ($stmt->affected_rows < 1) {
      // DB error
      $_SESSION['message'] = 'There was a problem updating your name. Please try again.';
    } else {
      // Success
      $_SESSION['message'] = "Your name was updated to $name.";
      $_SESSION['user_name'] = $name;
    }
  }
} else {
  $inputs['name']['value'] = $_SESSION['user_name'];
}

if (isset($_POST['pass']) || isset($_POST['npass'])) {
  // Validate password
  $pass = $_POST['pass'] ?? '';
  $inputs['pass'] = [ 'value' => $pass ];
  if (empty($_POST['npass'])) {
    $inputs['npass']['error'] = 'Please enter your current password.';
    $has_errors = true;
  }
  // Validate new password
  $npass = $_POST['npass'] ?? '';
  $inputs['npass'] = [ 'value' => $npass ];
  if (empty($_POST['npass'])) {
    $inputs['npass']['error'] = 'Please enter a new password.';
    $has_errors = true;
  }
  if (!$has_errors) {
    // Check current password
    $user_id = $_SESSION['user_id'];
    $hpass = '';
    $query = "SELECT password FROM members WHERE memberID = $user_id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bind_result($hpass);
    $stmt->execute();
    if ($stmt->fetch() && password_verify($pass, $hpass)) {
      // Update password
      $stmt->free_result();
      $hpass = password_hash($npass, PASSWORD_BCRYPT); // Generate hashed password
      $stmt = $db->prepare("UPDATE members SET password = ? WHERE memberID = $user_id");
      $stmt->bind_param('s', $hpass);
      $stmt->execute();
      if ($stmt->affected_rows < 1) {
        // DB error
        $_SESSION['message'] = 'There was a problem updating your password. Please try again.';
      } else {
        // Success
        $_SESSION['message'] = 'Your password has been updated.';
      }
      $inputs['pass']['value'] = '';
      $inputs['npass']['value'] = '';
    } else {
      $_SESSION['message'] = 'Failed to update password — entered password is incorrect.';
      $inputs['pass']['error'] = '';
      $inputs['npass']['value'] = '';
    }
  }
}

require('../private/header.php');
?>

<section class="l-section l-wrap">
  <?php include_once('../private/notice.php'); ?>
  <h1>
    Settings
  </h1>
  <h2>
    Change Name
  </h2>
  <form method="post" class="form--small drop-lg">
    <div class="form-field drop-sm">
      <label class="form-label">
        Name
      </label>
      <div class="fill-width">
        <input type="text" name="name" required class="fill-width<?php form_highlight_error($inputs, 'name'); ?>"<?php form_fill_input($inputs, 'name', 'text'); ?>>
        <?php form_display_error($inputs, 'name'); ?>
      </div>
    </div>
    <input type="submit" value="Change Name" class="btn">
  </form>
  <h2>
    Change Password
  </h2>
  <form method="post" class="form--small drop-lg">
    <div class="form-field drop-sm">
      <label class="form-label">
        Current password
      </label>
      <div class="fill-width">
        <input type="password" name="pass" required class="fill-width<?php form_highlight_error($inputs, 'pass'); ?>"<?php form_fill_input($inputs, 'pass', 'text'); ?>>
        <?php form_display_error($inputs, 'pass'); ?>
      </div>
    </div>
    <div class="form-field drop-sm">
      <label class="form-label">
        New password
      </label>
      <div class="fill-width">
        <input type="password" name="npass" required class="fill-width<?php form_highlight_error($inputs, 'npass'); ?>"<?php form_fill_input($inputs, 'npass', 'text'); ?>>
        <?php form_display_error($inputs, 'npass'); ?>
      </div>
    </div>
    <input type="submit" value="Change Password" class="btn">
  </form>
</section>

<?php require('../private/footer.php'); ?>
