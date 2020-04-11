<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
	
  class osC_Actions_notify_remove {

    public static function execute() {
      global $PHP_SELF, $messageStack;

      if (!isset($_SESSION['customer_id'])) {
        $_SESSION['navigation']->set_snapshot();

        tep_redirect(tep_href_link('login.php', '', 'SSL'));
      }

      if (isset($_GET['products_id'])) {
        tep_db_query("DELETE FROM products_notifications WHERE products_id = " . (int)$_GET['products_id'] . " AND customers_id = " . (int)$_SESSION['customer_id']);
        $messageStack->add_session('product_action', sprintf(PRODUCT_UNSUBSCRIBED, tep_get_products_name((int)$_GET['products_id'])), 'warning');

        tep_redirect(tep_href_link($PHP_SELF, tep_get_all_get_params(['action'])));
      }
    }

  }
