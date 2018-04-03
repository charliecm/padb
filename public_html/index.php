<?php
/**
 * Home
 */

require('../private/functions.php');

$page_id = 'home';
$page_title = 'Dashboard' . get_site_title_suffix();
$db = db_connect();

require('../private/header.php');
?>

<section class="l-section l-wrap">
  <?php include_once('../private/notice.php'); ?>
  <?php if (!is_logged_in()): // Show visitor page ?>
  <h1 class="visually-hidden">
    PADb
  </h1>
  <p class="drop-sm">
    Public Art Database (PADb) is your source for public artworks in Vancouver.<br>
    Want to keep track of which public artworks you've seen?
  </p>
  <p>
    <a href="register.php" class="btn btn--primary">
      Join Now
    </a><!--
    --><span class="text-or">or</span><!--
    --><a href="login.php" class="btn">
      Login
    </a>
  </p>
  <h2>
    Recently Installed
  </h2>
  <?php include('../private/list-recent-artworks.php'); ?>
  <?php else: ?>
  <h1>
    Dashboard
  </h1>
  <p>
    Public Art Database (PADb) is your source for public artworks in Vancouver.
  </p>
  <h2>
    Artworks To See
  </h2>
  <p>
    No artworks marked as to see yet.
  </p>
  <h2>
    Favourite Artists
  </h2>
  <p>
    No favourite artists yet.
  </p>
  <h2>
    Recently Installed
  </h2>
  <?php include('../private/list-recent-artworks.php'); ?>
  <h2>
    Artworks Already Seen
  </h2>
  <p>
    No artworks already seen yet.
  </p>
  <?php endif; ?>
</section>

<?php require('../private/footer.php'); ?>
