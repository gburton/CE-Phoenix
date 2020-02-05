<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  if ( !class_exists('OSCOM_PayPal') ) {
    include DIR_FS_CATALOG . 'includes/apps/paypal/OSCOM_PayPal.php';
  }

  class paypal_pro_hs {

    const REQUIRES = [
      'firstname',
      'lastname',
      'street_address',
      'city',
      'postcode',
      'country',
      'telephone',
      'email_address',
    ];

    public $code = 'paypal_pro_hs';
    public $title, $description, $enabled;
    public $_app;

    function __construct() {
      global $order;

      $this->_app = new OSCOM_PayPal();
      $this->_app->loadLanguageFile('modules/HS/HS.php');

      $this->signature = 'paypal|paypal_pro_hs|' . $this->_app->getVersion() . '|2.3';
      $this->api_version = $this->_app->getApiVersion();

      $this->title = $this->_app->getDef('module_hs_title');
      $this->public_title = $this->_app->getDef('module_hs_public_title');
      $this->description = '<div align="center">' . $this->_app->drawButton($this->_app->getDef('module_hs_legacy_admin_app_button'), tep_href_link('paypal.php', 'action=configure&module=HS'), 'primary', null, true) . '</div>';
      $this->sort_order = defined('OSCOM_APP_PAYPAL_HS_SORT_ORDER') ? OSCOM_APP_PAYPAL_HS_SORT_ORDER : 0;
      $this->enabled = defined('OSCOM_APP_PAYPAL_HS_STATUS') && in_array(OSCOM_APP_PAYPAL_HS_STATUS, ['1', '0']);
      $this->order_status = defined('OSCOM_APP_PAYPAL_HS_PREPARE_ORDER_STATUS_ID') && ((int)OSCOM_APP_PAYPAL_HS_PREPARE_ORDER_STATUS_ID > 0) ? (int)OSCOM_APP_PAYPAL_HS_PREPARE_ORDER_STATUS_ID : 0;

      if ( defined('OSCOM_APP_PAYPAL_HS_STATUS') ) {
        if ( OSCOM_APP_PAYPAL_HS_STATUS == '0' ) {
          $this->title .= ' [Sandbox]';
          $this->public_title .= ' (' . $this->code . '; Sandbox)';
        }

        if ( OSCOM_APP_PAYPAL_HS_STATUS == '1' ) {
          $this->api_url = 'https://api-3t.paypal.com/nvp';
        } else {
          $this->api_url = 'https://api-3t.sandbox.paypal.com/nvp';
        }
      }

      if ( !function_exists('curl_init') ) {
        $this->description .= '<div class="secWarning">' . $this->_app->getDef('module_hs_error_curl') . '</div>';

        $this->enabled = false;
      }

      if ( $this->enabled === true ) {
        if ( (OSCOM_APP_PAYPAL_GATEWAY == '1') && !$this->_app->hasCredentials('HS') ) { // PayPal
          $this->description .= '<div class="secWarning">' . $this->_app->getDef('module_hs_error_credentials') . '</div>';

          $this->enabled = false;
        } elseif ( OSCOM_APP_PAYPAL_GATEWAY == '0' ) { // Payflow
          $this->description .= '<div class="secWarning">' . $this->_app->getDef('module_hs_error_payflow') . '</div>';

          $this->enabled = false;
        }
      }

      if ( $this->enabled === true ) {
        if ( isset($order) && is_object($order) ) {
          $this->update_status();
        }
      }
    }

    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)OSCOM_APP_PAYPAL_HS_ZONE > 0) ) {
        $check_query = tep_db_query("SELECT zone_id FROM zones_to_geo_zones WHERE geo_zone_id = '" . OSCOM_APP_PAYPAL_HS_ZONE . "' AND zone_country_id = '" . $order->billing['country']['id'] . "' ORDER BY zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if (($check['zone_id'] < 1) || ($check['zone_id'] == $order->billing['zone_id'])) {
            return;
          }
        }

        $this->enabled = false;
      }
    }

    function javascript_validation() {
      return false;
    }

    private function extract_order_id() {
      global $cart_PayPal_Pro_HS_ID;

      return substr($cart_PayPal_Pro_HS_ID, strpos($cart_PayPal_Pro_HS_ID, '-')+1);
    }

    function selection() {
      global $cart_PayPal_Pro_HS_ID;

      if (tep_session_is_registered('cart_PayPal_Pro_HS_ID')) {
        $order_id = $this->extract_order_id();

        $check_query = tep_db_query('SELECT orders_id FROM orders_status_history WHERE orders_id = ' . (int)$order_id . ' LIMIT 1');

        if (tep_db_num_rows($check_query) < 1) {
          tep_delete_order($order_id);
          tep_session_unregister('cart_PayPal_Pro_HS_ID');
        }
      }

      return [
        'id' => $this->code,
        'module' => $this->public_title,
      ];
    }

    function pre_confirmation_check() {
      global $cartID, $cart;

      if (empty($cart->cartID)) {
        $cartID = $cart->cartID = $cart->generate_cart_id();
      }

      if (!tep_session_is_registered('cartID')) {
        tep_session_register('cartID');
      }
    }

    function confirmation() {
      global $cartID, $cart_PayPal_Pro_HS_ID, $customer_id, $order, $order_total_modules, $currency, $sendto, $pphs_result, $pphs_key;

      $pphs_result = [];

      if (tep_session_is_registered('cartID')) {
        $insert_order = false;

        if (tep_session_is_registered('cart_PayPal_Pro_HS_ID')) {
          $order_id = $this->extract_order_id();

          $curr_check = tep_db_query("SELECT currency FROM orders WHERE orders_id = '" . (int)$order_id . "'");
          $curr = tep_db_fetch_array($curr_check);

          if ( ($curr['currency'] != $order->info['currency']) || ($cartID != substr($cart_PayPal_Pro_HS_ID, 0, strlen($cartID))) ) {
            $check_query = tep_db_query('SELECT orders_id FROM orders_status_history WHERE orders_id = ' . (int)$order_id . ' LIMIT 1');

            if (tep_db_num_rows($check_query) < 1) {
              tep_delete_order($order_id);
            }

            $insert_order = true;
          }
        } else {
          $insert_order = true;
        }

        if ($insert_order) {
          require 'includes/modules/checkout/build_order_totals.php';
          require 'includes/modules/checkout/insert_order.php';

          $cart_PayPal_Pro_HS_ID = $cartID . '-' . $order_id;
          tep_session_register('cart_PayPal_Pro_HS_ID');
        } else {
          $order_id = $this->extract_order_id();
        }

        $params = [
          'buyer_email' => $order->customer['email_address'],
          'cancel_return' => tep_href_link('checkout_payment.php', '', 'SSL'),
          'currency_code' => $currency,
          'invoice' => $order_id,
          'custom' => $customer_id,
          'paymentaction' => OSCOM_APP_PAYPAL_HS_TRANSACTION_METHOD == '1' ? 'sale' : 'authorization',
          'return' => tep_href_link('checkout_process.php', '', 'SSL'),
          'notify_url' => tep_href_link('ext/modules/payment/paypal/pro_hosted_ipn.php', '', 'SSL', false, false),
          'shipping' => $this->_app->formatCurrencyRaw($order->info['shipping_cost']),
          'tax' => $this->_app->formatCurrencyRaw($order->info['tax']),
          'subtotal' => $this->_app->formatCurrencyRaw($order->info['total'] - $order->info['shipping_cost'] - $order->info['tax']),
          'billing_first_name' => $order->billing['firstname'],
          'billing_last_name' => $order->billing['lastname'],
          'billing_address1' => $order->billing['street_address'],
          'billing_address2' => $order->billing['suburb'],
          'billing_city' => $order->billing['city'],
          'billing_state' => tep_get_zone_code($order->billing['country']['id'], $order->billing['zone_id'], $order->billing['state']),
          'billing_zip' => $order->billing['postcode'],
          'billing_country' => $order->billing['country']['iso_code_2'],
          'night_phone_b' => $order->customer['telephone'],
          'template' => 'templateD',
          'item_name' => STORE_NAME,
          'showBillingAddress' => 'false',
          'showShippingAddress' => 'false',
          'showHostedThankyouPage' => 'false',
        ];

        if ( is_numeric($sendto) && ($sendto > 0) ) {
          $params['address_override'] = 'true';
          $params['first_name'] = $order->delivery['firstname'];
          $params['last_name'] = $order->delivery['lastname'];
          $params['address1'] = $order->delivery['street_address'];
          $params['address2'] = $order->delivery['suburb'];
          $params['city'] = $order->delivery['city'];
          $params['state'] = tep_get_zone_code($order->delivery['country']['id'], $order->delivery['zone_id'], $order->delivery['state']);
          $params['zip'] = $order->delivery['postcode'];
          $params['country'] = $order->delivery['country']['iso_code_2'];
        }

        $return_link_title = $this->_app->getDef('module_hs_button_return_to_store', ['storename' => STORE_NAME]);

        if ( strlen($return_link_title) <= 60 ) {
          $params['cbt'] = $return_link_title;
        }

        $pphs_result = $this->_app->getApiResult('APP', 'BMCreateButton', $params, (OSCOM_APP_PAYPAL_HS_STATUS == '1') ? 'live' : 'sandbox');

        if ( !tep_session_is_registered('pphs_result') ) {
          tep_session_register('pphs_result');
        }
      }

      $pphs_key = tep_create_random_value(16);

      if ( !tep_session_is_registered('pphs_key') ) {
        tep_session_register('pphs_key');
      }

      $iframe_url = tep_href_link('ext/modules/payment/paypal/hosted_checkout.php', 'key=' . $pphs_key, 'SSL');
      $form_url = tep_href_link('checkout_payment.php', 'payment_error=paypal_pro_hs', 'SSL');

// include jquery if it doesn't exist in the template
      $output = <<<EOD
<iframe src="{$iframe_url}" width="570px" height="540px" frameBorder="0" scrolling="no"></iframe>
<script>
if ( typeof jQuery == 'undefined' ) {
  document.write('<scr' + 'ipt src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></scr' + 'ipt>');
}
</script>

<script>
$(function() {
  $('form[name="checkout_confirmation"] input[type="submit"], form[name="checkout_confirmation"] input[type="image"], form[name="checkout_confirmation"] button[type="submit"]').hide();
  $('form[name="checkout_confirmation"]').attr('action', '{$form_url}');
});
</script>
EOD;

      $confirmation = ['title' => $output];

      return $confirmation;
    }

    function process_button() {
      return false;
    }

    function before_process() {
      global $cart_PayPal_Pro_HS_ID, $customer_id, $pphs_result, $order, $order_totals, $sendto, $billto, $languages_id, $payment, $currencies, $cart, $$payment;

      $result = false;

      if ( isset($_GET['tx']) && !empty($_GET['tx']) ) { // direct payment (eg, credit card)
        $result = $this->_app->getApiResult('APP', 'GetTransactionDetails', ['TRANSACTIONID' => $_GET['tx']], (OSCOM_APP_PAYPAL_HS_STATUS == '1') ? 'live' : 'sandbox');
      } elseif ( isset($_POST['txn_id']) && !empty($_POST['txn_id']) ) { // paypal payment
        $result = $this->_app->getApiResult('APP', 'GetTransactionDetails', ['TRANSACTIONID' => $_POST['txn_id']], (OSCOM_APP_PAYPAL_HS_STATUS == '1') ? 'live' : 'sandbox');
      }

      if ( !in_array($result['ACK'], ['Success', 'SuccessWithWarning']) ) {
        tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . stripslashes($result['L_LONGMESSAGE0'])));
      }

      $order_id = $this->extract_order_id();

      $seller_accounts = [$this->_app->getCredentials('HS', 'email')];

      if ( tep_not_null($this->_app->getCredentials('HS', 'email_primary')) ) {
        $seller_accounts[] = $this->_app->getCredentials('HS', 'email_primary');
      }

      if ( !isset($result['RECEIVERBUSINESS']) || !in_array($result['RECEIVERBUSINESS'], $seller_accounts) || ($result['INVNUM'] != $order_id) || ($result['CUSTOM'] != $customer_id) ) {
        tep_redirect(tep_href_link('shopping_cart.php'));
      }

      $pphs_result = $result;

      $check_query = tep_db_query("SELECT orders_status FROM orders WHERE orders_id = '" . (int)$order_id . "' and customers_id = '" . (int)$customer_id . "'");

      $tx_order_id = $pphs_result['INVNUM'];
      $tx_customer_id = $pphs_result['CUSTOM'];

      if (!tep_db_num_rows($check_query) || ($order_id != $tx_order_id) || ($customer_id != $tx_customer_id)) {
        tep_redirect(tep_href_link('shopping_cart.php'));
      }

      $check = tep_db_fetch_array($check_query);

      $this->verifyTransaction();

      $new_order_status = DEFAULT_ORDERS_STATUS_ID;

      if ( $check['orders_status'] != OSCOM_APP_PAYPAL_HS_PREPARE_ORDER_STATUS_ID ) {
        $new_order_status = $check['orders_status'];
      }

      if ( (OSCOM_APP_PAYPAL_HS_ORDER_STATUS_ID > 0) && ($check['orders_status'] == OSCOM_APP_PAYPAL_HS_ORDER_STATUS_ID) ) {
        $new_order_status = OSCOM_APP_PAYPAL_HS_ORDER_STATUS_ID;
      }

      tep_db_query("UPDATE orders SET orders_status = " . (int)$new_order_status . ", last_modified = NOW() WHERE orders_id = " . (int)$order_id);

      $sql_data = [
        'orders_id' => $order_id,
        'orders_status_id' => (int)$new_order_status,
        'date_added' => 'NOW()',
        'customer_notified' => (SEND_EMAILS == 'true') ? '1' : '0',
        'comments' => $order->info['comments'],
      ];

      tep_db_perform('orders_status_history', $sql_data);

      tep_notify('checkout', $order);

