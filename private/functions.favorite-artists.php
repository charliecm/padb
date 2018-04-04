<?php
/**
 * Show Favourite Artists
 */

function show_favorite_artists() {
  // Fetch artists
  $db = db_connect();
  $user_id = $_SESSION['user_id'];
  $res1 = $db->query("SELECT A.artistID, A.firstName, A.lastName, A.photoURL, A.countryID,
    (SELECT COUNT(*)
      FROM artistArtworks
      WHERE artistID = A.artistID) AS artworks
    FROM artists A
    INNER JOIN favoriteArtists F
    ON F.artistID = A.artistID
    AND F.memberID = $user_id");
?>
<p class="empty-copy<?php if ($res1->num_rows > 0) echo ' -hidden'; ?>">
  No favourite artists.
</p>
<ul class="list">
  <?php
    // Populate artists
    while ($artist = $res1->fetch_assoc()):
      $artist_id = intval($artist['artistID']);
      $name = get_artist_name($artist['firstName'], $artist['lastName']);
      $artist_url = "artist.php?id=$artist_id";
      $photo_url = get_artist_photo($artist['photoURL']);
      $artworks = intval($artist['artworks']);
      $country_id = intval($artist['countryID']);
      $res2 = $db->query("SELECT name FROM countries WHERE countryID = $country_id");
      $country = get_sanitized_text($res2->fetch_assoc()['name']);
      $res2->free();
  ?>
  <li class="list__item">
    <div class="list__content">
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
    </div>
    <a href="#" data-artist-id="<?php echo $artist_id; ?>" data-artist-name="<?php echo $name; ?>" class="action-unfav-artist btn btn--destructive btn--small list__cta">
      Remove
    </a>
  </li>
  <?php endwhile; ?>
</ul>
<?php
    $res1->free();
    $db->close();
  }
?>
