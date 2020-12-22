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
        $this->description .= '<div class="alert alert-warning">' . $this->_app->getDef('module_hs_error_curl') . '</div>';

        $this->enabled = false;
      }

      if ( $this->enabled === true ) {
        if ( (OSCOM_APP_PAYPAL_GATEWAY == '1') && !$this->_app->hasCredentials('HS') ) { // PayPal
          $this->description .= '<div class="alert alert-warning">' . $this->_app->getDef('module_hs_error_credentials') . '</div>';

          $this->enabled = false;
        } elseif ( OSCOM_APP_PAYPAL_GATEWAY == '0' ) { // Payflow
          $this->description .= '<div class="alert alert-warning">' . $this->_app->getDef('module_hs_error_payflow') . '</div>';

          $this->enabled = false;
        }
      }

      if ( $this->enabled === true ) {
        if ( isset($order->billing) ) {
          $this->update_status();
        }
      }
    }

    function update_status() {
      global $order;

      if ( $this->enabled && ((int)OSCOM_APP_PAYPAL_HS_ZONE > 0) ) {
        $check_query = tep_db_query("SELECT zone_id FROM zones_to_geo_zones WHERE geo_zone_id = '" . (int)OSCOM_APP_PAYPAL_HS_ZONE . "' AND zone_country_id = '" . (int)$GLOBALS['customer_data']->get('country_id', $order->billing) . "' ORDER BY zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if (($check['zone_id'] < 1) || ($check['zone_id'] === $GLOBALS['customer_data']->get('zone_id', $order->billing))) {
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
      return substr($_SESSION['cart_PayPal_Pro_HS_ID'], strpos($_SESSION['cart_PayPal_Pro_HS_ID'], '-')+1);
    }

    function selection() {
      if (isset($_SESSION['cart_PayPal_Pro_HS_ID'])) {
        $order_id = $this->extract_order_id();

        $check_query = tep_db_query('SELECT orders_id FROM orders_status_history WHERE orders_id = ' . (int)$order_id . ' LIMIT 1');

        if (tep_db_num_rows($check_query) < 1) {
          tep_delete_order($order_id);
          unset($_SESSION['cart_PayPal_Pro_HS_ID']);
        }
      }

      return [
        'id' => $this->code,
        'module' => $this->public_title,
      ];
    }

    function pre_confirmation_check() {
      if (empty($_SESSION['cart']->cartID)) {
        $_SESSION['cartID'] = $_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();
      }
    }

    function confirmation() {
      global $order, $order_total_modules, $customer_data;

      $_SESSION['pphs_result'] = [];

      if (isset($_SESSION['cartID'])) {
        $insert_order = false;

        if (isset($_SESSION['cart_PayPal_Pro_HS_ID'])) {
          $order_id = $this->extract_order_id();

          $curr_check = tep_db_query("SELECT currency FROM orders WHERE orders_id = " . (int)$order_id);
          $curr = tep_db_fetch_array($curr_check);

          if ( ($curr['currency'] != $order->info['currency'])
            || ($_SESSION['cartID'] != substr($_SESSION['cart_PayPal_Pro_HS_ID'], 0, strlen($_SESSION['cartID'])))
             )
          {
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
          require 'includes/system/segments/checkout/build_order_totals.php';
          require 'includes/system/segments/checkout/insert_order.php';

          $_SESSION['cart_PayPal_Pro_HS_ID'] = $_SESSION['cartID'] . '-' . $order_id;
        } else {
          $order_id = $this->extract_order_id();
        }

        $params = [
          'buyer_email' => $customer_data->get('email_address', $order->customer),
          'cancel_return' => tep_href_link('checkout_payment.php'),
          'currency_code' => $_SESSION['currency'],
          'invoice' => $order_id,
          'custom' => $_SESSION['customer_id'],
          'paymentaction' => OSCOM_APP_PAYPAL_HS_TRANSACTION_METHOD == '1' ? 'sale' : 'authorization',
          'return' => tep_href_link('checkout_process.php'),
          'notify_url' => tep_href_link('ext/modules/payment/paypal/pro_hosted_ipn.php', '', 'SSL', false, false),
          'shipping' => $this->_app->formatCurrencyRaw($order->info['shipping_cost']),
          'tax' => $this->_app->formatCurrencyRaw($order->info['tax']),
          'subtotal' => $this->_app->formatCurrencyRaw($order->info['total'] - $order->info['shipping_cost'] - $order->info['tax']),
          'billing_first_name' => $customer_data->get('firstname', $order->billing),
          'billing_last_name' => $customer_data->get('lastname', $order->billing),
          'billing_address1' => $customer_data->get('street_address', $order->billing),
          'billing_address2' => $customer_data->get('suburb', $order->billing),
          'billing_city' => $customer_data->get('city', $order->billing),
          'billing_state' => tep_get_zone_code(
            $customer_data->get('country_id', $order->billing),
            $customer_data->get('zone_id', $order->billing),
            $customer_data->get('state', $order->billing)),
          'billing_zip' => $customer_data->get('postcode', $order->billing),
          'billing_country' => $customer_data->get('country_iso_code_2', $order->billing),
          'night_phone_b' => $customer_data->get('telephone', $order->customer),
          'template' => 'templateD',
          'item_name' => STORE_NAME,
          'showBillingAddress' => 'false',
          'showShippingAddress' => 'false',
          'showHostedThankyouPage' => 'false',
        ];

        if ( is_numeric($_SESSION['sendto']) && ($_SESSION['sendto'] > 0) ) {
          $params['address_override'] = 'true';
          $customer_data->get('country', $order->delivery);
          $params['first_name'] = $customer_data->get('firstname', $order->delivery);
          $params['last_name'] = $customer_data->get('lastname', $order->delivery);
          $params['address1'] = $customer_data->get('street_address', $order->delivery);
          $params['address2'] = $customer_data->get('suburb', $order->delivery);
          $params['city'] = $customer_data->get('city', $order->delivery);
          $params['state'] = tep_get_zone_code(
            $customer_data->get('country_id', $order->delivery),
            $customer_data->get('zone_id', $order->delivery),
            $customer_data->get('state', $order->delivery));
          $params['zip'] = $customer_data->get('postcode', $order->delivery);
          $params['country'] = $customer_data->get('country_iso_code_2', $order->delivery);
        }

        $return_link_title = $this->_app->getDef('module_hs_button_return_to_store', ['storename' => STORE_NAME]);

        if ( strlen($return_link_title) <= 60 ) {
          $params['cbt'] = $return_link_title;
        }

        $_SESSION['pphs_result'] = $this->_app->getApiResult('APP', 'BMCreateButton', $params, (OSCOM_APP_PAYPAL_HS_STATUS == '1') ? 'live' : 'sandbox');
      }

      $_SESSION['pphs_key'] = tep_create_random_value(16);

      $iframe_url = tep_href_link('ext/modules/payment/paypal/hosted_checkout.php', 'key=' . $_SESSION['pphs_key']);
      $form_url = tep_href_link('checkout_payment.php', 'payment_error=paypal_pro_hs');

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
      $result = false;

      if ( !empty($_GET['tx']) ) { // direct payment (eg, credit card)
        $result = $this->_app->getApiResult('APP', 'GetTransactionDetails', ['TRANSACTIONID' => $_GET['tx']], (OSCOM_APP_PAYPAL_HS_STATUS == '1') ? 'live' : 'sandbox');
      } elseif ( !empty($_POST['txn_id']) ) { // paypal payment
        $result = $this->_app->getApiResult('APP', 'GetTransactionDetails', ['TRANSACTIONID' => $_POST['txn_id']], (OSCOM_APP_PAYPAL_HS_STATUS == '1') ? 'live' : 'sandbox');
      }

      if ( !in_array($result['ACK'], ['Success', 'SuccessWithWarning']) ) {
        tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . stripslashes($result['L_LONGMESSAGE0'])));
      }

      $order = new order($this->extract_order_id());

      $seller_accounts = [$this->_app->getCredentials('HS', 'email')];

      if ( tep_not_null($this->_app->getCredentials('HS', 'email_primary')) ) {
        $seller_accounts[] = $this->_app->getCredentials('HS', 'email_primary');
      }

      if ( !isset($result['RECEIVERBUSINESS']) || !in_array($result['RECEIVERBUSINESS'], $seller_accounts) || ($result['INVNUM'] != $order->get_id()) || ($result['CUSTOM'] != $_SESSION['customer_id']) ) {
        tep_redirect(tep_href_link('shopping_cart.php'));
      }

      $_SESSION['pphs_result'] = $result;

      $check_query = tep_db_query("SELECT orders_status FROM orders WHERE orders_id = " . (int)$order->get_id() . " and customers_id = " . (int)$_SESSION['customer_id']);

      if (!tep_db_num_rows($check_query)) {
        tep_redirect(tep_href_link('shopping_cart.php'));
      }

      $check = tep_db_fetch_array($check_query);

      $this->verifyTransaction($_SESSION['pphs_result']);

      $order->info['order_status'] = DEFAULT_ORDERS_STATUS_ID;

      if ( $check['orders_status'] != OSCOM_APP_PAYPAL_HS_PREPARE_ORDER_STATUS_ID ) {
        $order->info['order_status'] = $check['orders_status'];
      }

      if ( (OSCOM_APP_PAYPAL_HS_ORDER_STATUS_ID > 0) && ($check['orders_status'] == OSCOM_APP_PAYPAL_HS_ORDER_STATUS_ID) ) {
        $order->info['order_status'] = OSCOM_APP_PAYPAL_HS_ORDER_STATUS_ID;
      }

      tep_db_query("UPDATE orders SET orders_status = " . (int)$order->info['order_status'] . ", last_modified = NOW() WHERE orders_id = " . (int)$order->get_id());

      $GLOBALS['hooks']->register_pipeline('after');
      require 'includes/system/segments/checkout/insert_history.php';

// load the after_process function from the payment modules
      $this->after_process();

      $GLOBALS['hooks']->register_pipeline('reset');

      unset($_SESSION['cart_PayPal_Pro_HS_ID']);
      unset($_SESSION['pphs_result']);
      unset($_SESSION['pphs_key']);

      tep_redirect(tep_href_link('checkout_success.php'));
    }

    function after_process() {
      return false;
    }

    function get_error() {
      $error = [
        'title' => $this->_app->getDef('module_hs_error_general_title'),
        'error' => $this->_app->getDef('module_hs_error_general'),
      ];

      if ( isset($_SESSION['pphs_error_msg']) ) {
        $error['error'] = $_SESSION['pphs_error_msg'];

        unset($_SESSION['pphs_error_msg']);
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

    function verifyTransaction($pphs_result, $is_ipn = false) {
      $tx_order_id = $pphs_result['INVNUM'];
      $tx_customer_id = $pphs_result['CUSTOM'];
      $tx_transaction_id = $pphs_result['TRANSACTIONID'];
      $tx_payment_status = $pphs_result['PAYMENTSTATUS'];
      $tx_payment_type = $pphs_result['PAYMENTTYPE'];
      $tx_payer_status = $pphs_result['PAYERSTATUS'];
      $tx_address_status = $pphs_result['ADDRESSSTATUS'];
      $tx_amount = $pphs_result['AMT'];
      $tx_currency = $pphs_result['CURRENCYCODE'];
      $tx_pending_reason = $pphs_result['PENDINGREASON'] ?? null;

      if ( is_numeric($tx_order_id) && ($tx_order_id > 0) && is_numeric($tx_customer_id) && ($tx_customer_id > 0) ) {
        $order_query = tep_db_query("SELECT orders_id, orders_status, currency, currency_value FROM orders WHERE orders_id = " . (int)$tx_order_id . " and customers_id = " . (int)$tx_customer_id);

        if ( tep_db_num_rows($order_query) === 1 ) {
          $order = tep_db_fetch_array($order_query);

          $new_order_status = DEFAULT_ORDERS_STATUS_ID;

          if ( $order['orders_status'] != OSCOM_APP_PAYPAL_HS_PREPARE_ORDER_STATUS_ID ) {
            $new_order_status = $order['orders_status'];
          }

          $total_query = tep_db_query("SELECT value FROM orders_total WHERE orders_id = '" . (int)$order['orders_id'] . "' and class = 'ot_total' limit 1");
          $total = tep_db_fetch_array($total_query);

          $comment_status = 'Transaction ID: ' . htmlspecialchars($tx_transaction_id) . "\n"
                          . 'Payer Status: ' . htmlspecialchars($tx_payer_status) . "\n"
                          . 'Address Status: ' . htmlspecialchars($tx_address_status) . "\n"
                          . 'Payment Status: ' . htmlspecialchars($tx_payment_status) . "\n"
                          . 'Payment Type: ' . htmlspecialchars($tx_payment_type) . "\n"
                          . 'Pending Reason: ' . htmlspecialchars($tx_pending_reason);

          if ( $tx_amount != $this->_app->formatCurrencyRaw($total['value'], $order['currency'], $order['currency_value']) ) {
            $comment_status .= "\n" . 'OSCOM Error Total Mismatch: PayPal transaction value (' . htmlspecialchars($tx_amount) . ') does not match order value (' . $this->_app->formatCurrencyRaw($total['value'], $order['currency'], $order['currency_value']) . ')';
          } elseif ( $tx_payment_status == 'Completed' ) {
            $new_order_status = (OSCOM_APP_PAYPAL_HS_ORDER_STATUS_ID > 0 ? OSCOM_APP_PAYPAL_HS_ORDER_STATUS_ID : $new_order_status);
          }

          tep_db_query("UPDATE orders SET orders_status = " . (int)$new_order_status . ", last_modified = NOW() WHERE orders_id = " . (int)$order['orders_id']);

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
