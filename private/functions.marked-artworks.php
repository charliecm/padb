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
  if ($res1->num_rows < 1):
?>
<p>
  No artworks marked as to see yet.
</p>
<?php else: ?>
<ul class="list">
  <?php
    // Populate artworks
    while ($artwork = $res1->fetch_assoc()):
      $artwork_id = $artwork['artworkID'];
      $url = 'artwork.php?id=' . $artwork['artworkID'];
      $title = htmlspecialchars(get_sanitized_text($artwork['title']));
      $year_installed = date('Y', strtotime($artwork['yearInstalled']));
      $photo_url = get_artwork_photo($artwork['photoURL']);
  ?>
  <li class="list__item">
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
            $artistID = $artist['artistID'];
            $name = get_artist_name($artist['firstName'], $artist['lastName']);
        ?><?php if ($count++) echo ', '; ?><a href="artist.php?id=<?php echo $artistID; ?>" class="a-lite"><?php echo $name ?></a><?php endwhile; ?>
        in <?php echo $year_installed; ?>
        <?php $res2->free(); ?>
      </small>
    </div>
  </li>
  <?php endwhile; ?>
</ul>
<?php endif; ?>
<?php
    $res1->free();
    $db->close();
  }
?>
