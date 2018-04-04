<?php
/**
 * Artworks
 */

require('../private/functions.php');

ensure_http();

$page_id = 'artworks';
$page_title = 'Artworks' . get_site_title_suffix();
$limit = 10;
$db = db_connect();

// Get parameters
$search_query = $_GET['query'] ?? NULL;
$search_status = isset($_GET['status']) ? $_GET['status'] : NULL;
if ($search_status === 'in_place') $search_status = 'In Place';
else if ($search_status === 'removed') $search_status = 'Removed';
$search_neighborhood_name = '';
$search_neighborhood_id = isset($_GET['neighborhood']) ? intval($_GET['neighborhood']) : NULL;
$search_neighborhood_name = '';
$search_owner_id = isset($_GET['owner']) ? intval($_GET['owner']) : NULL;
$search_owner_name = '';
$search_type_id = isset($_GET['type']) ? intval($_GET['type']) : NULL;
$search_type_name = '';
$sort = (isset($_GET['sort']) && $_GET['sort'] === 'year') ? 'year' : 'title';
$sort_order = (isset($_GET['sort_order']) && $_GET['sort_order'] === 'desc') ? 'desc' : 'asc';
$page = max(isset($_GET['page']) ? intval($_GET['page']) : 1, 1);

require('../private/header.php');
?>

