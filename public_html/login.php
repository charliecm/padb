<?php
/**
 * Login
 */

require('../private/functions.php');

ensure_https();

$page_id = 'login';
$page_title = 'Login' . get_site_title_suffix();
$inputs = null;
$has_errors = false;
$login_failed = false;

if (isset($_POST['user']) || isset($_POST['pass'])) {
  $inputs = array();
  // Validate username (email)
  $user = $_POST['user'] ?? null;
  $inputs['user'] = [ 'value' => $user ];
  if (empty($user)) {
    $inputs['user']['error'] = 'Please enter your email.';
    $has_errors = true;
  }
  // Validate password
  $pass = $_POST['pass'] ?? null;
  $inputs['pass'] = [ 'value' => $pass ];
  if (empty($pass)) {
    $inputs['pass']['error'] = 'Please enter your password.';
    $has_errors = true;
  }
  if (!$has_errors) {
    // Attempt login
    $db = db_connect();
    $query = "SELECT memberID, password, name FROM members WHERE email = ? LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $user);
    $stmt->bind_result($user_id, $hpass, $name);
    $stmt->execute();
    if ($stmt->fetch() && password_verify($pass, $hpass)) {
      // Remember the user
      start_session();
      $_SESSION['user_id'] = $user_id;
      $_SESSION['user_name'] = $name;
      $_SESSION['message'] = "Welcome back, $name!";
      if (isset($_SESSION['redirect'])) {
        // Redirect to intended page
        header('Location: '. $_SESSION['redirect']);
      } else {
        header('Location: index.php');
      }
      exit;
    } else {
      $login_failed = true;
    }
    $stmt->free_result();
  }
}

require('../private/header.php');
?>

<main class="l-wrap-all">
  <div class="l-wrap-content">
    <?php include('../private/page-header.php'); ?>

    <section class="l-section l-wrap l-wrap--sm">
      <?php if (isset($_GET['redirect'])): ?>
      <p class="notice">
        Please log into your account first.
      </p>
      <?php endif; ?>
      <?php show_global_notices(); ?>
      <h2>
        Login
      </h2>
      <?php if ($login_failed): ?>
      <p class="text-error">
        Invalid email or password. Please try again!
      </p>
      <?php endif;?>
      <form method="post" class="form--small drop">
        <div class="form-field drop-sm">
          <label class="form-label">
            Email
          </label>
          <div class="fill-width">
            <input type="text" name="user" required class="fill-width<?php form_highlight_error($inputs, 'user'); ?>"<?php form_fill_input($inputs, 'user', 'text'); ?>>
            <?php form_display_error($inputs, 'user'); ?>
          </div>
        </div>
        <div class="form-field drop">
          <label class="form-label">
            Password
          </label>
          <div class="fill-width">
            <input type="password" name="pass" required class="fill-width<?php form_highlight_error($inputs, 'pass'); ?>"<?php form_fill_input($inputs, 'pass', 'text'); ?>>
            <?php form_display_error($inputs, 'pass'); ?>
          </div>
        </div>
        <input type="submit" value="Login" class="btn">
      </form>
      <p>
        Not registered yet?
        <a href="register.php">Sign up</a>.
      </p>
    </section>

  </div>
  <?php include('../private/page-footer.php'); ?>
</main>
<?php require('../private/footer.php'); ?>
