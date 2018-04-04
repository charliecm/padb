<?php
/**
 * Settings
 */

require('../private/functions.php');

ensure_https();

if (!is_logged_in()) {
  // Redirect if user is not logged in
  header('Location: index.php' );
}

$page_id = 'settings';
$page_title = 'Settings' . get_site_title_suffix();
$inputs = null;
$has_errors = false;
$change_name_msg = '';
$change_password_msg = '';
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
      $change_name_msg = 'There was a problem updating your name. Please try again.';
    } else {
      // Success
      $change_name_msg = 'Your name was updated to ' . get_sanitized_text($name) . '.';
      $_SESSION['user_name'] = $name;
    }
  }
} else {
  $inputs['name']['value'] = $_SESSION['user_name'];
}

if (isset($_POST['pass']) || isset($_POST['npass1']) || isset($_POST['npass2'])) {
  // Validate password
  $pass = $_POST['pass'] ?? '';
  $inputs['pass'] = [ 'value' => $pass ];
  if (empty($_POST['pass'])) {
    $inputs['pass']['error'] = 'Please enter your current password.';
    $has_errors = true;
  }
  // Validate new password
  $npass1 = $_POST['npass1'] ?? '';
  $inputs['npass1'] = [ 'value' => $npass1 ];
  if (empty($_POST['npass1'])) {
    $inputs['npass1']['error'] = 'Please enter a new password.';
    $has_errors = true;
  }
  // Confirm password
  $npass2 = $_POST['npass2'] ?? '';
  $inputs['npass2'] = [ 'value' => $npass2 ];
  if (empty($_POST['npass2'])) {
    $inputs['npass2']['error'] = 'Please confirm your new password.';
    $has_errors = true;
  }
  if ($npass1 !== $npass2) {
    $inputs['npass2']['error'] = 'Passwords don\'t match.';
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
      $hpass = password_hash($npass1, PASSWORD_BCRYPT); // Generate hashed password
      $stmt = $db->prepare("UPDATE members SET password = ? WHERE memberID = $user_id");
      $stmt->bind_param('s', $hpass);
      $stmt->execute();
      if ($stmt->affected_rows < 1) {
        // DB error
        $change_password_msg = 'There was a problem updating your password. Please try again.';
      } else {
        // Success
        $change_password_msg = 'Your password has been updated.';
      }
      $inputs['pass']['value'] = '';
      $inputs['npass1']['value'] = '';
      $inputs['npass2']['value'] = '';
    } else {
      $inputs['pass']['error'] = 'Password is incorrect.';
      $inputs['npass1']['value'] = '';
      $inputs['npass2']['value'] = '';
    }
  }
}

require('../private/header.php');
?>

<section class="l-section l-wrap">
  <?php show_global_notices(); ?>
  <h1>
    Settings
  </h1>
  <h2 id="change-name">
    Change Name
  </h2>
  <?php if (!empty($change_name_msg)) show_notice($change_name_msg); ?>
  <form method="post" action="#change-name" class="form--small drop-lg">
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
  <h2 id="change-password">
    Change Password
  </h2>
  <?php if (!empty($change_password_msg)) show_notice($change_password_msg); ?>
  <form method="post" action="#change-password" class="form--small drop-lg">
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
        <input type="password" name="npass1" required class="fill-width<?php form_highlight_error($inputs, 'npass1'); ?>"<?php form_fill_input($inputs, 'npass1', 'text'); ?>>
        <?php form_display_error($inputs, 'npass1'); ?>
      </div>
    </div>
    <div class="form-field drop-sm">
      <label class="form-label">
        Confirm password
      </label>
      <div class="fill-width">
        <input type="password" name="npass2" required class="fill-width<?php form_highlight_error($inputs, 'npass2'); ?>"<?php form_fill_input($inputs, 'npass2', 'text'); ?>>
        <?php form_display_error($inputs, 'npass2'); ?>
      </div>
    </div>
    <input type="submit" value="Change Password" class="btn">
  </form>
</section>

<?php require('../private/footer.php'); ?>
