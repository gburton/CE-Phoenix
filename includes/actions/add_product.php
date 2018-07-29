<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
	
	class osC_Actions_add_product {
		public static function execute() {
			global $PHP_SELF, $messageStack, $cart, $goto, $parameters;
      
      if (isset($_POST['products_id'])) {       
        $pid = (int)$_POST['products_id'];        
        $attributes = isset($_POST['id']) ? $_POST['id'] : '';
        
        // php 5
        $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
        // php 7
        // $qty = (int)($_POST['qty'] ?? 1);
        
        $cart->add_cart($_POST['products_id'], $cart->get_quantity(tep_get_uprid($pid, $attributes))+$qty, $attributes);
        
        $messageStack->add_session('product_action', sprintf(PRODUCT_ADDED, tep_get_products_name((int)$_POST['products_id'])), 'success');
      }
      
      tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));      
		}
	}
