<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  if (strstr($PHP_SELF, 'account_history_info.php')) {
    $last_order = $_GET['order_id'];
  } else {
// Get last order id for checkout_success
    $orders_query = tep_db_query("SELECT orders_id FROM orders WHERE customers_id = " . (int)$_SESSION['customer_id'] . " ORDER BY orders_id DESC LIMIT 1");
    $orders = tep_db_fetch_array($orders_query);
    $last_order = $orders['orders_id'];
  }

// Now get all downloadable products in that order
  $downloads_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT
    date_format(o.date_purchased, '%%Y-%%m-%%d') AS date_purchased_day,
    opd.download_maxdays,
    op.products_name,
    opd.orders_products_download_id,
    opd.orders_products_filename,
    opd.download_count,
    opd.download_maxdays
 FROM orders o
   INNER JOIN orders_products op ON o.orders_id = op.orders_id
   INNER JOIN orders_products_download opd ON op.orders_products_id = opd.orders_products_id
   INNER JOIN orders_status os ON o.orders_status = os.orders_status_id
 WHERE opd.orders_products_filename != ''
   AND os.downloads_flag = 1
   AND o.customers_id = %d
   AND o.orders_id = %d
   AND os.language_id = %d
EOSQL
    , (int)$_SESSION['customer_id'], (int)$last_order, (int)$_SESSION['languages_id']));
  if (tep_db_num_rows($downloads_query) > 0) {
?>

  <h3><?php echo HEADING_DOWNLOAD; ?></h3>

  <div class="contentText">
    <table class="table table-striped">

<?php
    while ($downloads = tep_db_fetch_array($downloads_query)) {
// MySQL 3.22 does not have INTERVAL
      list($dt_year, $dt_month, $dt_day) = explode('-', $downloads['date_purchased_day']);
      $download_timestamp = mktime(23, 59, 59, $dt_month, $dt_day + $downloads['download_maxdays'], $dt_year);
      $download_expiry = date('Y-m-d H:i:s', $download_timestamp);

      echo '<tr>';

// The link will appear only if:
// - Download remaining count is > 0, AND
// - The file is present in the DOWNLOAD directory, AND EITHER
// - No expiry date is enforced (maxdays == 0), OR
// - The expiry date is not reached
        if ( ($downloads['download_count'] > 0) && (file_exists(DIR_FS_DOWNLOAD . $downloads['orders_products_filename'])) && ( ($downloads['download_maxdays'] == 0) || ($download_timestamp > time())) ) {
          echo '<td><a href="' . tep_href_link('download.php', 'order=' . $last_order . '&id=' . $downloads['orders_products_download_id']) . '">' . $downloads['products_name'] . '</a></td>' . "\n";
        } else {
          echo '<td>' . $downloads['products_name'] . '</td>' . "\n";
        }

        echo '<td>' . TABLE_HEADING_DOWNLOAD_DATE . tep_date_long($download_expiry) . '</td>';
        echo '<td class="text-right">' . $downloads['download_count'] . TABLE_HEADING_DOWNLOAD_COUNT . '</td>';
      echo '</tr>';
    }
?>

    </table>

<?php
    if (!strstr($PHP_SELF, 'account_history_info.php')) {
?>

    <p><?php printf(FOOTER_DOWNLOAD, '<a href="' . tep_href_link('account.php', '', 'SSL') . '">' . HEADER_TITLE_MY_ACCOUNT . '</a>'); ?></p>

<?php
    }
?>

  </div>

<?php
  }
?>
