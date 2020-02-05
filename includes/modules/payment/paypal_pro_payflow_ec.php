<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class paypal_pro_payflow_ec extends abstract_payment_module {

    const CONFIG_KEY_BASE = 'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_';

    private $signature = 'paypal|paypal_pro_payflow_ec|3.0|2.3';

    public function __construct() {
      parent::__construct();

      if ( !defined('MODULE_PAYMENT_INSTALLED') || !tep_not_null(MODULE_PAYMENT_INSTALLED) || !in_array('paypal_pro_payflow_dp.php', explode(';', MODULE_PAYMENT_INSTALLED)) || !defined('MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_DP_STATUS') || (MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_DP_STATUS != 'True') ) {
        $this->description = '<div class="secWarning">' . MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_DIRECT_MODULE . '</div>' . $this->description;

        $this->enabled = false;
      }

      if ( defined('MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_STATUS') ) {
        if ( MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_SERVER == 'Sandbox' ) {
          $this->title .= ' [Sandbox]';
          $this->public_title .= ' (' . $this->code . '; Sandbox)';
        }

        $this->description .= $this->getTestLinkInfo();
      }

      if ( !function_exists('curl_init') ) {
        $this->description = '<div class="secWarning">' . MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_ADMIN_CURL . '</div>' . $this->description;

        $this->enabled = false;
      }

      if ( $this->enabled === true ) {
        if ( !tep_not_null(MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VENDOR) || !tep_not_null(MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PASSWORD) ) {
          $this->description = '<div class="secWarning">' . MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_ADMIN_CONFIGURATION . '</div>' . $this->description;

          $this->enabled = false;
        }
      }

      if ( ('modules.php' === $GLOBALS['PHP_SELF']) && ('install' === ($_GET['action'] ?? null)) && ('conntest' === ($_GET['subaction'] ?? null)) ) {
        echo $this->getTestConnectionResult();
        exit;
      }
    }

    public function checkout_initialization_method() {
      $button_title = tep_output_string_protected(MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TEXT_BUTTON);

      if ( MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_SERVER == 'Sandbox' ) {
        $button_title .= ' (' . $this->code . '; Sandbox)';
      }

      $string = '<a href="' . tep_href_link('ext/modules/payment/paypal/express_payflow.php', '', 'SSL') . '"><img src="' . MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_BUTTON . '" border="0" alt="" title="' . $button_title . '" /></a>';

      return $string;
    }

    public function pre_confirmation_check() {
      global $ppeuk_token, $ppeuk_secret, $ppeuk_order_total_check, $messageStack, $order;

      if (!tep_session_is_registered('ppeuk_token')) {
        tep_redirect(tep_href_link('ext/modules/payment/paypal/express_payflow.php', '', 'SSL'));
      }

      $response_array = $this->getExpressCheckoutDetails($ppeuk_token);

      if ($response_array['RESULT'] != '0') {
        tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . urlencode($response_array['OSCOM_ERROR_MESSAGE']), 'SSL'));
      } elseif ( !tep_session_is_registered('ppeuk_secret') || ($response_array['CUSTOM'] != $ppeuk_secret) ) {
        tep_redirect(tep_href_link('shopping_cart.php', '', 'SSL'));
      }

      if (!tep_session_is_registered('ppeuk_order_total_check')) tep_session_register('ppeuk_order_total_check');
      $ppeuk_order_total_check = true;

      $messageStack->add('checkout_confirmation', '<span id="PayPalNotice">' . MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_NOTICE_CHECKOUT_CONFIRMATION . '</span><script>$("#PayPalNotice").parent().css({backgroundColor: "#fcf8e3", border: "1px #faedd0 solid", color: "#a67d57", padding: "5px" });</script>', 'paypal');

      $order->info['payment_method'] = '<img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" border="0" alt="PayPal Logo" style="padding: 3px;" />';
    }

    public function confirmation() {
      global $comments;

      if (!isset($comments)) {
        $comments = null;
      }

      $confirmation = false;

      if (empty($comments)) {
        $confirmation = [
          'fields' => [[
            'title' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TEXT_COMMENTS,
            'field' => tep_draw_textarea_field('ppecomments', 'soft', '60', '5', $comments),
          ]],
        ];
      }

      return $confirmation;
    }

    public function before_process() {
      global $customer_id, $order, $sendto, $ppeuk_token, $ppeuk_payerid, $ppeuk_secret, $ppeuk_order_total_check, $comments, $response_array;

      if (!tep_session_is_registered('ppeuk_token')) {
        tep_redirect(tep_href_link('ext/modules/payment/paypal/express_payflow.php', '', 'SSL'));
      }

      $response_array = $this->getExpressCheckoutDetails($ppeuk_token);

      if ($response_array['RESULT'] == '0') {
        if ( !tep_session_is_registered('ppeuk_secret') || ($response_array['CUSTOM'] != $ppeuk_secret) ) {
          tep_redirect(tep_href_link('shopping_cart.php', '', 'SSL'));
        } elseif ( !tep_session_is_registered('ppeuk_order_total_check') ) {
          tep_redirect(tep_href_link('checkout_confirmation.php', '', 'SSL'));
        }
      } else {
        tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . urlencode($response_array['OSCOM_ERROR_MESSAGE']), 'SSL'));
      }

      if ( tep_session_is_registered('ppeuk_order_total_check') ) {
        tep_session_unregister('ppeuk_order_total_check');
      }

      if (empty($comments)) {
        if (isset($_POST['ppecomments']) && tep_not_null($_POST['ppecomments'])) {
          $comments = tep_db_prepare_input($_POST['ppecomments']);

          $order->info['comments'] = $comments;
        }
      }

      $params = [
        'EMAIL' => $order->customer['email_address'],
        'TOKEN' => $ppeuk_token,
        'PAYERID' => $ppeuk_payerid,
        'AMT' => $this->format_raw($order->info['total']),
        'CURRENCY' => $order->info['currency'],
      ];

      if (is_numeric($sendto) && ($sendto > 0)) {
        $params['SHIPTONAME'] = $order->delivery['name'];
        $params['SHIPTOSTREET'] = $order->delivery['street_address'];
        $params['SHIPTOCITY'] = $order->delivery['city'];
        $params['SHIPTOSTATE'] = tep_get_zone_code($order->delivery['country']['id'], $order->delivery['zone_id'], $order->delivery['state']);
        $params['SHIPTOCOUNTRY'] = $order->delivery['country']['iso_code_2'];
        $params['SHIPTOZIP'] = $order->delivery['postcode'];
      }

      $response_array = $this->doExpressCheckoutPayment($params);

      if ($response_array['RESULT'] != '0') {
        tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . urlencode($response_array['OSCOM_ERROR_MESSAGE']), 'SSL'));
      }
    }

    public function after_process() {
      global $response_array, $order_id, $ppeuk_payerstatus, $ppeuk_addressstatus;

      $pp_result = 'Payflow ID: ' . tep_output_string_protected($response_array['PNREF']) . "\n"
                 . 'PayPal ID: ' . tep_output_string_protected($response_array['PPREF']) . "\n\n"
                 . 'Payer Status: ' . tep_output_string_protected($ppeuk_payerstatus) . "\n"
                 . 'Address Status: ' . tep_output_string_protected($ppeuk_addressstatus) . "\n\n"
                 . 'Payment Status: ' . tep_output_string_protected($response_array['PENDINGREASON']) . "\n"
                 . 'Payment Type: ' . tep_output_string_protected($response_array['PAYMENTTYPE']) . "\n"
                 . 'Response: ' . tep_output_string_protected($response_array['RESPMSG']);

      $sql_data = [
        'orders_id' => $order_id,
        'orders_status_id' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTIONS_ORDER_STATUS_ID,
        'date_added' => 'NOW()',
        'customer_notified' => '0',
        'comments' => $pp_result,
      ];

      tep_db_perform('orders_status_history', $sql_data);

      tep_session_unregister('ppeuk_token');
      tep_session_unregister('ppeuk_payerid');
      tep_session_unregister('ppeuk_payerstatus');
      tep_session_unregister('ppeuk_addressstatus');
      tep_session_unregister('ppeuk_secret');
    }

    protected function get_parameters() {
      return [
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_STATUS' => [
          'title' => 'Enable PayPal Express Checkout (Payflow Edition)',
          'desc' => 'Do you want to accept PayPal Express Checkout (Payflow Edition) payments?',
          'value' => 'True',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VENDOR' => [
          'title' => 'Vendor',
          'desc' => 'Your merchant login ID that you created when you registered for the PayPal Payments Pro account.',
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_USERNAME' => [
          'title' => 'User',
          'desc' => 'If you set up one or more additional users on the account, this value is the ID of the user authorised to process transactions. If, however, you have not set up additional users on the account, USER has the same value as VENDOR.',
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PASSWORD' => [
          'title' => 'Password',
          'desc' => 'The 6- to 32-character password that you defined while registering for the account.',
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PARTNER' => [
          'title' => 'Partner',
          'desc' => 'The ID provided to you by the authorised PayPal Reseller who registered you for the Payflow SDK. If you purchased your account directly from PayPal, use PayPalUK.',
          'value' => 'PayPalUK',
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PAGE_STYLE' => [
          'title' => 'Page Style',
          'desc' => 'The page style to use for the checkout flow (defined at your PayPal Profile page)',
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_METHOD' => [
          'title' => 'Transaction Method',
          'desc' => 'The processing method to use for each transaction.',
          'value' => 'Sale',
          'set_func' => "tep_cfg_select_option(['Authorization', 'Sale'], ",
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ORDER_STATUS_ID' => [
          'title' => 'Set Order Status',
          'desc' => 'Set the status of orders made with this payment module to this value.',
          'value' => '0',
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
          'use_func' => 'tep_get_order_status_name',
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTIONS_ORDER_STATUS_ID' => [
          'title' => 'PayPal Transactions Order Status Level',
          'desc' => 'Include PayPal transaction information in this order status level.',
          'value' => self::ensure_order_status('MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTIONS_ORDER_STATUS_ID', 'PayPal [Transactions]'),
          'use_func' => 'tep_get_order_status_name',
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ZONE' => [
          'title' => 'Payment Zone',
          'desc' => 'If a zone is selected, only enable this payment method for that zone.',
          'value' => '0',
          'set_func' => 'tep_cfg_pull_down_zone_classes(',
          'use_func' => 'tep_get_zone_class_title',
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_SERVER' => [
          'title' => 'Transaction Server',
          'desc' => 'Use the live or testing (sandbox) gateway server to process transactions?',
          'value' => 'Live',
          'set_func' => "tep_cfg_select_option(['Live', 'Sandbox'], ",
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VERIFY_SSL' => [
          'title' => 'Verify SSL Certificate',
          'desc' => 'Verify gateway server SSL certificate on connection?',
          'value' => 'True',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PROXY' => [
          'title' => 'Proxy Server',
          'desc' => 'Send API requests through this proxy server. (host:port, eg: 123.45.67.89:8080 or proxy.example.com:8080)',
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_DEBUG_EMAIL' => [
          'title' => 'Debug E-Mail Address',
          'desc' => 'All parameters of an invalid transaction will be sent to this email address.',
        ],
        'MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_SORT_ORDER' => [
          'title' => 'Sort order of display.',
          'desc' => 'Sort order of display. Lowest is displayed first.',
          'value' => '0',
        ],
      ];
    }

    function sendTransactionToGateway($url, $parameters) {
      global $cartID, $order;

      $server = parse_url($url);

      if ( !isset($server['port']) ) {
        $server['port'] = ($server['scheme'] == 'https') ? 443 : 80;
      }

      if ( !isset($server['path']) ) {
        $server['path'] = '/';
      }

      $request_id = (isset($order) && is_object($order)) ? md5($cartID . tep_session_id() . $this->format_raw($order->info['total'])) : 'oscom_conn_test';

      $headers = [
        'X-VPS-REQUEST-ID: ' . $request_id,
        'X-VPS-CLIENT-TIMEOUT: 45',
        'X-VPS-VIT-INTEGRATION-PRODUCT: OSCOM',
        'X-VPS-VIT-INTEGRATION-VERSION: 2.3',
      ];

      $curl = curl_init($server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : ''));
      curl_setopt($curl, CURLOPT_PORT, $server['port']);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
      curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);

      if ( MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VERIFY_SSL == 'True' ) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        if ( file_exists(DIR_FS_CATALOG . 'ext/modules/payment/paypal/paypal.com.crt') ) {
          curl_setopt($curl, CURLOPT_CAINFO, DIR_FS_CATALOG . 'ext/modules/payment/paypal/paypal.com.crt');
        } elseif ( file_exists(DIR_FS_CATALOG . 'includes/cacert.pem') ) {
          curl_setopt($curl, CURLOPT_CAINFO, DIR_FS_CATALOG . 'includes/cacert.pem');
        }
      } else {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      }

      if ( tep_not_null(MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PROXY) ) {
        curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($curl, CURLOPT_PROXY, MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PROXY);
      }

      $result = curl_exec($curl);

      curl_close($curl);

      return $result;
    }

