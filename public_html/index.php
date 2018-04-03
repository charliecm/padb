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
  <?php
    else:
    // Load accordion preferences
    $db = db_connect();
    $user_id = $_SESSION['user_id'];
    $res = $db->query("SELECT preferences FROM members WHERE memberID = $user_id");
    $old_prefs = json_decode($res->fetch_assoc()['preferences'] ?? [], true);
    function is_active($id) {
      global $old_prefs;
      return isset($old_prefs[$id]) && $old_prefs[$id] === TRUE ? TRUE : FALSE;
    }
  ?>
  <h1>
    Dashboard
  </h1>
  <p>
    Public Art Database (PADb) is your source for public artworks in Vancouver.
  </p>
  <h2 data-id="toSee" class="accordion__header<?php echo is_active('toSee') ? ' -active' : ''; ?>">
    To See
  </h2>
  <div class="accordion__body<?php echo is_active('toSee') ? '' : ' -hidden'; ?>">
    <?php show_marked_artworks('To See'); ?>
  </div>
  <h2 data-id="seen" class="accordion__header<?php echo is_active('seen') ? ' -active' : ''; ?>">
    Have Seen
  </h2>
  <div class="accordion__body<?php echo is_active('seen') ? '' : ' -hidden'; ?>">
    <?php show_marked_artworks('Seen'); ?>
  </div>
  <h2 data-id="favs" class="accordion__header<?php echo is_active('favs') ? ' -active' : ''; ?>">
    Favourite Artists
  </h2>
  <div class="accordion__body<?php echo is_active('favs') ? '' : ' -hidden'; ?>">
    <?php show_favorite_artists(); ?>
  </div>
  <h2 data-id="recent" class="accordion__header<?php echo is_active('recent') ? ' -active' : ''; ?>">
    Recently Installed
  </h2>
  <div class="accordion__body<?php echo is_active('recent') ? '' : ' -hidden'; ?>">
    <?php show_recent_artworks(); ?>
  </div>
  <?php endif; ?>
</section>

<?php require('../private/footer.php'); ?>