// load the after_process function from the payment modules
      $this->after_process();

      require 'includes/modules/checkout/reset.php';

      tep_session_unregister('cart_PayPal_Pro_HS_ID');
      tep_session_unregister('pphs_result');
      tep_session_unregister('pphs_key');

      tep_redirect(tep_href_link('checkout_success.php', '', 'SSL'));
    }

    function after_process() {
      return false;
    }

    function get_error() {
      global $pphs_error_msg;

      $error = [
        'title' => $this->_app->getDef('module_hs_error_general_title'),
        'error' => $this->_app->getDef('module_hs_error_general'),
      ];

      if ( tep_session_is_registered('pphs_error_msg') ) {
        $error['error'] = $pphs_error_msg;

        tep_session_unregister('pphs_error_msg');
      }

      return $error;
    }

    function check() {
      $check_query = tep_db_query("SELECT configuration_value FROM configuration WHERE configuration_key = 'OSCOM_APP_PAYPAL_HS_STATUS'");
      if ( tep_db_num_rows($check_query) ) {
        $check = tep_db_fetch_array($check_query);

        return tep_not_null($check['configuration_value']);
      }

      return false;
    }

    function install() {
      tep_redirect(tep_href_link('paypal.php', 'action=configure&subaction=install&module=HS'));
    }

    function remove() {
      tep_redirect(tep_href_link('paypal.php', 'action=configure&subaction=uninstall&module=HS'));
    }

    function keys() {
      return ['OSCOM_APP_PAYPAL_HS_SORT_ORDER'];
    }

    function verifyTransaction($is_ipn = false) {
      global $pphs_result, $currencies;

      $tx_order_id = $pphs_result['INVNUM'];
      $tx_customer_id = $pphs_result['CUSTOM'];
      $tx_transaction_id = $pphs_result['TRANSACTIONID'];
      $tx_payment_status = $pphs_result['PAYMENTSTATUS'];
      $tx_payment_type = $pphs_result['PAYMENTTYPE'];
      $tx_payer_status = $pphs_result['PAYERSTATUS'];
      $tx_address_status = $pphs_result['ADDRESSSTATUS'];
      $tx_amount = $pphs_result['AMT'];
      $tx_currency = $pphs_result['CURRENCYCODE'];
      $tx_pending_reason = (isset($pphs_result['PENDINGREASON'])) ? $pphs_result['PENDINGREASON'] : null;

      if ( is_numeric($tx_order_id) && ($tx_order_id > 0) && is_numeric($tx_customer_id) && ($tx_customer_id > 0) ) {
        $order_query = tep_db_query("SELECT orders_id, orders_status, currency, currency_value FROM orders WHERE orders_id = '" . (int)$tx_order_id . "' and customers_id = '" . (int)$tx_customer_id . "'");

        if ( tep_db_num_rows($order_query) === 1 ) {
          $order = tep_db_fetch_array($order_query);

          $new_order_status = DEFAULT_ORDERS_STATUS_ID;

          if ( $order['orders_status'] != OSCOM_APP_PAYPAL_HS_PREPARE_ORDER_STATUS_ID ) {
            $new_order_status = $order['orders_status'];
          }

          $total_query = tep_db_query("SELECT value FROM orders_total WHERE orders_id = '" . (int)$order['orders_id'] . "' and class = 'ot_total' limit 1");
          $total = tep_db_fetch_array($total_query);

          $comment_status = 'Transaction ID: ' . tep_output_string_protected($tx_transaction_id) . "\n"
                          . 'Payer Status: ' . tep_output_string_protected($tx_payer_status) . "\n"
                          . 'Address Status: ' . tep_output_string_protected($tx_address_status) . "\n"
                          . 'Payment Status: ' . tep_output_string_protected($tx_payment_status) . "\n"
                          . 'Payment Type: ' . tep_output_string_protected($tx_payment_type) . "\n"
                          . 'Pending Reason: ' . tep_output_string_protected($tx_pending_reason);

          if ( $tx_amount != $this->_app->formatCurrencyRaw($total['value'], $order['currency'], $order['currency_value']) ) {
            $comment_status .= "\n" . 'OSCOM Error Total Mismatch: PayPal transaction value (' . tep_output_string_protected($tx_amount) . ') does not match order value (' . $this->_app->formatCurrencyRaw($total['value'], $order['currency'], $order['currency_value']) . ')';
          } elseif ( $tx_payment_status == 'Completed' ) {
            $new_order_status = (OSCOM_APP_PAYPAL_HS_ORDER_STATUS_ID > 0 ? OSCOM_APP_PAYPAL_HS_ORDER_STATUS_ID : $new_order_status);
          }

          tep_db_query("UPDATE orders SET orders_status = '" . (int)$new_order_status . "', last_modified = NOW() WHERE orders_id = " . (int)$order['orders_id']);

          if ( $is_ipn === true ) {
            $comment_status .= "\n" . 'Source: IPN';
          }

          $sql_data = [
            'orders_id' => (int)$order['orders_id'],
            'orders_status_id' => OSCOM_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID,
            'date_added' => 'NOW()',
            'customer_notified' => '0',
            'comments' => $comment_status,
          ];

          tep_db_perform('orders_status_history', $sql_data);
        }
      }
    }
  }
