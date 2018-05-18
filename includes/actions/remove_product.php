<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  class osC_Actions_remove_product {
    public static function execute() {
      global $PHP_SELF, $messageStack, $cart, $goto, $parameters; 
      
      if (isset($_GET['products_id'])) {       
        $pid = $_GET['products_id'];
        
        $cart->remove($pid);
        
        $messageStack->add_session('product_action', sprintf(PRODUCT_REMOVED, tep_get_products_name($pid)), 'warning');
      }
      
      tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));      
    }
  }
