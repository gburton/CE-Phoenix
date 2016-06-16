<?php
/*

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License

*/
?>
<?php
  $downloads_query_raw = "select distinct date_format(o.date_purchased, '%Y-%m-%d') as date_purchased_day, opd.download_maxdays, op.products_name,
      opd.orders_products_filename, opd.orders_products_download_id, opd.download_count, opd.download_maxdays from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " opd, " . TABLE_ORDERS_STATUS . " os 
      where o.orders_id = '" . (int)$oID . "' and o.orders_id = op.orders_id and op.orders_products_id = opd.orders_products_id and os.downloads_flag = '1'";
  $downloads_query = tep_db_query($downloads_query_raw);

  if (tep_db_num_rows($downloads_query) > 0) {
?>
  <div class="contentText col-sm-<?php echo (int)MODULE_CONTENT_ACCOUNT_HISTORY_INFO_DOWNLOADS_CONTENT_WIDTH; ?>">
  <h2><?php echo HEADING_DOWNLOAD; ?></h2>

    <div class="contentText">
      <table border="0" width="100%" cellspacing="1" cellpadding="2">

  <?php
      while ($downloads = tep_db_fetch_array($downloads_query)) {
  // MySQL 3.22 does not have INTERVAL
        list($dt_year, $dt_month, $dt_day) = explode('-', $downloads['date_purchased_day']);
        $download_timestamp = mktime(23, 59, 59, $dt_month, $dt_day + $downloads['download_maxdays'], $dt_year);
        $download_expiry = date('Y-m-d H:i:s', $download_timestamp);

        echo '      <tr>' . "\n";

  // The link will appear only if:
  // - Download remaining count is > 0, AND
  // - The file is present in the DOWNLOAD directory, AND EITHER
  // - No expiry date is enforced (maxdays == 0), OR
  // - The expiry date is not reached
        if ( ($downloads['download_count'] > 0) && (file_exists(DIR_FS_DOWNLOAD . $downloads['orders_products_filename'])) && ( ($downloads['download_maxdays'] == 0) || ($download_timestamp > time())) ) {
          echo '        <td> <div class="download-badge"><a href="' . tep_href_link(FILENAME_DOWNLOAD, 'order=' . $oID . '&id=' . $downloads['orders_products_download_id']) . '"><i class="fa fa-download"></i> ' . $downloads['products_name'] . '</a></div></td>' . "\n";
        } else {
          echo '        <td>' . $downloads['products_name'] . '</td>' . "\n";
        }

        echo '        <td>' . TABLE_HEADING_DOWNLOAD_DATE . tep_date_long($download_expiry) . '</td>' . "\n" .
             '        <td align="right">' . $downloads['download_count'] . TABLE_HEADING_DOWNLOAD_COUNT . '</td>' . "\n" .
             '      </tr>' . "\n";
      }
  ?>

      </table>
    </div>
  </div>

<?php
  }
?>
