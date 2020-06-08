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
  $parameters = [
    'page' => 'checkout_payment.php',
    'mode' => 'SSL',
  ];
  $OSCOM_Hooks->register_pipeline('loginRequired', $parameters);

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($_SESSION['cart']->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($_SESSION['cart']->cartID, $_SESSION['cartID'])) {
    if ($_SESSION['cart']->cartID != $_SESSION['cartID']) {
      tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
    }
  }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
  if (!isset($_SESSION['shipping'])) {
    tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
  }

  if (isset($_POST['payment'])) {
    $_SESSION['payment'] = $_POST['payment'];
  } elseif (!array_key_exists('payment', $_SESSION)) {
    $_SESSION['payment'] = null;
  }


  if (isset($_POST['comments']) && tep_not_null($_POST['comments'])) {
    $_SESSION['comments'] = tep_db_prepare_input($_POST['comments']);
  } elseif (!array_key_exists('comments', $_SESSION)) {
    $_SESSION['comments'] = null;
  }

// load the selected payment module
  $payment_modules = new payment($_SESSION['payment']);

  $order = new order();

  $payment_modules->update_status();

  if ( ($payment_modules->selected_module != $_SESSION['payment']) || ( is_array($payment_modules->modules) && (count($payment_modules->modules) > 1) && !is_object(${$_SESSION['payment']}) ) || !${$_SESSION['payment']}->enabled ) {
    tep_redirect(tep_href_link('checkout_payment.php', 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
  }

  if (is_array($payment_modules->modules)) {
    $payment_modules->pre_confirmation_check();
  }

// load the selected shipping module
  $shipping_modules = new shipping($shipping);

  $order_total_modules = new order_total();
  $order_total_modules->process();

  // Stock Check
  $any_out_of_stock = false;
  if (STOCK_CHECK == 'true') {
    foreach ($order->products as $product) {
      if (tep_check_stock($product['id'], $product['qty'])) {
        $any_out_of_stock = true;
      }
    }

    // Out of Stock
    if ( (STOCK_ALLOW_CHECKOUT != 'true') && $any_out_of_stock ) {
      tep_redirect(tep_href_link('shopping_cart.php'));
    }
  }

  require "includes/languages/$language/checkout_confirmation.php";

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
?>
