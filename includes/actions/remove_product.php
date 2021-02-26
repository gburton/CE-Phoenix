<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class osC_Actions_remove_product {

    public static function execute() {
      global $messageStack, $goto, $parameters;

      if (isset($_GET['products_id'])) {
        $_SESSION['cart']->remove($_GET['products_id']);

        $GLOBALS['messageStack']->add_session('product_action', sprintf(PRODUCT_REMOVED, tep_get_products_name($_GET['products_id'])), 'warning');
      }

      tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
    }

  }
