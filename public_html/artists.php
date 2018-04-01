<?php
/**
 * Artists
 */

require('../private/functions.php');

$page_id = 'artists';
$page_title = 'Artists' . get_site_title_suffix();
$limit = 10;
$db = db_connect();

// Get parameters
$search_query = $_GET['query'] ?? NULL;
$search_country_id = isset($_GET['country']) ? intval($_GET['country']) : NULL;
$search_country_name = '';
$sort_order = (isset($_GET['sort_order']) && $_GET['sort_order'] === 'desc') ? 'desc' : 'asc';
$page = max(isset($_GET['page']) ? intval($_GET['page']) : 1, 1);

require('../private/header.php');
?>

<section class="l-section l-wrap">
  <?php include_once('../private/notice.php'); ?>
  <h1>
    Artists
  </h1>
  <form method="get" class="drop">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="sort_order" value="<?php echo $sort_order; ?>">
    <div class="form-inline drop-sm">
      <input id="query" type="text" name="query" value="<?php echo get_sanitized_text($search_query); ?>" placeholder="Search artist by name..." class="fill-width form-inline__input">
      <input type="submit" value="Search" class="btn btn--primary">
    </div>
    <div class="form-select fill-width">
      <select name="country" class="select-filter">
        <option value="">Filter by country...</option>
        <?php
        // Populate country select box
        $res = $db->query("SELECT countryID, name FROM countries");
        while ($country = $res->fetch_assoc()):
          $id = $country['countryID'];
          $name = get_sanitized_text($country['name']);
          $selected = $search_country_id === intval($id);
          if ($selected) $search_country_name = $name;
        ?>
        <option value="<?php echo $id; ?>"<?php echo $selected ? 'selected' : ''; ?>><?php echo $name; ?></option>
        <?php endwhile; ?>
      </select>
    </div>
  </form>
  <?php
    // Prepare query
    $query = " FROM artists";
    $types = '';
    $params = [];
    $filter_desc = '';
    if ($search_country_id) {
      // Filter by country
      $query .= " WHERE countryID = ?";
      $types .= 'i';
      $params[] = &$search_country_id;
      $filter_desc = " from $search_country_name";
    }
    if ($search_query) {
      // Search by name
      $query .= (empty($types)) ? ' WHERE ' : ' AND ';
      $query .= " (firstName LIKE ? OR lastName LIKE ?)";
      $types .= 'ss';
      $match = '%' . $search_query . '%';
      $params[] = &$match;
      $params[] = &$match;
      $filter_desc = " with names containing '" . get_sanitized_text($search_query) . "'";
    }
    array_unshift($params, $types);
    $order_by = 'firstName, lastName ' . (($sort_order === 'asc') ? 'ASC' : 'DESC');

    // Get total rows and pages
    $stmt = $db->prepare("SELECT COUNT(*) $query");
    if (!empty($types)) call_user_func_array([ $stmt, 'bind_param' ], $params);
    $stmt->execute();
    $total_rows = $stmt->get_result()->fetch_row()[0];
    $total_pages = ceil($total_rows / $limit);
    $stmt->free_result();
    if ($total_rows < 1):
  ?>
  <p>
    No artists found.
  </p>
  <?php
    else:
    // Populate results
    $offset = $limit * ($page - 1);
    $stmt = $db->prepare("SELECT artistID, firstName, lastName, photoURL, countryID $query ORDER BY $order_by LIMIT $limit OFFSET $offset");
    if (!empty($types)) call_user_func_array([ $stmt, 'bind_param' ], $params);
    $stmt->execute();
    $res1 = $stmt->get_result();
    $num_rows = $res1->num_rows;
  ?>
  <div class="split drop">
    <div>
      Displaying <?php echo "$num_rows of $total_rows"; ?> artists<?php echo $filter_desc; ?>.
    </div>
    <div>
      <?php
        $url_params = $_GET;
        $url_params['sort_order'] = $sort_order === 'asc' ? 'desc' : 'asc';
        $sort = get_url_params($url_params);
      ?>
      Sort by
      <a href="<?php echo $sort; ?>" class="a-sort -active <?php if ($sort_order === 'desc') echo ' -alt'; ?>">Name</a>
    </div>
  </div>
  <ul class="list">
    <?php
      while ($artist = $res1->fetch_assoc()):
        // Get artist info
        $artist_id = $artist['artistID'];
        $artist_url = "artist.php?id=$artist_id";
        $photo_url = 'images/empty.php'; // TODO: $artist['photoURL'] ?? 'images/empty.png';
        $country_id = $artist['countryID'] ?? FALSE;
        $country = NULL;
        $name = get_artist_name($artist['firstName'], $artist['lastName']);
        // Get number of artworks
        $res2 = $db->query("SELECT COUNT(*) FROM artistArtworks WHERE artistID = $artist_id");
        $artworks = $res2->num_rows;
        if ($country_id) {
          // Get country name
          $res2 = $db->query("SELECT name FROM countries WHERE countryID = $country_id LIMIT 1");
          if ($res2->num_rows) {
            $countryName = get_sanitized_text($res2->fetch_assoc()['name']);
          }
        }
        $res2->free();
    ?>
    <li class="list__item">
      <a href="<?php echo $artist_url; ?>" class="list__thumbnail list__thumbnail--person" style="background-image:url('<?php echo $photo_url; ?>')"></a>
      <div class="list__text">
        <a href="<?php echo $artist_url; ?>" class="a-inherit">
          <strong><?php echo $name; ?></strong>
        </a><br>
        <small>
          <?php if (!empty($countryName)) echo 'From ' . $countryName . '.'; ?>
          Has <?php echo $artworks; ?> artwork<?php if ($artworks > 1) echo 's'; ?>.
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
