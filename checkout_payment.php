<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

// if the customer is not logged on, redirect them to the login page
  $OSCOM_Hooks->register_pipeline('loginRequired');

// if there is nothing in the customer's cart, redirect to the shopping cart page
  if ($_SESSION['cart']->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
  if (!isset($_SESSION['shipping'])) {
    tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
  }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($_SESSION['cart']->cartID, $_SESSION['cartID']) && ($_SESSION['cart']->cartID != $_SESSION['cartID'])) {
    tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
  }

// Stock Check
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    foreach ($_SESSION['cart']->get_products() as $product) {
      if (tep_check_stock($product['id'], $product['quantity'])) {
        tep_redirect(tep_href_link('shopping_cart.php'));
        break;
      }
    }
  }

  if (isset($_SESSION['billto'])) {
// verify the selected billing address
    if ( is_numeric($_SESSION['billto']) || ([] === $_SESSION['billto']) ) {
      $check_address_query = tep_db_query("SELECT COUNT(*) AS total FROM address_book WHERE customers_id = " . (int)$_SESSION['customer_id'] . " AND address_book_id = " . (int)$_SESSION['billto']);
      $check_address = tep_db_fetch_array($check_address_query);

      if ($check_address['total'] != '1') {
        $_SESSION['billto'] = $customer->get_default_address_id();
        unset($_SESSION['payment']);
      }
    }
  } else {
    // if no billing destination address was selected, use the customers own address as default
    $_SESSION['billto'] = $customer->get_default_address_id();
  }

  $order = new order();

  if (isset($_POST['comments']) && tep_not_null($_POST['comments'])) {
    $_SESSION['comments'] = tep_db_prepare_input($_POST['comments']);
  } elseif (!array_key_exists('comments', $_SESSION)) {
    $_SESSION['comments'] = null;
  }

  $total_weight = $_SESSION['cart']->show_weight();
  $total_count = $_SESSION['cart']->count_contents();

// load all enabled payment modules
  $payment_modules = new payment();

  require "includes/languages/$language/checkout_payment.php";

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
