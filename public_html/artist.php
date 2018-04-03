<?php
/**
 * Artwork
 */

require('../private/functions.php');

$db = db_connect();
$id = (isset($_GET['id'])) ? intval($_GET['id']) : NULL;

if (is_numeric($id)) {
  // Get artist details
  $res = $db->query("SELECT A.firstName, A.lastName, A.websiteURL, A.biography, A.biographyURL, A.photoURL, A.countryID,
    C.name AS country
    FROM artists A
    INNER JOIN countries C ON C.countryID = A.countryID
    WHERE A.artistID = $id");
  $artist = $res->fetch_assoc();
  $name = get_artist_name($artist['firstName'], $artist['lastName']);
  $website_url = get_sanitized_text($artist['websiteURL'] ?? $artist['biographyURL']);
  $biography = get_sanitized_text($artist['biography']);
  $photo_url = get_sanitized_text($artist['photoURL']);
  $country_id = intval($artist['countryID']);
  $country = get_sanitized_text($artist['country']);
  $page_title = $name . get_site_title_suffix();
} else {
  $page_title = 'Unknown Artist' . get_site_title_suffix();
}

require('../private/header.php');
?>

<section class="l-section l-wrap l-wrap--sm">
  <?php include_once('../private/notice.php'); ?>
  <p>
    <a href="artists.php">&lsaquo; View All Artists</a>
  </p>
  <?php if (!isset($res)): // Show error message ?>
  <h1 class="visually-hidden">
    Unknown Artist
  </h1>
  <p>
    Artist doesn't exist :(
  </p>
  <?php else: // Show artwork details ?>
  <p class="listing__profile" <?php if ($photo_url) echo "style=\"background-image:url('$photo_url')\""; ?>></p>
  <h1 class="text-center<?php if (!empty($country)) echo ' no-drop'; ?>">
    <?php echo $name; ?>
  </h1>
  <?php if (!empty($country)): ?>
  <p class="text-center<?php if (is_logged_in()) echo ' drop-sm'; ?>">
    From <a href="artists.php?country=<?php echo $country_id; ?>"><?php echo $country; ?></a>
  </p>
  <?php endif; ?>
  <?php if (is_logged_in()): ?>
  <p class="text-center">
    <a href="" class="btn btn--small">
      Add to Favourite
    </a>
  </p>
  <?php endif; ?>
  <?php if (!empty($biography)): ?>
  <h2 class="h3">
    Biography
  </h2>
  <p>
    <?php echo $biography; ?>
  </p>
  <?php else: ?>
  <p>
    No biography.
  </p>
  <?php endif; ?>
  <?php if (!empty($website_url)): ?>
  <p>
    <a href="<?php echo $website_url; ?>" target="_blank" class="btn">
      Visit Website
    </a>
  </p>
  <?php endif; ?>
  <h2 class="h3">
    Artworks
  </h2>
  <ul class="list">
    <?php
      $res = $db->query("SELECT A.artworkID, A.title, A.yearInstalled, A.photoURL
        FROM artworks A, artistArtworks AA
        WHERE A.artworkID = AA.artworkID AND AA.artistID = $id
        ORDER BY title DESC");
      while ($artwork = $res->fetch_assoc()):
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
        </a>
      </div>
    </li>
    <?php endwhile; ?>
  </ul>
  <p>
    <small>
      Data from <a href="http://vancouver.ca/" target="_blank">City of Vancouver</a>
    </small>
  </p>
  <?php endif; ?>
</section>

<?php require('../private/footer.php'); ?>
