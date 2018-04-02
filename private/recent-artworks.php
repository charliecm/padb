<ul class="list">
  <?php
    // Populate artworks
    $res1 = $db->query("SELECT artworkID, title, yearInstalled, photoURL
      FROM artworks
      WHERE status = 'In Place'
      ORDER BY yearInstalled DESC
      LIMIT 5");
    while ($artwork = $res1->fetch_assoc()):
      $artwork_id = $artwork['artworkID'];
      $url = 'artwork.php?id=' . $artwork['artworkID'];
      $title = htmlspecialchars(get_sanitized_text($artwork['title']));
      $year_installed = date('Y', strtotime($artwork['yearInstalled']));
      $photoURL = 'images/empty.png'; // TODO: $artwork['photoURL'];
  ?>
  <li class="list__item">
    <a href="<?php echo $url; ?>" class="list__thumbnail" style="background-image:url('<?php echo $photoURL; ?>')"></a>
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
      </small>
    </div>
  </li>
  <?php endwhile; ?>
</ul>
<p>
  <a href="artworks.php" class="btn">View All</a>
</p>
