<?php
/*

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License

*/
?>
  <div class="contentText col-sm-<?php echo (int)MODULE_CONTENT_ACCOUNT_HISTORY_INFO_STATUSES_CONTENT_WIDTH; ?>">
  <h2><?php echo HEADING_ORDER_HISTORY; ?></h2>
    <ul class="timeline">
      <?php
        $statuses_query_raw = "select os.orders_status_name, osh.date_added, osh.comments from " . TABLE_ORDERS_STATUS . " os, " . TABLE_ORDERS_STATUS_HISTORY . " osh where osh.orders_id = '" . $oID . "' and osh.orders_status_id = os.orders_status_id and os.language_id = '" . (int)$languages_id . "' and os.public_flag = '1' order by osh.date_added";
        $statuses_query = tep_db_query($statuses_query_raw);
        while ($statuses = tep_db_fetch_array($statuses_query)) {
        echo '<li>';
        echo '  <div class="timeline-badge"><i class="fa fa-check-square-o"></i></div>';
        echo '  <div class="timeline-panel">';
        echo '    <div class="timeline-heading">';
        echo '      <p class="pull-right"><small class="text-muted"><i class="fa fa-clock-o"></i> ' . tep_date_short($statuses['date_added']) . '</small></p><h2 class="timeline-title">' . $statuses['orders_status_name'] . '</h2>';
        echo '    </div>';
        echo '    <div class="timeline-body">';
        echo '      <p>' . (empty($statuses['comments']) ? '&nbsp;' : '<blockquote>' . nl2br(tep_output_string_protected($statuses['comments'])) . '</blockquote>') . '</p>';
        echo '    </div>';
        echo '  </div>';
        echo '</li>';
      }
      ?>
    </ul>
  </div>
