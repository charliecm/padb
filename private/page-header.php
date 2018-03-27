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
  global $page;
  if (isset($page) && $page === $name)
    echo ' class="-active"';
}
?>
<header class="header">
  <div class="header-wrap l-wrap">
    <h1 class="header-title">
      <a href="index.php">
        PADb
      </a>
    </h1>
    <nav class="header-nav">
      <ul>
        <li>
          <a href="artworks.php"<?php flag_active('artworks'); ?>>Artworks</a>
        </li>
        <li>
          <a href="artists.php"<?php flag_active('artists'); ?>>Artists</a>
        </li>
        <?php
          // Check if logged in
          start_session();
          if (!empty($_SESSION['user_id'])):
        ?>
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
  </div>
</header>
