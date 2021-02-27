<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class rbsworldpay_hosted extends abstract_payment_module {

    const CONFIG_KEY_BASE = 'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_';

    public function __construct() {
      parent::__construct();

      $this->signature = 'rbs|worldpay_hosted|2.0|2.3';
      $this->api_version = '4.6';

      $this->public_title = MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TEXT_PUBLIC_TITLE;
      $this->sort_order = $this->sort_order ?? 0;
      $this->order_status = defined('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID') && ((int)MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID > 0) ? (int)MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID : 0;

      if ( defined('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_STATUS') ) {
        if ( MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TESTMODE == 'True' ) {
          $this->title .= ' [Test]';
          $this->public_title .= ' (' . $this->code . '; Test)';
        }

        if ( MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TESTMODE == 'True' ) {
          $this->form_action_url = 'https://secure-test.worldpay.com/wcc/purchase';
        } else {
          $this->form_action_url = 'https://secure.worldpay.com/wcc/purchase';
        }
      }

      if ( $this->enabled === true ) {
        if ( !tep_not_null(MODULE_PAYMENT_RBSWORLDPAY_HOSTED_INSTALLATION_ID) ) {
          $this->description = '<div class="alert alert-warning">' . MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ERROR_ADMIN_CONFIGURATION . '</div>' . $this->description;

          $this->enabled = false;
        }
      }
    }

    private function extract_order_id() {
      return substr($_SESSION['cart_RBS_Worldpay_Hosted_ID'], strpos($_SESSION['cart_RBS_Worldpay_Hosted_ID'], '-')+1);
    }

    public function selection() {
      if (isset($_SESSION['cart_RBS_Worldpay_Hosted_ID'])) {
        $order_id = $this->extract_order_id();

        $check_query = tep_db_query('SELECT orders_id FROM orders_status_history WHERE orders_id = ' . (int)$order_id . ' LIMIT 1');

        if (tep_db_num_rows($check_query) < 1) {
          tep_delete_order($order_id);
          unset($_SESSION['cart_RBS_Worldpay_Hosted_ID']);
        }
      }

      return parent::selection();
    }

    public function pre_confirmation_check() {
      if (empty($_SESSION['cart']->cartID)) {
        $_SESSION['cartID'] = $_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();
      }
    }

    public function confirmation() {
      global $order;

      $insert_order = false;
      if (isset($_SESSION['cart_RBS_Worldpay_Hosted_ID'])) {
        $order_id = $this->extract_order_id();

        $curr_check = tep_db_query("SELECT currency FROM orders WHERE orders_id = " . (int)$order_id);
        $curr = tep_db_fetch_array($curr_check);

        if ( ($curr['currency'] != $order->info['currency']) || ($_SESSION['cartID'] != substr($GLOBALS['cart_RBS_Worldpay_Hosted_ID'], 0, strlen($_SESSION['cartID']))) ) {
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

        $_SESSION['cart_RBS_Worldpay_Hosted_ID'] = $_SESSION['cartID'] . '-' . $order->get_id();
      }

      return false;
    }

    public function build_hash($order_id) {
      global $order;
      return md5(session_id() . $_SESSION['customer_id'] . $order_id . $_SESSION['language'] . number_format($order->info['total'], 2) . MODULE_PAYMENT_RBSWORLDPAY_HOSTED_MD5_PASSWORD);
    }

    public function process_button() {
      global $order;

      $order_id = $this->extract_order_id();

      $lang_query = tep_db_query("SELECT code FROM languages WHERE languages_id = " . (int)$_SESSION['languages_id']);
      $lang = tep_db_fetch_array($lang_query);

      $process_button_string = tep_draw_hidden_field('instId', MODULE_PAYMENT_RBSWORLDPAY_HOSTED_INSTALLATION_ID)
                             . tep_draw_hidden_field('cartId', $order_id)
                             . tep_draw_hidden_field('amount', $this->format_raw($order->info['total']))
                             . tep_draw_hidden_field('currency', $_SESSION['currency'])
                             . tep_draw_hidden_field('desc', STORE_NAME)
                             . tep_draw_hidden_field('name', $order->billing['name'])
                             . tep_draw_hidden_field('address1', $order->billing['street_address'])
                             . tep_draw_hidden_field('town', $order->billing['city'])
                             . tep_draw_hidden_field('region', $order->billing['state'])
                             . tep_draw_hidden_field('postcode', $order->billing['postcode'])
                             . tep_draw_hidden_field('country', $order->billing['country']['iso_code_2'])
                             . tep_draw_hidden_field('tel', $order->customer['telephone'])
                             . tep_draw_hidden_field('email', $order->customer['email_address'])
                             . tep_draw_hidden_field('fixContact', 'Y')
                             . tep_draw_hidden_field('hideCurrency', 'true')
                             . tep_draw_hidden_field('lang', strtoupper($lang['code']))
                             . tep_draw_hidden_field('signatureFields', 'amount:currency:cartId')
                             . tep_draw_hidden_field('signature', md5(MODULE_PAYMENT_RBSWORLDPAY_HOSTED_MD5_PASSWORD . ':' . $this->format_raw($order->info['total']) . ':' . $_SESSION['currency'] . ':' . $order_id))
                             . tep_draw_hidden_field('MC_callback', tep_href_link('ext/modules/payment/rbsworldpay/hosted_callback.php', '', 'SSL', false))
                             . tep_draw_hidden_field('M_sid', session_id())
                             . tep_draw_hidden_field('M_cid', $_SESSION['customer_id'])
                             . tep_draw_hidden_field('M_lang', $_SESSION['language'])
                             . tep_draw_hidden_field('M_hash', $this->build_hash($order_id));

      if (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TRANSACTION_METHOD == 'Pre-Authorization') {
        $process_button_string .= tep_draw_hidden_field('authMode', 'E');
      }

      if (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TESTMODE == 'True') {
        $process_button_string .= tep_draw_hidden_field('testMode', '100');
      }

      return $process_button_string;
    }

    public function before_process() {
      global $order;

      $order_id = $this->extract_order_id();

      if (!isset($_GET['hash']) || ($_GET['hash'] != $this->build_hash($order_id))) {
        $this->sendDebugEmail();

        tep_redirect(tep_href_link('shopping_cart.php'));
      }

      $trans_result = 'WorldPay: Transaction Verified';
      if (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TESTMODE == 'True') {
        $trans_result .= "\n" . MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TEXT_WARNING_DEMO_MODE;
      }

      $module_status_id = MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ORDER_STATUS_ID;
      $order_status_id = (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ORDER_STATUS_ID > 0 ? MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ORDER_STATUS_ID : DEFAULT_ORDERS_STATUS_ID);

      $order_query = tep_db_query("SELECT orders_status FROM orders WHERE orders_id = " . (int)$order_id . " AND customers_id = " . (int)$_SESSION['customer_id']);

      if (!tep_db_num_rows($order_query)) {
        $this->sendDebugEmail();

        tep_redirect(tep_href_link('shopping_cart.php'));
      }

      $GLOBALS['hooks']->register_pipeline('after');

      $order_status = tep_db_fetch_array($order_query);
      if ($order_status['orders_status'] == $MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID) {
        tep_db_query("UPDATE orders SET orders_status = " . (int)$order_status_id . ", last_modified = NOW() WHERE orders_id = " . (int)$order_id);

        $sql_data = [
          'orders_id' => $order_id,
          'orders_status_id' => $order_status_id,
          'date_added' => 'NOW()',
          'customer_notified' => $GLOBALS['customer_notification'],
          'comments' => $order->info['comments'],
        ];

        tep_db_perform('orders_status_history', $sql_data);
      } else {
        $order_status_query = tep_db_query("SELECT orders_status_history_id FROM orders_status_history WHERE orders_id = " . (int)$order_id . " AND orders_status_id = " . (int)$order_status_id . " AND comments = '' ORDER BY date_added DESC LIMIT 1");

        if (tep_db_num_rows($order_status_query)) {
          $order_status = tep_db_fetch_array($order_status_query);

          $sql_data = [
            'customer_notified' => $GLOBALS['customer_notification'],
            'comments' => $order->info['comments'],
          ];

          tep_db_perform('orders_status_history', $sql_data, 'update', "orders_status_history_id = " . (int)$order_status['orders_status_history_id']);
        }
      }

      $sql_data = [
        'orders_id' => $order_id,
        'orders_status_id' => $module_status_id,
        'date_added' => 'NOW()',
        'customer_notified' => 0,
        'comments' => $trans_result,
      ];

      tep_db_perform('orders_status_history', $sql_data);

// load the after_process function from the payment modules
      $this->after_process();

      $GLOBALS['hooks']->register_pipeline('reset');

      unset($_SESSION['cart_RBS_Worldpay_Hosted_ID']);

      tep_redirect(tep_href_link('checkout_success.php', '', 'SSL'));
    }

    protected function get_parameters() {
      $params = [
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_STATUS' => [
          'title' => 'Enable WorldPay Hosted Payment Pages',
          'desc' => 'Do you want to accept WorldPay Hosted Payment Pages payments?',
          'value' => 'True',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_INSTALLATION_ID' => [
          'title' => 'Installation ID',
          'desc' => 'The WorldPay Account Installation ID to accept payments for',
        ],
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_CALLBACK_PASSWORD' => [
          'title' => 'Callback Password',
          'desc' => 'The password sent to the callback processing script. This must be the same value defined in the WorldPay Merchant Interface.',
        ],
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_MD5_PASSWORD' => [
          'title' => 'MD5 Password',
          'desc' => 'The MD5 password to verify transactions with. This must be the same value defined in the WorldPay Merchant Interface.',
        ],
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TRANSACTION_METHOD' => [
          'title' => 'Transaction Method',
          'desc' => 'The processing method to use for each transaction.',
          'value' => 'Capture',
          'set_func' => "tep_cfg_select_option(['Pre-Authorization', 'Capture'], ",
        ],
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID' => [
          'title' => 'Set Preparing Order Status',
          'desc' => 'Set the status of prepared orders made with this payment module to this value',
          'value' => abstract_payment_module::ensure_order_status('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID', 'Preparing [WorldPay]'),
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
          'use_func' => 'tep_get_order_status_name',
        ],
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ORDER_STATUS_ID' => [
          'title' => 'Set Order Status',
          'desc' => 'Set the status of orders made with this payment module to this value',
          'value' => '0',
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
          'use_func' => 'tep_get_order_status_name',
        ],
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TRANSACTIONS_ORDER_STATUS_ID' => [
          'title' => 'Transactions Order Status Level',
          'desc' => 'Include WorldPay transaction information in this order status level.',
          'value' => abstract_payment_module::ensure_order_status('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TRANSACTIONS_ORDER_STATUS_ID', 'WorldPay [Transactions]'),
          'use_func' => 'tep_get_order_status_name',
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
        ],
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ZONE' => [
          'title' => 'Payment Zone',
          'desc' => 'If a zone is selected, only enable this payment method for that zone.',
          'value' => '0',
          'use_func' => 'tep_get_zone_class_title',
          'set_func' => 'tep_cfg_pull_down_zone_classes(',
        ],
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TESTMODE' => [
          'title' => 'Test Mode',
          'desc' => 'Should transactions be processed in test mode?',
          'value' => 'False',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_DEBUG_EMAIL' => [
          'title' => 'Debug E-Mail Address',
          'desc' => 'All parameters of an invalid transaction will be sent to this email address if one is entered.',
        ],
        'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_SORT_ORDER' => [
          'title' => 'Sort order of display.',
          'desc' => 'Sort order of display. Lowest is displayed first.',
          'value' => '0',
        ],
      ];

      return $params;
    }

// format prices without currency formatting
    public function format_raw($number, $currency_code = '', $currency_value = '') {
      global $currencies;

      if (empty($currency_code) || !$this->is_set($currency_code)) {
        $currency_code = $_SESSION['currency'];
      }

      if (empty($currency_value) || !is_numeric($currency_value)) {
        $currency_value = $currencies->currencies[$currency_code]['value'];
      }

      return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '.', '');
    }

    public function sendDebugEmail($response = []) {
      if (tep_not_null(MODULE_PAYMENT_RBSWORLDPAY_HOSTED_DEBUG_EMAIL)) {
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
          tep_mail('', MODULE_PAYMENT_RBSWORLDPAY_HOSTED_DEBUG_EMAIL, 'WorldPay Hosted Debug E-Mail', trim($email_body), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }
      }
    }

  }
