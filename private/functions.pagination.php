<?php
/**
 * Pagination
 */

function show_pagination($page, $total, $params = array()) {
  if ($total < 1) return;
  if ($page > 1) {
    $params['page'] = $page - 1;
    $prev = get_url_params($params);
    $params['page'] = 1;
    $first = get_url_params($params);
  }
  if ($page < $total) {
    $params['page'] = $page + 1;
    $next = get_url_params($params);
    $params['page'] = $total;
    $last = get_url_params($params);
  }
?>
<ul class="pagination">
  <li>
    <a <?php echo empty($first) ? 'disabled' : "href=\"$first\""; ?> class="btn btn--small">&laquo;</a>
  </li>
  <li>
    <a <?php echo empty($prev) ? 'disabled' : "href=\"$prev\""; ?> class="btn btn--small">&lsaquo;</a>
  </li>
  <?php
    for ($i = 1; $i < ($total + 1); $i++):
      $params['page'] = $i;
      $url = get_url_params($params);
  ?>
  <li>
    <a href="<?php echo $url; ?>" class="btn btn--small<?php echo ($i === $page) ? ' -active' : ''; ?>"><?php echo $i; ?></a>
  </li>
  <?php endfor; ?>
  <li>
    <a <?php echo empty($next) ? 'disabled' : "href=\"$next\""; ?> class="btn btn--small">&rsaquo;</a>
  </li>
  <li>
    <a <?php echo empty($last) ? 'disabled' : "href=\"$last\""; ?> class="btn btn--small">&raquo;</a>
  </li>
</ul>
<?php } ?>
