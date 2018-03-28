<?php
/**
 * Home
 */

require('../private/functions.php');

$page = 'home';
$db = db_connect();

require('../private/header.php');
?>

<section class="l-section l-wrap">
  <?php include_once('../private/notice.php'); ?>
  <h1 class="visually-hidden">
    PADb
  </h1>
  <p>
    Public Art Database (PADb) is your source for public artworks in Vancouver.
  </p>
  <h2>
    Recently Installed
  </h2>
  <ul class="list">
      <?php
        $res1 = $db->query("SELECT artworkID, title, yearInstalled, photoURL FROM artworks WHERE status = 'In Place' ORDER BY yearInstalled DESC LIMIT 10");
        while ($artwork = $res1->fetch_assoc()):
          $artworkID = $artwork['artworkID'];
          $artworkURL = 'artwork.php?id=' . $artwork['artworkID'];
          $title = htmlspecialchars(get_artwork_title($artwork['title']));
          $yearInstalled = date('Y', strtotime($artwork['yearInstalled']));
          $photoURL = 'images/empty.png'; // TODO: $artwork['photoURL'];
          $res2 = $db->query("SELECT A.artistID, A.firstName, A.lastName FROM artists A, artistArtworks AA WHERE A.artistID = AA.artistID AND AA.artworkID = $artworkID");
          $count = 0;
      ?>
      <li class="list__item">
        <a href="<?php echo $artworkURL; ?>" class="list__thumbnail" style="background-image:url('<?php echo $photoURL; ?>')"></a>
        <div class="list__text">
          <a href="<?php echo $artworkURL; ?>" class="a-inherit">
            <strong><?php echo $title; ?></strong>
          </a><br>
          <small>
            by
            <?php
              while ($artist = $res2->fetch_assoc()):
                $artistID = $artist['artistID'];
                $name = get_artist_name($artist['firstName'], $artist['lastName']);
            ?><?php if ($count++) echo ', '; ?><a href="artist.php?id=<?php echo $artistID; ?>" class="a-lite"><?php echo $name ?></a><?php endwhile; ?>
            in <?php echo $yearInstalled; ?>
          </small>
        </div>
      </li>
      <?php endwhile; ?>
  </ul>
  <p>
    <a href="artworks.php" class="btn">View All</a>
  </p>
</section>

<?php require('../private/footer.php'); ?>
