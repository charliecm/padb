<?php
/**
 * Artwork
 */

require('../private/functions.php');
require('../private/config.php');

ensure_http();

$id = (isset($_GET['id'])) ? intval($_GET['id']) : NULL;
$db = db_connect();

if (is_numeric($id)) {
  // Get artwork details
  $res = $db->query("SELECT A.title, A.status, A.yearInstalled, A.material, A.description, A.statement, A.photoURL, A.latitude, A.longitude, A.siteName, A.siteAddress, T.type, T.typeID,
    N.name AS neighborhoodName, N.neighborhoodID,
    O.name AS ownerName, O.ownerID
    FROM artworks A
    INNER JOIN types T ON T.typeID = A.typeID
    INNER JOIN neighborhoods N ON N.neighborhoodID = A.neighborhoodID
    INNER JOIN owners O ON O.ownerID = A.ownerID
    WHERE A.artworkID = $id;");
  $artwork = $res->fetch_assoc();
  $title = get_sanitized_text($artwork['title']);
  $status = get_sanitized_text($artwork['status']);
  $year_installed = date('Y', strtotime($artwork['yearInstalled']));
  $type = get_sanitized_text($artwork['type']);
  $type_id = intval($artwork['typeID']);
  $neighborhood_name = get_sanitized_text($artwork['neighborhoodName']);
  $neighborhood_id = intval($artwork['neighborhoodID']);
  $owner_name = get_sanitized_text($artwork['ownerName']);
  $owner_id = intval($artwork['ownerID']);
  $material = get_sanitized_text($artwork['material']);
  $description = get_sanitized_text($artwork['description']);
  $artist_statement = get_sanitized_text($artwork['statement']);
  $photo_url = get_artwork_photo($artwork['photoURL']);
  if ($artwork['latitude'] && $artwork['longitude']) {
    $latitude = floatval($artwork['latitude']);
    $longitude = floatval($artwork['longitude']);
    $lat_lng = $latitude . ',' . $longitude;
  }
  $site_name = get_sanitized_text($artwork['siteName']);
  $site_address = get_sanitized_text($artwork['siteAddress']);
  $page_title = $title . get_site_title_suffix();
} else {
  $page_title = 'Unknown Artwork' . get_site_title_suffix();
}

require('../private/header.php');
?>

<section class="l-section l-wrap">
  <?php show_global_notices(); ?>
  <p>
    <a href="artworks.php">&lsaquo; View All Artworks</a>
  </p>
  <?php if (!isset($res)): // Show error message ?>
  <h1 class="visually-hidden">
    Unknown Artwork
  </h1>
  <p>
    Artwork doesn't exist :(
  </p>
  <?php else: // Show artwork details ?>
  <h1>
    <?php echo $title; ?>
  </h1>
  <div class="row">
    <div class="col col--4md">
      <p class="listing__photo">
        <a href="<?php echo $photo_url; ?>" target="_blank">
          <img src="<?php echo $photo_url; ?>" alt="<?php echo $title; ?>" class="img-responsive">
        </a>
      </p>
      <?php if (isset($lat_lng)): ?>
      <img src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $lat_lng; ?>&zoom=13&size=302x200&maptype=roadmap&scale=2&markers=color:red%7C<?php echo $lat_lng; ?>&key=<?php echo $google_static_map_api_key; ?>" class="listing__map">
      <?php endif; ?>
      <p>
        <?php
          // Show location meta
          $location = ($status === 'In Place') ? 'Can be found ' : 'Used to be ';
          if ($site_name) {
            $location .= "at $site_name";
            if ($site_address)
              $location .= " ($site_address) in ";
            else
              $location .= ' in ';
          } else {
            if ($site_address)
              $location .= "$site_address in ";
            else
              $location .= 'in ';
          }
          $location .= "<a href=\"artworks.php?neighborhood=$neighborhood_id\">$neighborhood_name</a>";
          echo $location;
        ?>
      </p>
    </div>
    <div class="col col--8md">
      <?php if (is_logged_in()):
        $user_id = $_SESSION['user_id'];
        $res = $db->query("SELECT status FROM marks WHERE memberID = $user_id AND artworkID = $id LIMIT 1");
        $mark_status = '';
        if ($row = $res->fetch_assoc()) {
          $mark_status = $row['status'] ?? '';
        };
      ?>
      <p class="drop--sm">
        Mark as
        <a href="#" data-artwork-id="<?php echo $id; ?>" data-status="To See" class="action-mark-artwork btn btn--small<?php if ($mark_status === 'To See') echo ' -active'; ?>">
          To See
        </a>
        <a href="#" data-artwork-id="<?php echo $id; ?>" data-status="Have Seen" class="action-mark-artwork btn btn--small<?php if ($mark_status === 'Have Seen') echo ' -active'; ?>">
          Have Seen
        </a>
      </p>
      <?php endif; ?>
      <p>
        <strong>Artists:</strong>
        <?php
          // Show artists
          $res = $db->query("SELECT A.artistID, A.firstName, A.lastName
            FROM artists A, artistArtworks AA
            WHERE A.artistID = AA.artistID AND AA.artworkID = $id");
          $count = 0;
          while ($artist = $res->fetch_assoc()):
            $artistID = $artist['artistID'];
            $name = get_artist_name($artist['firstName'], $artist['lastName']);
        ?><?php if ($count++) echo ', '; ?><a href="artist.php?id=<?php echo $artistID; ?>"><?php echo $name ?></a><?php endwhile; ?><br>
        <strong>Owner:</strong> <a href="artworks.php?owner=<?php echo $owner_id; ?>"><?php echo $owner_name; ?></a><br>
        <strong>Type:</strong> <a href="artworks.php?type=<?php echo $type_id; ?>"><?php echo $type; ?></a><br>
        <strong>Year Installed:</strong> <?php echo $year_installed; ?><br>
        <strong>Status:</strong> <?php echo $status; ?>
      </p>
      <?php if (!empty($description)): ?>
      <h2>
        Description
      </h2>
      <p>
        <?php echo $description; ?>
      </p>
      <?php else: ?>
      <p>
        No description.
      </p>
      <?php endif; ?>
      <?php if (!empty($artist_statement)): ?>
      <h2>
        Artist's Statement
      </h2>
      <p>
        <?php echo $artist_statement; ?>
      </p>
      <?php endif; ?>
      <p>
        <small>
          Data from <a href="http://vancouver.ca/" target="_blank">City of Vancouver</a>
        </small>
      </p>
    </div>
  </div>
  <?php endif; ?>
</section>

<?php require('../private/footer.php'); ?>
