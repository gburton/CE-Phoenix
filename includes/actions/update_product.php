<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
	
	class osC_Actions_update_product {
		public static function execute() {
			global $PHP_SELF, $messageStack, $cart, $goto, $parameters;
      
      if (isset($_POST['products_id'])) {
        $n=sizeof($_POST['products_id']);
        
        for ($i=0; $i<$n; $i++) {
          if (isset($_POST['cart_delete']) && in_array($_POST['products_id'][$i], (is_array($_POST['cart_delete']) ? $_POST['cart_delete'] : array()))) {
            $cart->remove($_POST['products_id'][$i]);
            $messageStack->add_session('product_action', sprintf(PRODUCT_REMOVED, tep_get_products_name($_POST['products_id'][$i])), 'warning');
          } else {
            $attributes = (isset($_POST['id']) && $_POST['id'][$_POST['products_id'][$i]]) ? $_POST['id'][$_POST['products_id'][$i]] : '';
            $cart->add_cart($_POST['products_id'][$i], $_POST['cart_quantity'][$i], $attributes, false);
          }
        }
      }
      
      tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));      
		}
	}
