<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class paypoint_secpay extends abstract_payment_module {

    const CONFIG_KEY_BASE = 'MODULE_PAYMENT_PAYPOINT_SECPAY_';

    private $signature = 'paypoint|paypoint_secpay|1.0|2.3';
    public $form_action_url = 'https://www.secpay.com/java-bin/ValCard';

    public function process_button() {
      global $order, $currencies;

      switch (MODULE_PAYMENT_PAYPOINT_SECPAY_CURRENCY) {
        case 'Default Currency':
          $sec_currency = DEFAULT_CURRENCY;
          break;
        case 'Any Currency':
        default:
          $sec_currency = $_SESSION['currency'];
          break;
      }

      switch (MODULE_PAYMENT_PAYPOINT_SECPAY_TEST_STATUS) {
        case 'Always Fail':
          $test_status = 'false';
          break;
        case 'Production':
          $test_status = 'live';
          break;
        case 'Always Successful':
        default:
          $test_status = 'true';
          break;
      }

// Calculate the digest to send to SECPAY

      $digest_string = STORE_NAME . date('Ymdhis') . number_format($order->info['total'] * $currencies->get_value($sec_currency), $currencies->currencies[$sec_currency]['decimal_places'], '.', '') . MODULE_PAYMENT_PAYPOINT_SECPAY_REMOTE;

// There is a bug in the digest code, if there are any spaces in the trans id ( usually in the STORE_NAME
// SECPay will replace these with an _ and the hash is calculated of that so need to do a search and replace
// in the digest_string for spaces and replace with _
      $digest_string = str_replace(' ', '_', $digest_string);

      $digest = md5($digest_string);

// In case this gets 'fixed' at the SECPay end do a search and replace on the trans_id too
      $trans_id_string = STORE_NAME . date('Ymdhis');
      $trans_id = str_replace(' ', '_', $trans_id_string);

      $process_button_string = tep_draw_hidden_field('merchant', MODULE_PAYMENT_PAYPOINT_SECPAY_MERCHANT_ID)
                             . tep_draw_hidden_field('trans_id', $trans_id)
                             . tep_draw_hidden_field('amount', number_format($order->info['total'] * $currencies->get_value($sec_currency), $currencies->currencies[$sec_currency]['decimal_places'], '.', ''))
                             . tep_draw_hidden_field('bill_name', $order->billing['name'])
                             . tep_draw_hidden_field('bill_addr_1', $order->billing['street_address'])
                             . tep_draw_hidden_field('bill_addr_2', $order->billing['suburb'])
                             . tep_draw_hidden_field('bill_city', $order->billing['city'])
                             . tep_draw_hidden_field('bill_state', $order->billing['state'])
                             . tep_draw_hidden_field('bill_post_code', $order->billing['postcode'])
                             . tep_draw_hidden_field('bill_country', $order->billing['country']['title'])
                             . tep_draw_hidden_field('bill_tel', $order->customer['telephone'])
                             . tep_draw_hidden_field('bill_email', $order->customer['email_address'])
                             . tep_draw_hidden_field('ship_name', $order->delivery['name'])
                             . tep_draw_hidden_field('ship_addr_1', $order->delivery['street_address'])
                             . tep_draw_hidden_field('ship_addr_2', $order->delivery['suburb'])
                             . tep_draw_hidden_field('ship_city', $order->delivery['city'])
                             . tep_draw_hidden_field('ship_state', $order->delivery['state'])
                             . tep_draw_hidden_field('ship_post_code', $order->delivery['postcode'])
                             . tep_draw_hidden_field('ship_country', $order->delivery['country']['title'])
                             . tep_draw_hidden_field('currency', $sec_currency)
                             . tep_draw_hidden_field('callback', tep_href_link('checkout_process.php', '', 'SSL', false) . ';' . tep_href_link('checkout_payment.php', 'payment_error=' . $this->code, 'SSL', false))
                             . tep_draw_hidden_field(session_name(), session_id())
                             . tep_draw_hidden_field('options', 'test_status=' . $test_status . ',dups=false,cb_flds=' . session_name())
                             . tep_draw_hidden_field('digest', $digest);

      return $process_button_string;
    }

    public function before_process() {
      if ( ($_GET['valid'] == 'true') && ($_GET['code'] == 'A') && !empty($_GET['auth_code']) && empty($_GET['resp_code']) && !empty($_GET[session_name()]) ) {
        $DIGEST_PASSWORD = MODULE_PAYMENT_PAYPOINT_SECPAY_READERS_DIGEST;
        list($REQUEST_URI, $CHECK_SUM) = explode('hash=', $_SERVER['REQUEST_URI']);

        if ($_GET['hash'] != md5($REQUEST_URI . $DIGEST_PASSWORD)) {
          tep_redirect(tep_href_link('checkout_payment.php', session_name() . '=' . $_GET[session_name()] . '&payment_error=' . $this->code ."&detail=hash", 'SSL', false, false));
        }
      } else {
        tep_redirect(tep_href_link('checkout_payment.php', session_name() . '=' . $_GET[session_name()] . '&payment_error=' . $this->code, 'SSL', false, false));
      }
    }

    public function get_error() {
      if ($_GET['code'] == 'N') {
        $error = MODULE_PAYMENT_PAYPOINT_SECPAY_TEXT_ERROR_MESSAGE_N;
      } elseif ($_GET['code'] == 'C') {
        $error = MODULE_PAYMENT_PAYPOINT_SECPAY_TEXT_ERROR_MESSAGE_C;
      } else {
        $error = MODULE_PAYMENT_PAYPOINT_SECPAY_TEXT_ERROR_MESSAGE;
      }

      return [
        'title' => MODULE_PAYMENT_PAYPOINT_SECPAY_TEXT_ERROR,
        'error' => $error,
      ];
    }

    protected function get_parameters() {
      return [
        'MODULE_PAYMENT_PAYPOINT_SECPAY_STATUS' => [
          'title' => 'Enable PayPoint.net SECPay Module',
          'value' => 'False',
          'desc' => 'Do you want to accept PayPoint.net SECPay payments?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_PAYPOINT_SECPAY_MERCHANT_ID' => [
          'title' => 'Merchant ID',
          'value' => 'secpay',
          'desc' => 'Merchant ID to use for the SECPay service',
        ],
        'MODULE_PAYMENT_PAYPOINT_SECPAY_CURRENCY' => [
          'title' => 'Transaction Currency',
          'value' => 'Any Currency',
          'desc' => 'The currency to use for credit card transactions',
          'set_func' => "tep_cfg_select_option(['Any Currency', 'Default Currency'], ",
        ],
        'MODULE_PAYMENT_PAYPOINT_SECPAY_TEST_STATUS' => [
          'title' => 'Transaction Mode',
          'value' => 'Always Successful',
          'desc' => 'Transaction mode to use for the PayPoint.net SECPay service',
          'set_func' => "tep_cfg_select_option(['Always Successful', 'Always Fail', 'Production'], ",
        ],
        'MODULE_PAYMENT_PAYPOINT_SECPAY_SORT_ORDER' => [
          'title' => 'Sort order of display.',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
        'MODULE_PAYMENT_PAYPOINT_SECPAY_ZONE' => [
          'title' => 'Payment Zone',
          'value' => '0',
          'desc' => 'If a zone is selected, only enable this payment method for that zone.',
          'use_func' => 'tep_get_zone_class_title',
          'set_func' => 'tep_cfg_pull_down_zone_classes(',
        ],
        'MODULE_PAYMENT_PAYPOINT_SECPAY_ORDER_STATUS_ID' => [
          'title' => 'Set Order Status',
          'value' => '0',
          'desc' => 'Set the status of orders made with this payment module to this value',
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
          'use_func' => 'tep_get_order_status_name',
        ],
        'MODULE_PAYMENT_PAYPOINT_SECPAY_REMOTE' => [
          'title' => 'Remote Password',
          'value' => 'secpay',
          'desc' => 'The Remote Password needs to be created in the PayPoint extranet.',
        ],
        'MODULE_PAYMENT_PAYPOINT_SECPAY_READERS_DIGEST' => [
          'title' => 'Digest Key',
          'value' => 'secpay',
          'desc' => 'The Digest Key needs to be created in the PayPoint extranet.',
        ],
      ];
    }

  }
