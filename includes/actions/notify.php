<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class osC_Actions_notify {

    public static function execute() {
      if (!isset($_SESSION['customer_id'])) {
        $_SESSION['navigation']->set_snapshot();

        tep_redirect(tep_href_link('login.php', '', 'SSL'));
      }

      $notify = $_GET['products_id'] ?? $_GET['notify'] ?? $_POST['notify'];
      if (!is_null($notify)) {
        foreach ((array)$notify as $product_id) {
          tep_db_query("INSERT IGNORE INTO products_notifications (products_id, customers_id, date_added) VALUES (" . (int)$product_id . ", " . (int)$_SESSION['customer_id'] . ", NOW())");
          $GLOBALS['messageStack']->add_session('product_action', sprintf(PRODUCT_SUBSCRIBED, tep_get_products_name((int)$product_id)), 'success');
        }
      }

      tep_redirect(tep_href_link($GLOBALS['PHP_SELF'], tep_get_all_get_params(['action', 'notify'])));
    }

  }
