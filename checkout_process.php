<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  include 'includes/application_top.php';

// if the customer is not logged on, redirect them to the login page
  if (!isset($_SESSION['customer_id'])) {
    $navigation->set_snapshot(['mode' => 'SSL', 'page' => 'checkout_payment.php']);
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
  if (!isset($_SESSION['shipping']) || !isset($_SESSION['sendto'])) {
    tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
  }

  if ( (tep_not_null(MODULE_PAYMENT_INSTALLED)) && (!isset($_SESSION['payment'])) ) {
    tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
 }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($cart->cartID) && isset($_SESSION['cartID'])) {
    if ($cart->cartID != $cartID) {
      tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
    }
  }

  include "includes/languages/$language/checkout_process.php";

// load selected payment module
  $payment_modules = new payment($payment);

// load the selected shipping module
  $shipping_modules = new shipping($shipping);

  $order = new order();

// Stock Check
  if (STOCK_CHECK == 'true') {
    $any_out_of_stock = false;
    foreach ($order->products as $product) {
      if (tep_check_stock($product['id'], $product['qty'])) {
        $any_out_of_stock = true;
      }
    }

    // Out of Stock
    if ( (STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true) ) {
      tep_redirect(tep_href_link('shopping_cart.php'));
    }
  }

  $payment_modules->update_status();

  if ( ($payment_modules->selected_module != $payment)
    || ( is_array($payment_modules->modules) && (count($payment_modules->modules) > 1) && !is_object($$payment) )
    || (is_object($$payment) && (!$$payment->enabled)) )
  {
    tep_redirect(tep_href_link('checkout_payment.php', 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
  }

  $order_total_modules = new order_total();

  $order_totals = $order_total_modules->process();

// load the before_process function from the payment modules
  $payment_modules->before_process();

  require 'includes/modules/checkout/insert_order.php';

  $customer_notification = (SEND_EMAILS == 'true') ? '1' : '0';
  $sql_data = [
    'orders_id' => $order_id,
    'orders_status_id' => $order->info['order_status'],
    'date_added' => 'now()',
    'customer_notified' => $customer_notification,
    'comments' => $order->info['comments'],
  ];
  tep_db_perform('orders_status_history', $sql_data);

  tep_notify('checkout', $order);

// load the after_process function from the payment modules
  $payment_modules->after_process();

  require 'includes/modules/checkout/reset.php';

  tep_redirect(tep_href_link('checkout_success.php', '', 'SSL'));

  require 'includes/application_bottom.php';
