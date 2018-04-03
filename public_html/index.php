<?php
/**
 * Home
 */

require('../private/functions.php');

$page_id = 'home';
$page_title = 'Dashboard' . get_site_title_suffix();

require('../private/header.php');
?>

<section class="l-section l-wrap l-wrap--md">
  <?php show_global_notices(); ?>
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
  <?php show_recent_artworks(); ?>
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
  <?php show_marked_artworks('To See'); ?>
  <h2>
    Favourite Artists
  </h2>
  <?php show_favorite_artists(); ?>
  <h2>
    Recently Installed
  </h2>
  <?php show_recent_artworks(); ?>
  <h2>
    Artworks Already Seen
  </h2>
  <?php show_marked_artworks('Seen'); ?>
  <?php endif; ?>
</section>

<?php require('../private/footer.php'); ?>
