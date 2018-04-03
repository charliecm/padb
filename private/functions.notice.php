<?php
/**
 * Notice
 * TODO: Add notice type (success/error).
 */

start_session();

function show_notice($message) {
?>
<p class="notice">
  <?php echo $message; ?>
</p>
<?php
}

function show_global_notices() {
  // Show logged out message
  if (isset($_GET['logged_out'])):
?>
<p class="notice">
  You've been successfully logged out.
</p>
<?php
  endif;

  // Show post-registration message
  if (isset($_SESSION['first_time'])):
?>
<p class="notice">
  <?php echo 'Welcome to PADb, ' . htmlspecialchars($_SESSION['first_time']) . '! You can now track artworks as to see or seen, and add artists to your favourites list.'; ?>
</p>
<?php
    unset($_SESSION['first_time']);
  endif;

  // Show generic message
  if (isset($_SESSION['message'])):
?>
<p class="notice">
  <?php echo $_SESSION['message']; ?>
</p>
<?php
    unset($_SESSION['message']);
  endif;
}
?>
