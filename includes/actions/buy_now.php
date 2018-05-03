<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
	
	class osC_Actions_buy_now {
		public static function execute() {
			global $PHP_SELF, $messageStack, $cart, $goto, $parameters;
      
      if (isset($_GET['products_id'])) {       
        $pid = (int)$_GET['products_id'];
        
        if (tep_has_product_attributes($pid)) {
          tep_redirect(tep_href_link('product_info.php', 'products_id=' . $pid));
        } else {
          $cart->add_cart($pid, $cart->get_quantity($pid)+1);
          
          $messageStack->add_session('product_action', sprintf(PRODUCT_ADDED, tep_get_products_name($pid)), 'success');
        }        
      }
      
      tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));      
		}
	}