// format prices without currency formatting
    function format_raw($number, $currency_code = '', $currency_value = '') {
      global $currencies, $currency;

      if (empty($currency_code) || !$this->is_set($currency_code)) {
        $currency_code = $currency;
      }

      if (empty($currency_value) || !is_numeric($currency_value)) {
        $currency_value = $currencies->currencies[$currency_code]['value'];
      }

      return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '.', '');
    }

    function setExpressCheckout($parameters) {
      if (MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_SERVER == 'Live') {
        $api_url = 'https://payflowpro.paypal.com';
      } else {
        $api_url = 'https://pilot-payflowpro.paypal.com';
      }

      $params = [
        'USER' => (tep_not_null(MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_USERNAME) ? MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_USERNAME : MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VENDOR),
        'VENDOR' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VENDOR,
        'PARTNER' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PARTNER,
        'PWD' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PASSWORD,
        'TENDER' => 'P',
        'TRXTYPE' => ((MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_METHOD == 'Sale') ? 'S' : 'A'),
        'ACTION' => 'S',
        'RETURNURL' => tep_href_link('ext/modules/payment/paypal/express_payflow.php', 'osC_Action=retrieve', 'SSL'),
        'CANCELURL' => tep_href_link('shopping_cart.php', '', 'SSL'),
      ];

      if (is_array($parameters) && !empty($parameters)) {
        $params = array_merge($params, $parameters);
      }

      $post_string = '';

      foreach ($params as $key => $value) {
        $post_string .= $key . '[' . strlen(trim($value)) . ']=' . trim($value) . '&';
      }

      $post_string = substr($post_string, 0, -1);

      $response = $this->sendTransactionToGateway($api_url, $post_string);

      $response_array = [];
      parse_str($response, $response_array);

      if ($response_array['RESULT'] != '0') {
        $this->sendDebugEmail($response_array);

        switch ($response_array['RESULT']) {
          case '1':
          case '26':
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_CFG_ERROR;
            break;

          case '1000':
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_EXPRESS_DISABLED;
            break;

          default:
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_GENERAL;
            break;
        }

        $response_array['OSCOM_ERROR_MESSAGE'] = $error_message;
      }

      return $response_array;
    }

    function getExpressCheckoutDetails($token) {
      if (MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_SERVER == 'Live') {
        $api_url = 'https://payflowpro.paypal.com';
      } else {
        $api_url = 'https://pilot-payflowpro.paypal.com';
      }

      $params = [
        'USER' => (tep_not_null(MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_USERNAME) ? MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_USERNAME : MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VENDOR),
        'VENDOR' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VENDOR,
        'PARTNER' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PARTNER,
        'PWD' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PASSWORD,
        'TENDER' => 'P',
        'TRXTYPE' => ((MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_METHOD == 'Sale') ? 'S' : 'A'),
        'ACTION' => 'G',
        'TOKEN' => $token,
      ];

      $post_string = '';

      foreach ($params as $key => $value) {
        $post_string .= $key . '[' . strlen(trim($value)) . ']=' . trim($value) . '&';
      }

      $post_string = substr($post_string, 0, -1);

      $response = $this->sendTransactionToGateway($api_url, $post_string);

      $response_array = [];
      parse_str($response, $response_array);

      if ($response_array['RESULT'] != '0') {
        $this->sendDebugEmail($response_array);

        switch ($response_array['RESULT']) {
          case '1':
          case '26':
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_CFG_ERROR;
            break;

          case '7':
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_ADDRESS;
            break;

          case '12':
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_DECLINED;
            break;

          case '1000':
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_EXPRESS_DISABLED;
            break;

          default:
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_GENERAL;
            break;
        }

        $response_array['OSCOM_ERROR_MESSAGE'] = $error_message;
      }

      return $response_array;
    }

    function doExpressCheckoutPayment($parameters) {
      if (MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_SERVER == 'Live') {
        $api_url = 'https://payflowpro.paypal.com';
      } else {
        $api_url = 'https://pilot-payflowpro.paypal.com';
      }

      $params = [
        'USER' => (tep_not_null(MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_USERNAME) ? MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_USERNAME : MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VENDOR),
        'VENDOR' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VENDOR,
        'PARTNER' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PARTNER,
        'PWD' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PASSWORD,
        'TENDER' => 'P',
        'TRXTYPE' => ((MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_METHOD == 'Sale') ? 'S' : 'A'),
        'ACTION' => 'D',
        'BUTTONSOURCE' => 'OSCOM23_ECPF',
      ];

      if (is_array($parameters) && !empty($parameters)) {
        $params = array_merge($params, $parameters);
      }

      $post_string = '';

      foreach ($params as $key => $value) {
        $post_string .= $key . '[' . strlen(trim($value)) . ']=' . trim($value) . '&';
      }

      $post_string = substr($post_string, 0, -1);

      $response = $this->sendTransactionToGateway($api_url, $post_string);

      $response_array = [];
      parse_str($response, $response_array);

      if ($response_array['RESULT'] != '0') {
        $this->sendDebugEmail($response_array);

        switch ($response_array['RESULT']) {
          case '1':
          case '26':
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_CFG_ERROR;
            break;

          case '7':
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_ADDRESS;
            break;

          case '12':
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_DECLINED;
            break;

          case '1000':
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_EXPRESS_DISABLED;
            break;

          default:
            $error_message = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_GENERAL;
            break;
        }

        $response_array['OSCOM_ERROR_MESSAGE'] = $error_message;
      }

      return $response_array;
    }

    function sendDebugEmail($response = []) {
      if (tep_not_null(MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_DEBUG_EMAIL)) {
        $email_body = '';

        if (!empty($response)) {
          $email_body .= 'RESPONSE:' . "\n\n" . print_r($response, true) . "\n\n";
        }

        if (!empty($_POST)) {
          $email_body .= '$_POST:' . "\n\n" . print_r($_POST, true) . "\n\n";
        }

        if (!empty($_GET)) {
          $email_body .= '$_GET:' . "\n\n" . print_r($_GET, true) . "\n\n";
        }

        if (!empty($email_body)) {
          tep_mail('', MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_DEBUG_EMAIL, 'PayPal Express Checkout (Payflow Edition) Debug E-Mail', trim($email_body), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }
      }
    }

    function getTestLinkInfo() {
      $dialog_title = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_DIALOG_CONNECTION_TITLE;
      $dialog_button_close = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_DIALOG_CONNECTION_BUTTON_CLOSE;
      $dialog_success = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_DIALOG_CONNECTION_SUCCESS;
      $dialog_failed = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_DIALOG_CONNECTION_FAILED;
      $dialog_error = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_DIALOG_CONNECTION_ERROR;
      $dialog_connection_time = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_DIALOG_CONNECTION_TIME;

      $test_url = tep_href_link('modules.php', 'set=payment&module=' . $this->code . '&action=install&subaction=conntest');

      $js = <<<EOD
<script>
$(function() {
  $('#tcdprogressbar').progressbar({
    value: false
  });
});

function openTestConnectionDialog() {
  var d = $('<div>').html($('#testConnectionDialog').html()).dialog({
    modal: true,
    title: '{$dialog_title}',
    buttons: {
      '{$dialog_button_close}': function () {
        $(this).dialog('destroy');
      }
    }
  });

  var timeStart = new Date().getTime();

  $.ajax({
    url: '{$test_url}'
  }).done(function(data) {
    if ( data == '1' ) {
      d.find('#testConnectionDialogProgress').html('<p style="font-weight: bold; color: green;">{$dialog_success}</p>');
    } else {
      d.find('#testConnectionDialogProgress').html('<p style="font-weight: bold; color: red;">{$dialog_failed}</p>');
    }
  }).fail(function() {
    d.find('#testConnectionDialogProgress').html('<p style="font-weight: bold; color: red;">{$dialog_error}</p>');
  }).always(function() {
    var timeEnd = new Date().getTime();
    var timeTook = new Date(0, 0, 0, 0, 0, 0, timeEnd-timeStart);

    d.find('#testConnectionDialogProgress').append('<p>{$dialog_connection_time} ' + timeTook.getSeconds() + '.' + timeTook.getMilliseconds() + 's</p>');
  });
}
</script>
EOD;

      $info = '<p><img src="images/icons/locked.gif" border="0">&nbsp;<a href="javascript:openTestConnectionDialog();" style="text-decoration: underline; font-weight: bold;">' . MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_DIALOG_CONNECTION_LINK_TITLE . '</a></p>'
            . '<div id="testConnectionDialog" style="display: none;"><p>';

      if ( MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_SERVER == 'Live' ) {
        $info .= 'Live Server:<br />https://payflowpro.paypal.com';
      } else {
        $info .= 'Sandbox Server:<br />https://pilot-payflowpro.paypal.com';
      }

      $info .= '</p><div id="testConnectionDialogProgress"><p>' . MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_DIALOG_CONNECTION_GENERAL_TEXT . '</p><div id="tcdprogressbar"></div></div></div>'
             . $js;

      return $info;
    }

    function getTestConnectionResult() {
      if (MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_SERVER == 'Live') {
        $api_url = 'https://payflowpro.paypal.com';
      } else {
        $api_url = 'https://pilot-payflowpro.paypal.com';
      }

      $params = [
        'USER' => (tep_not_null(MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_USERNAME) ? MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_USERNAME : MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VENDOR),
        'VENDOR' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_VENDOR,
        'PARTNER' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PARTNER,
        'PWD' => MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PASSWORD,
        'TENDER' => 'P',
        'TRXTYPE' => ((MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_METHOD == 'Sale') ? 'S' : 'A'),
      ];

      $post_string = '';

      foreach ($params as $key => $value) {
        $post_string .= $key . '[' . strlen(trim($value)) . ']=' . trim($value) . '&';
      }

      $post_string = substr($post_string, 0, -1);

      $response = $this->sendTransactionToGateway($api_url, $post_string);

      $response_array = [];
      parse_str($response, $response_array);

      return isset($response_array['RESULT']) ? 1 : -1;
    }
  }
