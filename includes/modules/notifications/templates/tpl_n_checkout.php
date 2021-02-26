<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

// let's start with the email confirmation
  echo STORE_NAME . "\n"
     . MODULE_NOTIFICATIONS_CHECKOUT_SEPARATOR . "\n"
     . MODULE_NOTIFICATIONS_CHECKOUT_TEXT_ORDER_NUMBER . ' ' . $order->get_id() . "\n"
     . MODULE_NOTIFICATIONS_CHECKOUT_TEXT_INVOICE_URL . ' '
     . tep_href_link('account_history_info.php', 'order_id=' . $order->get_id(), 'SSL', false) . "\n"
     . MODULE_NOTIFICATIONS_CHECKOUT_TEXT_DATE_ORDERED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";

  if (!empty($order->info['comments'])) {
    echo tep_db_output($order->info['comments']) . "\n";
  }

  echo MODULE_NOTIFICATIONS_CHECKOUT_TEXT_PRODUCTS . "\n"
     . MODULE_NOTIFICATIONS_CHECKOUT_SEPARATOR . "\n";
  foreach ($order->products as $product) {
    echo "\n" . $product['qty']
       . ' x ' . $product['name']
       . (empty($product['model']) ? '' : ' (' . $product['model'] . ')')
       . ' = ' . $GLOBALS['currencies']->display_price($product['final_price'], $product['tax'], $product['qty']);

//------insert customer chosen option to order--------
    foreach (($product['attributes'] ?? []) as $attribute) {
      echo "\n\t" . $attribute['option'] . ' ' . $attribute['value'];
    }
//------insert customer chosen option eof ----
  }
  echo "\n" . MODULE_NOTIFICATIONS_CHECKOUT_SEPARATOR . "\n";

  foreach ($order->totals as $order_total) {
    echo strip_tags($order_total['title']) . ' ' . strip_tags($order_total['text']) . "\n";
  }

  if ($order->content_type != 'virtual') {
    echo "\n" . MODULE_NOTIFICATIONS_CHECKOUT_TEXT_DELIVERY_ADDRESS . "\n"
       . MODULE_NOTIFICATIONS_CHECKOUT_SEPARATOR . "\n"
       . $customer->make_address_label($order->delivery, 0, '', "\n") . "\n";
  }

  echo "\n" . MODULE_NOTIFICATIONS_CHECKOUT_TEXT_BILLING_ADDRESS . "\n"
     . MODULE_NOTIFICATIONS_CHECKOUT_SEPARATOR . "\n"
     . $customer->make_address_label($order->billing, 0, '', "\n") . "\n\n";

  $payment = $GLOBALS[$_SESSION['payment']];
  if (is_object($payment)) {
    echo MODULE_NOTIFICATIONS_CHECKOUT_TEXT_PAYMENT_METHOD . "\n"
       . MODULE_NOTIFICATIONS_CHECKOUT_SEPARATOR . "\n";
    echo $order->info['payment_method'] . "\n\n";
    if (isset($payment->email_footer)) {
      echo $payment->email_footer . "\n\n";
    }
  }
?>
