<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
	
	class osC_Actions_notify_remove {
		public static function execute() {
			global $PHP_SELF, $messageStack, $customer_id, $navigation;
      
      if (tep_session_is_registered('customer_id') && isset($_GET['products_id'])) {
        $check_query = tep_db_query("select count(*) as count from products_notifications where products_id = '" . (int)$_GET['products_id'] . "' and customers_id = '" . (int)$customer_id . "'");
        $check = tep_db_fetch_array($check_query);
        if ($check['count'] > 0) {
          tep_db_query("delete from products_notifications where products_id = '" . (int)$_GET['products_id'] . "' and customers_id = '" . (int)$customer_id . "'");
        }
        $messageStack->add_session('product_action', sprintf(PRODUCT_UNSUBSCRIBED, tep_get_products_name((int)$_GET['products_id'])), 'warning');

        tep_redirect(tep_href_link($PHP_SELF, tep_get_all_get_params(array('action'))));
      } else {
        $navigation->set_snapshot();
        
        tep_redirect(tep_href_link('login.php', '', 'SSL'));
      }
		}
	}
