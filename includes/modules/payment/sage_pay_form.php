<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class sage_pay_form extends abstract_payment_module {

    const CONFIG_KEY_BASE = 'MODULE_PAYMENT_SAGE_PAY_FORM_';

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

    public function __construct() {
      $this->signature = 'sage_pay|sage_pay_form|2.0|2.3';
      $this->api_version = '3.00';

      parent::__construct();
      $this->public_title = MODULE_PAYMENT_SAGE_PAY_FORM_TEXT_PUBLIC_TITLE;
      $this->sort_order = $this->sort_order ?? 0;
      $this->order_status = ((int)self::get_constant('MODULE_PAYMENT_SAGE_PAY_FORM_ORDER_STATUS_ID') > 0) ? (int)MODULE_PAYMENT_SAGE_PAY_FORM_ORDER_STATUS_ID : 0;

      if ( defined('MODULE_PAYMENT_SAGE_PAY_FORM_STATUS') ) {
        if ( MODULE_PAYMENT_SAGE_PAY_FORM_TRANSACTION_SERVER == 'Test' ) {
          $this->title .= ' [Test]';
          $this->public_title .= ' (' . $this->code . '; Test)';
        }
      }

      if ( $this->enabled === true ) {
        if ( !tep_not_null(MODULE_PAYMENT_SAGE_PAY_FORM_VENDOR_LOGIN_NAME) || !tep_not_null(MODULE_PAYMENT_SAGE_PAY_FORM_ENCRYPTION_PASSWORD) ) {
          $this->description = '<div class="alert alert-warning">' . MODULE_PAYMENT_SAGE_PAY_FORM_ERROR_ADMIN_CONFIGURATION . '</div>' . $this->description;

          $this->enabled = false;
        }
      }

      if ( defined('MODULE_PAYMENT_SAGE_PAY_FORM_TRANSACTION_SERVER') && MODULE_PAYMENT_SAGE_PAY_FORM_TRANSACTION_SERVER == 'Live' ) {
        $this->form_action_url = 'https://live.sagepay.com/gateway/service/vspform-register.vsp';
      } else {
        $this->form_action_url = 'https://test.sagepay.com/gateway/service/vspform-register.vsp';
      }
    }

    public function process_button() {
      global $order;

      $process_button_string = '';

      $params = [
        'VPSProtocol' => $this->api_version,
        'Vendor' => substr(MODULE_PAYMENT_SAGE_PAY_FORM_VENDOR_LOGIN_NAME, 0, 15),
      ];

      if ( MODULE_PAYMENT_SAGE_PAY_FORM_TRANSACTION_METHOD == 'Payment' ) {
        $params['TxType'] = 'PAYMENT';
      } elseif ( MODULE_PAYMENT_SAGE_PAY_FORM_TRANSACTION_METHOD == 'Deferred' ) {
        $params['TxType'] = 'DEFERRED';
      } else {
        $params['TxType'] = 'AUTHENTICATE';
      }

      $crypt = [
        'ReferrerID' => 'C74D7B82-E9EB-4FBD-93DB-76F0F551C802',
        'VendorTxCode' => substr(date('YmdHis') . '-' . $_SESSION['customer_id'] . '-' . $_SESSION['cartID'], 0, 40),
        'Amount' => $this->format_raw($order->info['total']),
        'Currency' => $_SESSION['currency'],
        'Description' => substr(STORE_NAME, 0, 100),
        'SuccessURL' => tep_href_link('checkout_process.php', '', 'SSL'),
        'FailureURL' => tep_href_link('checkout_payment.php', 'payment_error=' . $this->code, 'SSL'),
        'CustomerName' => substr($order->billing['name'], 0, 100),
        'CustomerEMail' => substr($order->customer['email_address'], 0, 255),
        'BillingSurname' => substr($order->billing['lastname'], 0, 20),
        'BillingFirstnames' => substr($order->billing['firstname'], 0, 20),
        'BillingAddress1' => substr($order->billing['street_address'], 0, 100),
        'BillingCity' => substr($order->billing['city'], 0, 40),
        'BillingPostCode' => substr($order->billing['postcode'], 0, 10),
        'BillingCountry' => $order->billing['country']['iso_code_2'],
      ];

      if ($crypt['BillingCountry'] == 'US') {
        $crypt['BillingState'] = tep_get_zone_code($order->billing['country']['id'], $order->billing['zone_id'], '');
      }

      $crypt['BillingPhone'] = substr($order->customer['telephone'], 0, 20);
      $crypt['DeliverySurname'] = substr($order->delivery['lastname'], 0, 20);
      $crypt['DeliveryFirstnames'] = substr($order->delivery['firstname'], 0, 20);
      $crypt['DeliveryAddress1'] = substr($order->delivery['street_address'], 0, 100);
      $crypt['DeliveryCity'] = substr($order->delivery['city'], 0, 40);
      $crypt['DeliveryPostCode'] = substr($order->delivery['postcode'], 0, 10);
      $crypt['DeliveryCountry'] = $order->delivery['country']['iso_code_2'];

      if ($crypt['DeliveryCountry'] == 'US') {
        $crypt['DeliveryState'] = tep_get_zone_code($order->delivery['country']['id'], $order->delivery['zone_id'], '');
      }

      if (tep_not_null(MODULE_PAYMENT_SAGE_PAY_FORM_VENDOR_EMAIL)) {
        $crypt['VendorEMail'] = substr(MODULE_PAYMENT_SAGE_PAY_FORM_VENDOR_EMAIL, 0, 255);
      }

      switch (MODULE_PAYMENT_SAGE_PAY_FORM_SEND_EMAIL) {
        case 'No One':
          $crypt['SendEMail'] = 0;
          break;

        case 'Customer and Vendor':
          $crypt['SendEMail'] = 1;
          break;

        case 'Vendor Only':
          $crypt['SendEMail'] = 2;
          break;
      }

      if (tep_not_null(MODULE_PAYMENT_SAGE_PAY_FORM_CUSTOMER_EMAIL_MESSAGE)) {
        $crypt['eMailMessage'] = substr(MODULE_PAYMENT_SAGE_PAY_FORM_CUSTOMER_EMAIL_MESSAGE, 0, 7500);
      }

      $contents = [];

      foreach ($order->products as $product) {
        $product_name = $product['name'];

        if (isset($product['attributes'])) {
          foreach ($product['attributes'] as $att) {
            $product_name .= '; ' . $att['option'] . '=' . $att['value'];
          }
        }

        $contents[] = str_replace([':', "\n", "\r", '&'], '', $product_name) . ':' . $product['qty'] . ':' . $this->format_raw($product['final_price']) . ':' . $this->format_raw(($product['tax'] / 100) * $product['final_price']) . ':' . $this->format_raw((($product['tax'] / 100) * $product['final_price']) + $product['final_price']) . ':' . $this->format_raw(((($product['tax'] / 100) * $product['final_price']) + $product['final_price']) * $product['qty']);
      }

      foreach ($this->getOrderTotalsSummary() as $ot) {
        $contents[] = str_replace([':', "\n", "\r", '&'], '', strip_tags($ot['title'])) . ':---:---:---:---:' . $this->format_raw($ot['value']);
      }

      $crypt['Basket'] = substr(count($contents) . ':' . implode(':', $contents), 0, 7500);
      $crypt['Apply3DSecure'] = '0';

      $crypt_string = '';

      foreach ($crypt as $key => $value) {
        $crypt_string .= $key . '=' . trim($value) . '&';
      }

      $crypt_string = substr($crypt_string, 0, -1);

      $params['Crypt'] = $this->encryptParams($crypt_string);

      foreach ($params as $key => $value) {
        $process_button_string .= tep_draw_hidden_field($key, $value);
      }

      return $process_button_string;
    }

    public function before_process() {
      global $sage_pay_response;

      if (isset($_GET['crypt']) && tep_not_null($_GET['crypt'])) {
        $transaction_response = $this->decryptParams($_GET['crypt']);

        $sage_pay_response = ['Status' => null];

        foreach (explode('&', $transaction_response) as $string) {
          if (strpos($string, '=') != false) {
            $parts = explode('=', $string, 2);
            $sage_pay_response[trim($parts[0])] = trim($parts[1]);
          }
        }

        if ( ($sage_pay_response['Status'] != 'OK') && ($sage_pay_response['Status'] != 'AUTHENTICATED') && ($sage_pay_response['Status'] != 'REGISTERED') ) {
          $this->sendDebugEmail($sage_pay_response);

          $error = $this->getErrorMessageNumber($sage_pay_response['StatusDetail']);

          tep_redirect(tep_href_link('checkout_payment.php', 'payment_error=' . $this->code . (tep_not_null($error) ? '&error=' . $error : ''), 'SSL'));
        }
      } else {
        tep_redirect(tep_href_link('checkout_payment.php', 'payment_error=' . $this->code, 'SSL'));
      }
    }

    public function after_process() {
      global $order_id, $sage_pay_response;

      $result = [];

      if ( isset($sage_pay_response['VPSTxId']) ) {
        $result['ID'] = $sage_pay_response['VPSTxId'];
      }

      if ( isset($sage_pay_response['CardType']) ) {
        $result['Card'] = $sage_pay_response['CardType'];
      }

      if ( isset($sage_pay_response['AVSCV2']) ) {
        $result['AVS/CV2'] = $sage_pay_response['AVSCV2'];
      }

      if ( isset($sage_pay_response['AddressResult']) ) {
        $result['Address'] = $sage_pay_response['AddressResult'];
      }

      if ( isset($sage_pay_response['PostCodeResult']) ) {
        $result['Post Code'] = $sage_pay_response['PostCodeResult'];
      }

      if ( isset($sage_pay_response['CV2Result']) ) {
        $result['CV2'] = $sage_pay_response['CV2Result'];
      }

      if ( isset($sage_pay_response['3DSecureStatus']) ) {
        $result['3D Secure'] = $sage_pay_response['3DSecureStatus'];
      }

      if ( isset($sage_pay_response['PayerStatus']) ) {
        $result['PayPal Payer Status'] = $sage_pay_response['PayerStatus'];
      }

      if ( isset($sage_pay_response['AddressStatus']) ) {
        $result['PayPal Payer Address'] = $sage_pay_response['AddressStatus'];
      }

      $result_string = '';

      foreach ( $result as $k => $v ) {
        $result_string .= $k . ': ' . $v . "\n";
      }

      $sql_data_array = [
        'orders_id' => $order_id,
        'orders_status_id' => MODULE_PAYMENT_SAGE_PAY_FORM_TRANSACTION_ORDER_STATUS_ID,
        'date_added' => 'NOW()',
        'customer_notified' => '0',
        'comments' => trim($result_string),
      ];

      tep_db_perform('orders_status_history', $sql_data_array);
    }

    public function get_error() {
      $message = MODULE_PAYMENT_SAGE_PAY_FORM_ERROR_GENERAL;

      $error_number = null;

      if ( isset($_GET['error']) && is_numeric($_GET['error']) && $this->errorMessageNumberExists($_GET['error']) ) {
        $error_number = $_GET['error'];
      } elseif (isset($_GET['crypt']) && tep_not_null($_GET['crypt'])) {
        $transaction_response = $this->decryptParams($_GET['crypt']);

        $string_array = explode('&', $transaction_response);
        $return = ['Status' => null];

        foreach ($string_array as $string) {
          if (strpos($string, '=') != false) {
            $parts = explode('=', $string, 2);
            $return[trim($parts[0])] = trim($parts[1]);
          }
        }

        $error = $this->getErrorMessageNumber($return['StatusDetail']);

        if ( is_numeric($error) && $this->errorMessageNumberExists($error) ) {
          $error_number = $error;
        }
      }

      if ( isset($error_number) ) {
// don't show an error message for user cancelled/aborted transactions
        if ( $error_number == '2013' ) {
          return false;
        }

        $message = $this->getErrorMessage($error_number) . ' ' . MODULE_PAYMENT_SAGE_PAY_FORM_ERROR_GENERAL;
      }

      $error = [
        'title' => MODULE_PAYMENT_SAGE_PAY_FORM_ERROR_TITLE,
        'error' => $message,
      ];

      return $error;
    }

    protected function get_parameters() {
      return [
        'MODULE_PAYMENT_SAGE_PAY_FORM_STATUS' => [
          'title' => 'Enable Sage Pay Form Module',
          'desc' => 'Do you want to accept Sage Pay Form payments?',
          'value' => 'True',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_VENDOR_LOGIN_NAME' => [
          'title' => 'Vendor Login Name',
          'desc' => 'The vendor login name to connect to the gateway with.',
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_ENCRYPTION_PASSWORD' => [
          'title' => 'Encryption Password',
          'desc' => 'The encrpytion password to secure and verify transactions with.',
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_TRANSACTION_METHOD' => [
          'title' => 'Transaction Method',
          'desc' => 'The processing method to use for each transaction.',
          'value' => 'Authenticate',
          'set_func' => "tep_cfg_select_option(['Authenticate', 'Deferred', 'Payment'], ",
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_VENDOR_EMAIL' => [
          'title' => 'Vendor E-Mail Notification',
          'desc' => 'An e-mail address on which you can be contacted when a transaction completes. NOTE: If you wish to use multiple email addresses, you should add them using the colon character as a separator. e.g. me@mail1.com:me@mail2.com',
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_SEND_EMAIL' => [
          'title' => 'Send E-Mail Notifications',
          'desc' => 'Who to send e-mails to.',
          'value' => 'Customer and Vendor',
          'set_func' => "tep_cfg_select_option(['No One', 'Customer and Vendor', 'Vendor Only'], ",
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_CUSTOMER_EMAIL_MESSAGE' => [
          'title' => 'Customer E-Mail Message',
          'desc' => 'A message to the customer which is inserted into successful transaction e-mails only.',
          'use_func' => 'sage_pay_form_clip_text',
          'set_func' => 'sage_pay_form_textarea_field(',
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_ORDER_STATUS_ID' => [
          'title' => 'Set Order Status',
          'desc' => 'Set the status of orders made with this payment module to this value',
          'value' => '0',
          'use_func' => 'tep_get_order_status_name',
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_TRANSACTION_ORDER_STATUS_ID' => [
          'title' => 'Transaction Order Status',
          'desc' => 'Include transaction information in this order status level',
          'value' => self::ensure_order_status('MODULE_PAYMENT_SAGE_PAY_FORM_TRANSACTION_ORDER_STATUS_ID', 'Sage Pay [Transactions]'),
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
          'use_func' => 'tep_get_order_status_name',
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_ZONE' => [
          'title' => 'Payment Zone',
          'desc' => 'If a zone is selected, only enable this payment method for that zone.',
          'value' => '0',
          'use_func' => 'tep_get_zone_class_title',
          'set_func' => 'tep_cfg_pull_down_zone_classes(',
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_TRANSACTION_SERVER' => [
          'title' => 'Transaction Server',
          'desc' => 'Perform transactions on the production server or on the testing server.',
          'value' => 'Live',
          'set_func' => "tep_cfg_select_option(['Live', 'Test'], ",
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_DEBUG_EMAIL' => [
          'title' => 'Debug E-Mail Address',
          'desc' => 'All parameters of an invalid transaction will be sent to this email address.',
        ],
        'MODULE_PAYMENT_SAGE_PAY_FORM_SORT_ORDER' => [
          'title' => 'Sort order of display.',
          'desc' => 'Sort order of display. Lowest is displayed first.',
          'value' => '0',
        ],
      ];
    }

// format prices without currency formatting
    function format_raw($number, $currency_code = '', $currency_value = '') {
      global $currencies;

      if (empty($currency_code) || !$currencies->is_set($currency_code)) {
        $currency_code = $_SESSION['currency'];
      }

      if (empty($currency_value) || !is_numeric($currency_value)) {
        $currency_value = $currencies->currencies[$currency_code]['value'];
      }

      return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '.', '');
    }

    function getOrderTotalsSummary() {
      $order_totals = [];
      foreach (($GLOBALS['order_total_modules']->modules ?? []) as $value) {
        $class = pathinfo($value, PATHINFO_FILENAME);
        if ($GLOBALS[$class]->enabled) {
          foreach ($GLOBALS[$class]->output as $module) {
            if (tep_not_null($module['title']) && tep_not_null($module['text'])) {
              $order_totals[] = [
                'code' => $GLOBALS[$class]->code,
                'title' => $module['title'],
                'text' => $module['text'],
                'value' => $module['value'],
                'sort_order' => $GLOBALS[$class]->sort_order,
              ];
            }
          }
        }
      }

      return $order_totals;
    }

    function encryptParams($string) {

      $result = '@' . strtoupper(bin2hex(openssl_encrypt($string, 'aes-128-cbc', MODULE_PAYMENT_SAGE_PAY_FORM_ENCRYPTION_PASSWORD, OPENSSL_RAW_DATA, MODULE_PAYMENT_SAGE_PAY_FORM_ENCRYPTION_PASSWORD)));

      return $result;
    }

   function decryptParams($string) {
     if ( substr($string, 0, 1) == '@' ) {
       $string = substr($string, 1);
     }

     $result = openssl_decrypt(hex2bin($string), 'aes-128-cbc', MODULE_PAYMENT_SAGE_PAY_FORM_ENCRYPTION_PASSWORD, OPENSSL_RAW_DATA, MODULE_PAYMENT_SAGE_PAY_FORM_ENCRYPTION_PASSWORD);

     return $result;
   }

    function loadErrorMessages() {
      $errors = [];

      if (file_exists(dirname(__FILE__) . '/../../../ext/modules/payment/sage_pay/errors.php')) {
        include(dirname(__FILE__) . '/../../../ext/modules/payment/sage_pay/errors.php');
      }

      $this->_error_messages = $errors;
    }

    function getErrorMessageNumber($string) {
      if (!isset($this->_error_messages)) {
        $this->loadErrorMessages();
      }

      $error = explode(' ', $string, 2);

      if (is_numeric($error[0]) && $this->errorMessageNumberExists($error[0])) {
        return $error[0];
      }

      return false;
    }

    function getErrorMessage($number) {
      if (!isset($this->_error_messages)) {
        $this->loadErrorMessages();
      }

      if (is_numeric($number) && $this->errorMessageNumberExists($number)) {
        return $this->_error_messages[$number];
      }

      return false;
    }

    function errorMessageNumberExists($number) {
      if (!isset($this->_error_messages)) {
        $this->loadErrorMessages();
      }

      return (is_numeric($number) && isset($this->_error_messages[$number]));
    }

    protected function sendDebugEmail($response = []) {
      if (tep_not_null(MODULE_PAYMENT_SAGE_PAY_FORM_DEBUG_EMAIL)) {
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
          tep_mail('', MODULE_PAYMENT_SAGE_PAY_FORM_DEBUG_EMAIL, 'Sage Pay Form Debug E-Mail', trim($email_body), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }
      }
    }
  }

  function sage_pay_form_clip_text($value) {
    if ( strlen($value) > 20 ) {
      $value = substr($value, 0, 20) . '..';
    }

    return $value;
  }

  function sage_pay_form_textarea_field($value = '', $key = '') {
    return tep_draw_textarea_field('configuration[' . $key . ']', 'soft', 60, 5, $value);
  }
