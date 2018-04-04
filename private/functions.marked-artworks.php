<?php
/**
 * Marked Artworks List
 */

function show_marked_artworks($status) {
  // Fetch artworks by status
  $db = db_connect();
  $user_id = $_SESSION['user_id'];
  $res1 = $db->query("SELECT A.artworkID, A.title, A.yearInstalled, A.photoURL
    FROM artworks A
    INNER JOIN marks M ON M.artworkID = A.artworkID
    WHERE M.memberID = $user_id AND M.status = '$status'");
?>
<p class="empty-copy<?php if ($res1->num_rows > 0) echo ' -hidden'; ?>">
  <?php echo ($status === 'To See') ? 'No artworks marked as to see.' : 'No artworks marked as seen.'; ?>
</p>
<ul class="list">
  <?php
    // Populate artworks
    while ($artwork = $res1->fetch_assoc()):
      $artwork_id = intval($artwork['artworkID']);
      $url = 'artwork.php?id=' . $artwork['artworkID'];
      $title = get_sanitized_text($artwork['title']);
      $year_installed = date('Y', strtotime($artwork['yearInstalled']));
      $photo_url = get_artwork_photo($artwork['photoURL']);
  ?>
  <li class="list__item">
    <div class="list__content">
      <a href="<?php echo $url; ?>" class="list__thumbnail" style="background-image:url('<?php echo $photo_url; ?>')"></a>
      <div class="list__text">
        <a href="<?php echo $url; ?>" class="a-inherit">
          <strong><?php echo $title; ?></strong>
        </a><br>
        <small>
          by
          <?php
            // Show artists
            $res2 = $db->query("SELECT A.artistID, A.firstName, A.lastName
              FROM artists A, artistArtworks AA
              WHERE A.artistID = AA.artistID AND AA.artworkID = $artwork_id");
            $count = 0;
            while ($artist = $res2->fetch_assoc()):
              $artist_id = $artist['artistID'];
              $name = get_artist_name($artist['firstName'], $artist['lastName']);
          ?><?php if ($count++) echo ', '; ?><a href="artist.php?id=<?php echo $artist_id; ?>" class="a-lite"><?php echo $name ?></a><?php endwhile; ?>
          in <?php echo $year_installed; ?>
          <?php $res2->free(); ?>
        </small>
      </div>
    </div>
    <a href="#" data-artwork-id="<?php echo $artwork_id; ?>" data-artwork-title="<?php echo $title; ?>" class="action-unmark-artwork btn btn--destructive list__cta">
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
