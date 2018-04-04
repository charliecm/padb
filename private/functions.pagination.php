<?php
/**
 * Pagination
 */

function show_pagination($page, $total, $params = array()) {
  $range = 2;
  $start = max($page - $range, 1);
  $end = min($page + $range + 1, $total);
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
    if ($start > 1):
      $params['page'] = 1;
      $url = get_url_params($params);
  ?>
  <li>
    <a href="<?php echo $url; ?>" class="btn btn--small">1</a>
  </li>
  <?php if ($start > 2): ?>
  <li>
    ...
  </li>
  <?php endif; ?>
  <?php endif; ?>
  <?php
    for ($i = $start; $i < $end; $i++):
      $params['page'] = $i;
      $url = get_url_params($params);
  ?>
  <li>
    <a href="<?php echo $url; ?>" class="btn btn--small<?php echo ($i === $page) ? ' -active' : ''; ?>"><?php echo $i; ?></a>
  </li>
  <?php endfor; ?>
  <?php
    if ($end <= $total):
      $params['page'] = $total;
      $url = get_url_params($params);
  ?>
  <?php if ($end < $total): ?>
  <li>
    ...
  </li>
  <?php endif; ?>
  <li>
    <a href="<?php echo $url; ?>" class="btn btn--small<?php echo ($i === $page) ? ' -active' : ''; ?>"><?php echo $total; ?></a>
  </li>
  <?php endif; ?>
  <li>
    <a <?php echo empty($next) ? 'disabled' : "href=\"$next\""; ?> class="btn btn--small">&rsaquo;</a>
  </li>
  <li>
    <a <?php echo empty($last) ? 'disabled' : "href=\"$last\""; ?> class="btn btn--small">&raquo;</a>
  </li>
</ul>
<?php } ?>
