<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  chdir('../../../../');
  require 'includes/application_top.php';

  if ( !defined('OSCOM_APP_PAYPAL_PS_STATUS') || !in_array(OSCOM_APP_PAYPAL_PS_STATUS, ['1', '0']) ) {
    exit;
  }

  require 'includes/modules/payment/paypal_standard.php';

  $payment = 'paypal_standard';
  $$payment = new paypal_standard();

  require DIR_FS_CATALOG . "includes/languages/$language/checkout_process.php";

  $result = false;

  $seller_accounts = [$$payment->_app->getCredentials('PS', 'email')];

  if ( tep_not_null($$payment->_app->getCredentials('PS', 'email_primary')) ) {
    $seller_accounts[] = $$payment->_app->getCredentials('PS', 'email_primary');
  }

  if ( (isset($_POST['receiver_email']) && in_array($_POST['receiver_email'], $seller_accounts)) || (isset($_POST['business']) && in_array($_POST['business'], $seller_accounts)) ) {
    $parameters = 'cmd=_notify-validate&';

    foreach ( $_POST as $key => $value ) {
      if ( $key != 'cmd' ) {
        $parameters .= $key . '=' . urlencode(stripslashes($value)) . '&';
      }
    }

    $parameters = substr($parameters, 0, -1);

    $result = $$payment->_app->makeApiCall($$payment->form_action_url, $parameters);
  }

  $log_params = [];

  foreach ( $_POST as $key => $value ) {
    $log_params[$key] = stripslashes($value);
  }

  foreach ( $_GET as $key => $value ) {
    $log_params['GET ' . $key] = stripslashes($value);
  }

  $$payment->_app->log('PS', '_notify-validate', ($result == 'VERIFIED') ? 1 : -1, $log_params, $result, (OSCOM_APP_PAYPAL_PS_STATUS == '1') ? 'live' : 'sandbox', true);

  if ( $result == 'VERIFIED' ) {
    $$payment->verifyTransaction($_POST, true);

    $order_id = (int)$_POST['invoice'];
    $customer_id = (int)$_POST['custom'];
    if (!($customer instanceof customer)) {
      $customer = new customer($customer_id);
    }

    $check_query = tep_db_query("SELECT orders_status FROM orders WHERE orders_id = " . (int)$order_id . " AND customers_id = " . (int)$customer_id);

    if ($check = tep_db_fetch_array($check_query)) {
      if ( $check['orders_status'] == OSCOM_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID ) {
        $new_order_status = DEFAULT_ORDERS_STATUS_ID;

        if ( OSCOM_APP_PAYPAL_PS_ORDER_STATUS_ID > 0 ) {
          $new_order_status = OSCOM_APP_PAYPAL_PS_ORDER_STATUS_ID;
        }

        tep_db_query("UPDATE orders SET orders_status = " . (int)$new_order_status . ", last_modified = NOW() WHERE orders_id = " . (int)$order_id);

        $sql_data = [
          'orders_id' => $order_id,
          'orders_status_id' => (int)$new_order_status,
          'date_added' => 'NOW()',
          'customer_notified' => (SEND_EMAILS == 'true') ? '1' : '0',
          'comments' => '',
        ];

        tep_db_perform('orders_status_history', $sql_data);

        $order = new order($order_id);

        if (DOWNLOAD_ENABLED == 'true') {
          $downloads_query = tep_db_query("SELECT opd.orders_products_filename FROM orders o, orders_products op, orders_products_download opd WHERE o.orders_id = " . (int)$order_id . " AND o.customers_id = " . (int)$customer_id . " AND o.orders_id = op.orders_id AND op.orders_products_id = opd.orders_products_id AND opd.orders_products_filename != ''");

          switch (tep_db_num_rows($downloads_query)) {
            case 0:
              $order->content_type = 'physical';
              break;
            case count($order->products):
              $order->content_type = 'virtual';
              break;
            default:
              $order->content_type = 'mixed';
          }
        } else {
          $order->content_type = 'physical';
        }

// let's start with the email confirmation
        tep_notify('checkout', $order);

        tep_db_query("DELETE FROM customers_basket WHERE customers_id = " . (int)$customer_id);
        tep_db_query("DELETE FROM customers_basket_attributes WHERE customers_id = " . (int)$customer_id);
      }
    }
  }

  require 'includes/application_bottom.php';
