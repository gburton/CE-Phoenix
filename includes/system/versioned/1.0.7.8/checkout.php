<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class Checkout {

    public function register_stages() {
      $stages = [];

      switch (pathinfo($GLOBALS['PHP_SELF'], PATHINFO_FILENAME)) {
        case 'checkout_process':
        case 'checkout_confirmation':
          $stages[] = 'checkout_confirmation_stage';
        case 'checkout_payment_address':
        case 'checkout_payment':
          $stages[] = 'checkout_payment_stage';
      }

      foreach (array_reverse($stages) as $stage) {
        $GLOBALS['hooks']->register_pipeline($stage);
      }
    }

    public function require_login() {
// if the customer is not logged on, redirect to the login page
      $parameters = [
        'page' => 'checkout_payment.php',
        'mode' => 'SSL',
      ];
      $GLOBALS['hooks']->register_pipeline('loginRequired', $parameters);
    }

    public function guarantee_cart() {
// if there is nothing in the customer's cart, redirect to the shopping cart page
      if ($_SESSION['cart']->count_contents() <= 0) {
        tep_redirect(tep_href_link('shopping_cart.php'));
      }
    }

    public function guarantee_cart_id() {
// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
      if (isset($_SESSION['cartID']) && ($_SESSION['cartID'] != $_SESSION['cart']->cartID)) {
        unset($_SESSION['shipping']);
      }
      
      $_SESSION['cartID'] = $_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();
    }

    public function validate_sendto() {
      global $customer;

      if (isset($_SESSION['sendto'])) {
        if ( (is_numeric($_SESSION['sendto']) && empty($customer->fetch_to_address($_SESSION['sendto']))) || ([] === $_SESSION['sendto']) ) {
          $_SESSION['sendto'] = $customer->get('default_sendto');
          unset($_SESSION['shipping']);
        }
      } else {
        // if no shipping destination address was selected, use the customer's own address as default
        $_SESSION['sendto'] = $customer->get('default_sendto');
      }
    }

    public function validate() {
// if no shipping method has been selected, redirect the customer to the shipping method selection page
// avoid hack attempts during the checkout procedure by checking the internal cartID
      if (!isset($_SESSION['shipping'], $_SESSION['sendto'], $_SESSION['cart']->cartID, $_SESSION['cartID'])
        || ($_SESSION['cart']->cartID !== $_SESSION['cartID']))
      {
        tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
      }
    }

    public function validate_billto() {
      global $customer;

      if (isset($_SESSION['billto'])) {
// verify the selected billing address
        if ( is_numeric($_SESSION['billto']) || ([] === $_SESSION['billto']) ) {
          $check_address_query = tep_db_query("SELECT COUNT(*) AS total FROM address_book WHERE customers_id = " . (int)$_SESSION['customer_id'] . " AND address_book_id = " . (int)$_SESSION['billto']);
          $check_address = tep_db_fetch_array($check_address_query);
          
          if ($check_address['total'] != '1') {
            $_SESSION['billto'] = $customer->get('default_billto');
            unset($_SESSION['payment']);
          }
        }
      } else {
// if no billing destination address was selected, use the customer's own address as default
        $_SESSION['billto'] = $customer->get('default_billto');
      }
    }

    public function validate_payment() {
      if ( (tep_not_null(MODULE_PAYMENT_INSTALLED)) && (!isset($_SESSION['payment'])) ) {
        tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
      }
    }

    public function skip_shipping() {
// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
      if ('virtual' === $GLOBALS['order']->content_type) {
        $_SESSION['shipping'] = false;
        $_SESSION['sendto'] = false;
        tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
      }
    }

    public function initialize_payment_modules() {
      $GLOBALS['payment_modules'] = new payment();
    }

    public function initialize_payment_module() {
      $GLOBALS['payment_modules'] = new payment($_SESSION['payment']);
    }

    public function initialize_shipping_module() {
// load the selected shipping module
      $GLOBALS['shipping_modules'] = new shipping($_SESSION['shipping']);
    }

    public function update_payment_module() {
      global $payment_modules;

      $payment_modules->update_status();

      if ( ($payment_modules->selected_module != $_SESSION['payment'])
        || ( is_array($payment_modules->modules) && (count($payment_modules->modules) > 1) && !is_object(${$_SESSION['payment']}) )
        || (is_object(${$_SESSION['payment']}) && (!${$_SESSION['payment']}->enabled)) )
      {
        tep_redirect(tep_href_link('checkout_payment.php', 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
      }
    }

    public function preconfirm_payment() {
      if (is_array($GLOBALS['payment_modules']->modules)) {
        $GLOBALS['payment_modules']->pre_confirmation_check();
      }
    }

    public function set_order_totals() {
      $GLOBALS['order_total_modules'] = new order_total();
      $GLOBALS['order']->totals = $GLOBALS['order_total_modules']->process();
    }

    public function prepare_payment() {
      $GLOBALS['payment_modules']->before_process();
    }

    public function notify() {
      $GLOBALS['customer_notification'] = tep_notify('checkout', $GLOBALS['order']) ? 1 : 0;
    }

    public function conclude_payment() {
      $GLOBALS['payment_modules']->after_process();
    }

    public function reset_cart() {
      $_SESSION['cart']->reset(true);
    }

    public function redirect_success() {
      tep_redirect(tep_href_link('checkout_success.php', '', 'SSL'));
    }

  }
