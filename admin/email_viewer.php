<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!function_exists('tep_address_label')) {
    function tep_address_label($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
      if (is_array($address_id) && !empty($address_id)) {
        return tep_address_format($address_id['address_format_id'], $address_id, $html, $boln, $eoln);
      }

      $address_query = tep_db_query("select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customers_id . "' and address_book_id = '" . (int)$address_id . "'");
      $address = tep_db_fetch_array($address_query);

      $format_id = tep_get_address_format_id($address['country_id']);

      return tep_address_format($format_id, $address, $html, $boln, $eoln);
    }
  }

  if (!function_exists('tep_get_address_format_id')) {
    function tep_get_address_format_id($country_id) {
      $address_format_query = tep_db_query("select address_format_id as format_id from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");
      if (tep_db_num_rows($address_format_query)) {
        $address_format = tep_db_fetch_array($address_format_query);
        return $address_format['format_id'];
      } else {
        return '1';
      }
    }
  }

  class payment_demo {
    var $title, $email_footer;

    function payment_demo() {
      $this->title = TITLE_PAYMENT_DEMO;
      $this->email_footer = TEXT_EMAIL_FOOTER_DEMO;
    }
  }

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'osc_template.php');
  $oscTemplate = new oscTemplate();

  $page = (isset($_GET['page']) ? $_GET['page'] : '');
  $mode = (isset($_GET['html']) ? 'html' : 'text');

  $last_order_query = tep_db_query("select max(orders_id) as last_orders_id from orders");
  $order_id = tep_db_fetch_array($last_order_query);

  $oID = $order_id['last_orders_id'];

  $template = '';

  // search in all class modules for preview
  $result = glob('.*/includes/modules/pages/tp_email_*.php');
  foreach ($result as $key => $filename) {
    include($filename);
    $template_page_class = str_replace('.php', '', basename($filename));

    if ( class_exists($template_page_class) && $page == $template_page_class) {
      $template_page = new $template_page_class();

      $template_page->preview();
      $template = $template_page->template;
      break;
    }
  }

  if ($mode == 'text') {
    $template = strip_tags($template);
    header("Content-Type: text/plain");
    echo "\xEF\xBB\xBF";
  }

  echo $template;

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
