<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  $log_query_raw = "select l.id, l.customers_id, l.module, l.action, l.result, l.ip_address, unix_timestamp(l.date_added) as date_added, c.customers_firstname, c.customers_lastname from oscom_app_paypal_log l left join customers c on (l.customers_id = c.customers_id) order by l.date_added desc";
  $log_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $log_query_raw, $log_query_numrows);
  $log_query = tep_db_query($log_query_raw);
?>

<h1 class="display-4"><?= $OSCOM_PayPal->getDef('heading_log'); ?></h1>

<table class="table table-hover">
  <thead class="thead-dark">
    <tr>
      <th colspan="2"><?= $OSCOM_PayPal->getDef('table_heading_action'); ?></th>
      <th><?= $OSCOM_PayPal->getDef('table_heading_ip'); ?></th>
      <th><?= $OSCOM_PayPal->getDef('table_heading_customer'); ?></th>
      <th colspan="2"><?= $OSCOM_PayPal->getDef('table_heading_date'); ?></th>
    </tr>
  </thead>
  <tbody>
<?php
  if ( tep_db_num_rows($log_query) > 0 ) {
    while ($log = tep_db_fetch_array($log_query)) {
      $customers_name = null;

      if ( $log['customers_id'] > 0 ) {
        $customers_name = trim($log['customers_firstname'] . ' ' . $log['customers_lastname']);

        if ( empty($customers_name) ) {
          $customers_name = '- ? -';
        }
      }
?>

    <tr>
      <td style="text-center"><span class="<?= ($log['result'] == '1') ? 'logSuccess' : 'logError'; ?>"><?= $log['module']; ?></span></td>
      <td><?= $log['action']; ?></td>
      <td><?= long2ip($log['ip_address']); ?></td>
      <td><?= (!empty($customers_name)) ? tep_output_string_protected($customers_name) : '<i>' . $OSCOM_PayPal->getDef('guest') . '</i>'; ?></td>
      <td><?= date(PHP_DATE_TIME_FORMAT, $log['date_added']); ?></td>
      <td class="pp-table-action text-right"><?= $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_view'), tep_href_link('paypal.php', 'action=log&page=' . $_GET['page'] . '&lID=' . $log['id'] . '&subaction=view'), 'info'); ?></td>
    </tr>

<?php
    }
  } else {
?>

    <tr>
      <td colspan="6"><?= $OSCOM_PayPal->getDef('no_entries'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

<div class="row my-1">
  <div class="col"><?= $log_split->display_count($log_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], $OSCOM_PayPal->getDef('listing_number_of_log_entries')); ?></div>
  <div class="col text-right mr-2"><?= $log_split->display_links($log_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], 'action=log'); ?></div>
</div>
