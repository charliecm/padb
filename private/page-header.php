<?php
/**
 * Page Header
 */

require_once('../private/functions.php');

/**
 * Output active class for navigation item based on $page.
 * @param string $name Page name.
 */
function flag_active($name) {
  global $page_id;
  if (isset($page_id) && $page_id === $name)
    echo ' class="-active"';
}
?>
<header class="header">
  <div class="header-wrap l-wrap">
    <a href="index.php" class="header-logo">
      PADb
    </a>
    <nav class="header-nav">
      <ul>
        <li class="header-nav__home">
          <a href="index.php"<?php flag_active('home'); ?>>Home</a>
        </li>
        <li>
          <a href="artworks.php"<?php flag_active('artworks'); ?>>Artworks</a>
        </li>
        <li>
          <a href="artists.php"<?php flag_active('artists'); ?>>Artists</a>
        </li>
        <?php
          // Check if logged in
          start_session();
          if (is_logged_in()):
        ?>
        <li>
          <a href="settings.php"<?php flag_active('settings'); ?>>Settings</a>
        </li>
        <li>
          <a href="logout.php">Logout</a>
        </li>
        <?php else: ?>
        <li>
        <a href="login.php"<?php flag_active('login'); ?>>Login</a>
        </li>
        <li>
          <a href="register.php"<?php flag_active('register'); ?>>Register</a>
        </li>
        <?php endif; ?>
      </ul>
    </nav>
    <?php if (is_logged_in()): ?>
    <p class="header-status">
      Logged in as <?php echo $_SESSION['user_name']; ?>
    </p>
    <?php endif; ?>
  </div>
</header>