<section class="l-section l-wrap">
  <?php show_global_notices(); ?>
  <h1>
    Artworks
  </h1>
  <form method="get" class="drop">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="sort" value="<?php echo $sort; ?>">
    <input type="hidden" name="sort_order" value="<?php echo $sort_order; ?>">
    <div class="form-inline drop-sm">
      <input id="query" type="text" name="query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search artwork by name..." class="fill-width form-inline__input">
      <input type="submit" value="Search" class="btn btn--primary">
    </div>
    <div class="row drop-sm">
      <div class="col col--6">
        <div class="form-select fill-width">
          <select name="status" class="select-filter">
            <option value="">Filter by status...</option>
            <?php
            // Populate status select box
            $statuses = [
              'in_place' => 'In Place',
              'removed' => 'Removed'
            ];
            foreach ($statuses as $key => $value):
              $selected = $search_status === $value;
            ?>
            <option value="<?php echo $key; ?>"<?php if ($selected) echo ' selected'; ?>><?php echo $value; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="col col--6">
        <div class="form-select">
          <select name="type" class="select-filter">
            <option value="">Filter by type...</option>
            <?php
            // Populate type select box
            $res = $db->query("SELECT typeID, type FROM types");
            while ($type = $res->fetch_assoc()):
              $id = intval($type['typeID']);
              $name = get_sanitized_text($type['type']);
              $selected = $search_type_id === intval($id);
              if ($selected) $search_type_name = get_artwork_type_plural($name);
            ?>
            <option value="<?php echo $id; ?>"<?php echo $selected ? 'selected' : ''; ?>><?php echo $name; ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
    </div>
    <div class="row drop-sm">
      <div class="col col--6">
        <div class="form-select fill-width">
          <select name="neighborhood" class="select-filter">
            <option value="">Filter by neighborhood...</option>
            <?php
            // Populate neighborhood select box
            $res = $db->query("SELECT neighborhoodID, name FROM neighborhoods");
            while ($neighborhood = $res->fetch_assoc()):
              $id = intval($neighborhood['neighborhoodID']);
              $name = get_sanitized_text($neighborhood['name']);
              $selected = $search_neighborhood_id === intval($id);
              if ($selected) $search_neighborhood_name = $name;
            ?>
            <option value="<?php echo $id; ?>"<?php echo $selected ? 'selected' : ''; ?>><?php echo $name; ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      <div class="col col--6">
        <div class="form-select">
          <select name="owner" class="select-filter">
            <option value="">Filter by owner...</option>
            <?php
            // Populate owner select box
            $res = $db->query("SELECT ownerID, name FROM owners");
            while ($owner = $res->fetch_assoc()):
              $id = intval($owner['ownerID']);
              $name = get_sanitized_text($owner['name']);
              $selected = $search_owner_id === intval($id);
              if ($selected) $search_owner_name = $name;
            ?>
            <option value="<?php echo $id; ?>"<?php echo $selected ? 'selected' : ''; ?>><?php echo $name; ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
    </div>
  </form>
  <?php
    // Prepare query
    $query = " FROM artworks";
    $types = '';
    $params = [];
    $filter_desc = '';
    if ($search_type_id) {
      // Filter by type
      $query .= " WHERE typeID = ?";
      $types .= 'i';
      $params[] = &$search_type_id;
      $filter_desc = " $search_type_name";
    } else {
      $filter_desc = ' artworks';
    }
    if ($search_status) {
      // Filter by status
      $query .= (empty($types)) ? ' WHERE ' : ' AND ';
      $query .= "status = ?";
      $types .= 's';
      $params[] = &$search_status;
      $filter_desc = ' that are ' . strtolower($search_status);
    }
    if ($search_neighborhood_id) {
      // Filter by neighborhood
      $query .= (empty($types)) ? ' WHERE ' : ' AND ';
      $query .= "neighborhoodID = ?";
      $types .= 'i';
      $params[] = &$search_neighborhood_id;
      $filter_desc = " in $search_neighborhood_name";
    }
    if ($search_owner_id) {
      // Filter by owner
      $query .= (empty($types)) ? ' WHERE ' : ' AND ';
      $query .= "ownerID = ?";
      $types .= 'i';
      $params[] = &$search_owner_id;
      $filter_desc = " owned by $search_owner_name";
    }
    if ($search_query) {
      // Search by name
      $query .= (empty($types)) ? ' WHERE ' : ' AND ';
      $query .= " title LIKE ?";
      $types .= 's';
      $match = '%' . $search_query . '%';
      $params[] = &$match;
      $filter_desc = " with title containing '" . get_sanitized_text($search_query) . "'";
    }
    array_unshift($params, $types);
    $order_by = (($sort === 'title') ? 'title ' : 'yearInstalled ') . (($sort_order === 'asc') ? 'ASC' : 'DESC');

    // Get total rows and pages
    $stmt = $db->prepare("SELECT COUNT(*) $query");
    if (!empty($types)) call_user_func_array([ $stmt, 'bind_param' ], $params);
    $stmt->execute();
    $total_rows = $stmt->get_result()->fetch_row()[0];
    $total_pages = ceil($total_rows / $limit);
    $stmt->free_result();
    if ($total_rows < 1): // Show no results
  ?>
  <p>
    No artworks found.
  </p>
  <?php
    else:
    // Fetch results
    $offset = $limit * ($page - 1);
    $stmt = $db->prepare("SELECT artworkID, title, status, yearInstalled, siteAddress, latitude, longitude, photoURL, neighborhoodID, ownerID, typeID
      $query
      ORDER BY $order_by
      LIMIT $limit
      OFFSET $offset");
    if (!empty($types)) call_user_func_array([ $stmt, 'bind_param' ], $params);
    $stmt->execute();
    $res1 = $stmt->get_result();
    $offset_start = $offset + 1;
    $offset_end = $offset + $res1->num_rows;
  ?>
  <div class="split drop">
    <div>
      Displaying <?php echo "$offset_start - $offset_end of $total_rows"; ?><?php echo $filter_desc; ?>.
    </div>
    <div>
      <?php
        // Show sorting options
        $url_params = $_GET;
        $url_params['sort'] = 'title';
        $url_params['sort_order'] = 'asc';
        $sort_order_alt = $sort_order === 'asc' ? 'desc' : 'asc';
        if ($sort === 'title') $url_params['sort_order'] = $sort_order_alt;
        $sort_title = get_url_params($url_params);
        $url_params['sort'] = 'year';
        $url_params['sort_order'] = 'asc';
        if ($sort === 'year') $url_params['sort_order'] = $sort_order_alt;
        $sort_year = get_url_params($url_params);
      ?>
      Sort by
      <a href="<?php echo $sort_title; ?>" class="a-sort <?php if ($sort === 'title') echo '-active'; if ($sort_order === 'desc') echo ' -alt'; ?>">Title</a>
      <a href="<?php echo $sort_year; ?>" class="a-sort <?php if ($sort === 'year') echo '-active'; if ($sort_order === 'desc') echo ' -alt'; ?>">Year Installed</a>
    </div>
  </div>
  <ul class="list">
    <?php
      // Populate results
      while ($artwork = $res1->fetch_assoc()):
        $artwork_id = intval($artwork['artworkID']);
        $url = "artwork.php?id=$artwork_id";
        $status = $artwork['status'];
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
          in <?php echo $year_installed; ?><?php echo ($status === 'Removed') ? ' (Removed)' : ''; ?>
        </small>
      </div>
    </li>
    <?php endwhile; ?>
  </ul>
  <?php
    $res1->free();
    endif;

    // Show pagination
    show_pagination($page, $total_pages, $_GET);
  ?>
</section>

<?php require('../private/footer.php'); ?>
