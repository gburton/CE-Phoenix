<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class Login {

    public static function add_customer_id() {
      $GLOBALS['customer'] = new customer($GLOBALS['customer_data']->get('id', $GLOBALS['customer_details']));
      $_SESSION['customer_id'] = $GLOBALS['customer']->get_id();
      $GLOBALS['customer_id'] =& $_SESSION['customer_id'];
    }

    public static function set_customer_id() {
      $_SESSION['customer_id'] = $GLOBALS['login_customer_id'];
    }

    public static function log() {
      tep_db_query("UPDATE customers_info SET customers_info_date_of_last_logon = NOW(), customers_info_number_of_logons = customers_info_number_of_logons+1, password_reset_key = null, password_reset_date = null WHERE customers_info_id = " . (int)$_SESSION['customer_id']);
    }

    public static function notify() {
      tep_notify('create_account', $GLOBALS['customer']);
    }

    public static function redirect_success() {
      tep_redirect(tep_href_link('create_account_success.php'));
    }

    public static function hook() {
      $GLOBALS['hooks']->call('siteWide', 'postLogin');
    }

  }
