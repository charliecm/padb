<?php
/**
 * Notice
 */

start_session();

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
  <?php echo 'Welcome to PADb, ' . $_SESSION['first_time'] . '!'; ?>
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
?>
