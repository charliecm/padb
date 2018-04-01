<?php
/**
 * Register
 */

require('../private/functions.php');

ensure_https();

$page_id = 'register';
$page_title = 'Register' . get_site_title_suffix();
$inputs = null;
$has_errors = false;
$register_failed = null;

if (isset($_POST['email']) ||
  isset($_POST['name']) ||
  isset($_POST['password'])) {
  $inputs = array();
  // Validate email
  $email = $_POST['email'] ?? '';
  $inputs['email'] = [ 'value' => $email ];
  if (empty($email)) {
    $inputs['email']['error'] = 'Please enter your email.';
    $has_errors = true;
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // http://php.net/manual/en/filter.examples.validation.php
    $inputs['email']['error'] = 'Please enter a valid email.';
    $has_errors = true;
  }
  // Validate name
  $name = $_POST['name'] ?? '';
  $inputs['name'] = [ 'value' => $name ];
  if (empty($name)) {
    $inputs['name']['error'] = 'Please enter your name.';
    $has_errors = true;
  }
  // Validate password
  $password = $_POST['password'] ?? null;
  $inputs['password'] = [ 'value' => $password ];
  if (empty($password)) {
    $inputs['password']['error'] = 'Please enter your password.';
    $has_errors = true;
  }
  if (!$has_errors) {
    // Connect to database
    $db = db_connect();
    // Retrieve product with matching productCode
    $query = "INSERT INTO members (email, name, password) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    $hpass = password_hash($password, PASSWORD_BCRYPT); // Generate hashed password
    $stmt->bind_param('sss', $email, $name, $hpass);
    // Create user in DB
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
      start_session();
      $_SESSION['user_id'] = $stmt->insert_id;
      $_SESSION['first_time'] = htmlspecialchars($name);
      if (isset($_SESSION['redirect'])) {
        // Redirect to intended page
        header('Location: ' . $_SESSION['redirect']);
      } else {
        header('Location: index.php');
      }
      exit;
    } else {
      $register_failed = true;
    }
    $stmt->free_result();
  }
}

require('../private/header.php');
?>

<section class="l-section l-wrap l-wrap--sm">
  <h2>
    Sign Up
  </h2>
  <?php if ($register_failed): // Show error message when account creation failed in the server ?>
  <p class="text-error">
    Sorry, there was a problem creating your account. Please try again!
  </p>
  <?php endif;?>
  <form method="post" class="form--small">
    <div class="form-fieldset">
      <div class="form-field">
        <label class="form-label form-label--required">
          Name
        </label>
        <div class="fill-width">
          <input type="text" name="name" required class="fill-width<?php form_highlight_error($inputs, 'name'); ?>"<?php form_fill_input($inputs, 'name', 'text'); ?>>
          <?php form_display_error($inputs, 'name'); ?>
        </div>
      </div>
      <div class="form-field">
        <label class="form-label form-label--required">
          Email
        </label>
        <div class="fill-width">
          <input type="email" name="email" required class="fill-width<?php form_highlight_error($inputs, 'email'); ?>"<?php form_fill_input($inputs, 'email', 'text'); ?>>
          <?php form_display_error($inputs, 'email'); ?>
        </div>
      </div>
      <div class="form-field">
        <label class="form-label form-label--required">
          Password
        </label>
        <div class="fill-width">
          <input type="password" name="password" required class="fill-width<?php form_highlight_error($inputs, 'password'); ?>"<?php form_fill_input($inputs, 'password', 'text'); ?>>
          <?php form_display_error($inputs, 'password'); ?>
        </div>
      </div>
    </div>
    <div class="form-fieldset">
      <input type="submit" value="Sign Up" class="btn">
    </div>
  </form>
  <p>
    Already registered? <a href="login.php">Login</a>.
  </p>
</section>

<?php require('../private/footer.php'); ?>
