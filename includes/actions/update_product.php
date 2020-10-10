<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
	
  class osC_Actions_update_product {

    public static function execute() {
      global $messageStack, $goto, $parameters;

      foreach (($_POST['products_id'] ?? []) as $i => $product_id) {
        if (in_array($product_id, ($_POST['cart_delete'] ?? []))) {
          $_SESSION['cart']->remove($product_id);
          $messageStack->add_session('product_action', sprintf(PRODUCT_REMOVED, tep_get_products_name($product_id)), 'warning');
        } else {
          $attributes = $_POST['id'][$product_id] ?? null;
          $_SESSION['cart']->add_cart($product_id, $_POST['cart_quantity'][$i], $attributes, false);
        }
      }

      tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));      
    }

  }
