<?php
/**
 * Show Favourite Artists
 */

function show_favorite_artists() {
  // Fetch artists
  $db = db_connect();
  $user_id = $_SESSION['user_id'];
  $res = $db->query("SELECT A.artistID, A.firstName, A.lastName, A.photoURL,
    C.name AS country,
    (SELECT COUNT(*)
      FROM artistArtworks
      WHERE artistID = A.artistID) AS artworks
    FROM artists A
    INNER JOIN countries C ON C.countryID = A.countryID
    INNER JOIN favoriteArtists F ON F.artistID = A.artistID
    WHERE F.memberID = $user_id");
  if ($res->num_rows < 1):
?>
<p>
  No favourite artists yet.
</p>
<?php else: ?>
<ul class="list">
  <?php
    // Populate results
    while ($artist = $res->fetch_assoc()):
      $artist_id = $artist['artistID'];
      $name = get_artist_name($artist['firstName'], $artist['lastName']);
      $artist_url = "artist.php?id=$artist_id";
      $photo_url = get_artist_photo($artist['photoURL']);
      $country = get_sanitized_text($artist['country']);
      $artworks = $artist['artworks'];
  ?>
  <li class="list__item">
    <a href="<?php echo $artist_url; ?>" class="list__thumbnail list__thumbnail--person" style="background-image:url('<?php echo $photo_url; ?>')"></a>
    <div class="list__text">
      <a href="<?php echo $artist_url; ?>" class="a-inherit">
        <strong><?php echo $name; ?></strong>
      </a><br>
      <small>
        <?php if (!empty($country)) echo 'From ' . $country . '.'; ?>
        Has <?php echo $artworks; ?> artwork<?php if ($artworks > 1) echo 's'; ?>.
      </small>
    </div>
  </li>
  <?php endwhile; ?>
</ul>
<?php endif; ?>
<?php
    $res->free();
    $db->close();
  }
?>
