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

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($_SESSION['cart']->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

  if (isset($_SESSION['sendto'])) {
    if ( (is_numeric($_SESSION['sendto']) && empty($customer->fetch_to_address($_SESSION['sendto']))) || ([] === $_SESSION['sendto']) ) {
      $_SESSION['sendto'] = $customer->get('default_address_id');
      unset($_SESSION['shipping']);
    }
  } else {
    // if no shipping destination address was selected, use the customer's own address as default
    $_SESSION['sendto'] = $customer->get('default_address_id');
  }

  $order = new order();

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
  if (isset($_SESSION['cartID']) && ($_SESSION['cartID'] != $_SESSION['cart']->cartID)) {
    unset($_SESSION['shipping']);
  }

  $_SESSION['cartID'] = $_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
  if ($order->content_type == 'virtual') {
    $_SESSION['shipping'] = false;
    $_SESSION['sendto'] = false;
    tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
  }

  $total_weight = $_SESSION['cart']->show_weight();
  $total_count = $_SESSION['cart']->count_contents();

// load all enabled shipping modules
  $shipping_modules = new shipping();

  $free_shipping = ot_shipping::is_eligible_free_shipping($order->delivery['country_id'], $order->info['total']);

  $module_count = $shipping_modules->count();
// process the selected shipping method
  if (tep_validate_form_action_is('process')) {
    $shipping_modules->process_selection();
  }

// get all available shipping quotes
  $quotes = $shipping_modules->quote();

  shipping::ensure_enabled();

// if no shipping method has been selected, automatically select the cheapest method.
// if the module's status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
  if ( !isset($_SESSION['shipping']) || (!$_SESSION['shipping'] && ($module_count > 1)) ) {
    $_SESSION['shipping'] = $shipping_modules->cheapest();
  }

  require "includes/languages/$language/checkout_shipping.php";

  if ( defined('SHIPPING_ALLOW_UNDEFINED_ZONES') && (SHIPPING_ALLOW_UNDEFINED_ZONES == 'False') && !$_SESSION['shipping'] ) {
    $messageStack->add_session('checkout_address', ERROR_NO_SHIPPING_AVAILABLE_TO_SHIPPING_ADDRESS);
    tep_redirect(tep_href_link('checkout_shipping_address.php', '', 'SSL'));
  }

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
