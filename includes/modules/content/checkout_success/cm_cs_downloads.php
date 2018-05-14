<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class cm_cs_downloads {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_TITLE;
      $this->description = MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_DESCRIPTION;

      if ( defined('MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $customer_id, $languages_id;

      if ( DOWNLOAD_ENABLED == 'true' ) {
        
// Get last order id for checkout_success
        $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where customers_id = '" . (int)$customer_id . "' order by orders_id desc limit 1");
        $orders = tep_db_fetch_array($orders_query);
        $last_order = $orders['orders_id'];
        
        
// Now get all downloadable products in that order
        $downloads_query = tep_db_query("select date_format(o.date_purchased, '%Y-%m-%d') as date_purchased_day, opd.download_maxdays, op.products_name, opd.orders_products_download_id, opd.orders_products_filename, opd.download_count, opd.download_maxdays from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " opd, " . TABLE_ORDERS_STATUS . " os where o.customers_id = '" . (int)$customer_id . "' and o.orders_id = '" . (int)$last_order . "' and o.orders_id = op.orders_id and op.orders_products_id = opd.orders_products_id and opd.orders_products_filename != '' and o.orders_status = os.orders_status_id and os.downloads_flag = '1' and os.language_id = '" . (int)$languages_id . "'");
        if (tep_db_num_rows($downloads_query) > 0) {
          $download_content =  '<table style="width:100%; margin-bottom:10px;">';

          while ($downloads = tep_db_fetch_array($downloads_query)) {
// MySQL 3.22 does not have INTERVAL
            list($dt_year, $dt_month, $dt_day) = explode('-', $downloads['date_purchased_day']);
            $download_timestamp = mktime(23, 59, 59, $dt_month, $dt_day + $downloads['download_maxdays'], $dt_year);
            $download_expiry = date('Y-m-d H:i:s', $download_timestamp);

      $download_content .=  '      <tr>' . "\n";

// The link will appear only if:
// - Download remaining count is > 0, AND
// - The file is present in the DOWNLOAD directory, AND EITHER
// - No expiry date is enforced (maxdays == 0), OR
// - The expiry date is not reached
      if ( ($downloads['download_count'] > 0) && (file_exists(DIR_FS_DOWNLOAD . $downloads['orders_products_filename'])) && ( ($downloads['download_maxdays'] == 0) || ($download_timestamp > time())) ) {
        $download_content .=  '        <td><a href="' . tep_href_link('download.php', 'order=' . $last_order . '&id=' . $downloads['orders_products_download_id']) . '">' . $downloads['products_name'] . '</a></td>' . "\n";
      } else {
        $download_content .=  '        <td>' . $downloads['products_name'] . '</td>' . "\n";
      }

      $download_content .=  '        <td>' . MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_DATE . tep_date_long($download_expiry) . '</td>' . "\n" .
           '        <td align="right">' . $downloads['download_count'] . TMODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_COUNT . '</td>' . "\n" .
           '      </tr>' . "\n";
    }

    $download_content .=  '</table>';



        
          ob_start();
          include('includes/modules/content/' . $this->group . '/templates/tpl_' . basename(__FILE__));
          $template = ob_get_clean();

          $oscTemplate->addContent($template, $this->group);
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Product Downloads Module', 'MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_STATUS', 'True', 'Should ordered product download links be shown on the checkout success page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_STATUS','MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_SORT_ORDER');
    }
  }
?>
