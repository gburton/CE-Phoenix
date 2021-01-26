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

  if ( !defined('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_STATUS') || (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_STATUS  != 'True') ) {
    exit();
  }

  if (isset($_SESSION['language'])) {
    if ($_SESSION['language'] != $_POST['M_lang']) {
// bypass autoloader's language selection by loading language and module files manually
      include language::map_to_translation('/modules/payment/rbsworldpay_hosted.php', basename($_POST['M_lang']));
      include 'includes/modules/payment/rbsworldpay_hosted.php';
    }
  } elseif (isset($lng->catalog_languages[$_POST['M_lang']])) {
    $_SESSION['language'] = $_POST['M_lang'];
  }

  $rbsworldpay_hosted = new rbsworldpay_hosted();

  $error = false;

  if ( is_null($_GET['installation'] ?? $_POST['installation'] ?? null) || (($_GET['installation'] ?? $_POST['installation']) != MODULE_PAYMENT_RBSWORLDPAY_HOSTED_INSTALLATION_ID) ) {
    $error = true;
  } elseif ( !Text::is_empty(MODULE_PAYMENT_RBSWORLDPAY_HOSTED_CALLBACK_PASSWORD) && (!isset($_POST['callbackPW']) || ($_POST['callbackPW'] != MODULE_PAYMENT_RBSWORLDPAY_HOSTED_CALLBACK_PASSWORD)) ) {
    $error = true;
  } elseif ( !isset($_POST['transStatus']) || ($_POST['transStatus'] != 'Y') ) {
    $error = true;
  } elseif ( !isset($_POST['M_hash'], $_POST['M_sid'], $_POST['M_cid'], $_POST['cartId'], $_POST['M_lang'], $_POST['amount']) || ($_POST['M_hash'] != md5($_POST['M_sid'] . $_POST['M_cid'] . $_POST['cartId'] . $_POST['M_lang'] . number_format($_POST['amount'], 2) . MODULE_PAYMENT_RBSWORLDPAY_HOSTED_MD5_PASSWORD)) ) {
    $error = true;
  }

  if ( !$error ) {
    $order_query = tep_db_query("SELECT orders_id, orders_status, currency, currency_value FROM orders WHERE orders_id = " . (int)$_POST['cartId'] . " AND customers_id = " . (int)$_POST['M_cid']);

    if (!mysqli_num_rows($order_query)) {
      $error = true;
    }
  }

  if ( $error ) {
    $rbsworldpay_hosted->sendDebugEmail();

    exit();
  }

  $order = $order_query->fetch_assoc();

  if ($order['orders_status'] == MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID) {
    $order_status_id = (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ORDER_STATUS_ID > 0 ? (int)MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ORDER_STATUS_ID : (int)DEFAULT_ORDERS_STATUS_ID);

    tep_db_query("UPDATE orders SET orders_status = " . (int)$order_status_id . ", last_modified = NOW() WHERE orders_id = " . (int)$order['orders_id']);

    $sql_data = [
      'orders_id' => $order['orders_id'],
      'orders_status_id' => $order_status_id,
      'date_added' => 'NOW()',
      'customer_notified' => '0',
      'comments' => '',
    ];

    tep_db_perform('orders_status_history', $sql_data);
  }

  $trans_result = 'WorldPay: Transaction Verified (Callback)' . "\n" .
                  'Transaction ID: ' . $_POST['transId'];

  if (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TESTMODE == 'True') {
    $trans_result .= "\n" . MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TEXT_WARNING_DEMO_MODE;
  }

  $sql_data = [
    'orders_id' => $order['orders_id'],
    'orders_status_id' => MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TRANSACTIONS_ORDER_STATUS_ID,
    'date_added' => 'NOW()',
    'customer_notified' => '0',
    'comments' => $trans_result,
  ];

  tep_db_perform('orders_status_history', $sql_data);
  require $oscTemplate->map_to_template(__FILE__, 'ext');
  tep_session_destroy();
