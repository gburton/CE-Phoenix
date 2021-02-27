<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

// Shopping cart actions
  if (isset($_GET['action'])) {
// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled
    if (!$session_started) {
      tep_redirect(tep_href_link('cookie_usage.php'));
    }

    if (DISPLAY_CART == 'true') {
      $goto = 'shopping_cart.php';
      $parameters = ['action', 'cPath', 'products_id', 'pid'];
    } else {
      $goto = $PHP_SELF;
      if ($_GET['action'] == 'buy_now') {
        $parameters = ['action', 'pid', 'products_id'];
      } else {
        $parameters = ['action', 'pid'];
      }
    }

    include 'includes/classes/actions.php';
    osC_Actions::parse($_GET['action']);
  }
