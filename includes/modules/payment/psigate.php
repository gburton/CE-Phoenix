<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class psigate extends abstract_payment_module {

    const CONFIG_KEY_BASE = 'MODULE_PAYMENT_PSIGATE_';

    public $form_action_url = 'https://order.psigate.com/psigate.asp';

    public function javascript_validation() {
      if (MODULE_PAYMENT_PSIGATE_INPUT_MODE == 'Local') {
        $js = 'if (payment_value == "' . $this->code . '") {' . "\n" .
              '  var psigate_cc_number = document.checkout_payment.psigate_cc_number.value;' . "\n" .
              '  if (psigate_cc_number == "" || psigate_cc_number.length < ' . MODULE_PAYMENT_PSIGATE_CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
              '    error_message = error_message + "' . sprintf(MODULE_PAYMENT_PSIGATE_TEXT_JS_CC_NUMBER, MODULE_PAYMENT_PSIGATE_CC_NUMBER_MIN_LENGTH) . '";' . "\n" .
              '    error = 1;' . "\n" .
              '  }' . "\n" .
              '}' . "\n";

        return $js;
      } else {
        return false;
      }
    }

    public function selection() {
      global $order;

      if (MODULE_PAYMENT_PSIGATE_INPUT_MODE == 'Local') {
        for ($i = 1; $i <= 12; $i++) {
          $expires_month[] = ['id' => sprintf('%02d', $i), 'text' => strftime('%B', mktime(0, 0, 0, $i, 1, 2000))];
        }

        $today = getdate();
        for ($i = $today['year']; $i < $today['year'] + 10; $i++) {
          $expires_year[] = ['id' => strftime('%y', mktime(0, 0, 0, 1, 1, $i)), 'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))];
        }

        return [
          'id' => $this->code,
          'module' => $this->title,
          'fields' => [
            [ 'title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_OWNER,
              'field' => $order->billing['name'],
            ],
            [ 'title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_NUMBER,
              'field' => tep_draw_input_field('psigate_cc_number'),
            ],
            [ 'title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_EXPIRES,
              'field' => tep_draw_pull_down_menu('psigate_cc_expires_month', $expires_month) . '&nbsp;' . tep_draw_pull_down_menu('psigate_cc_expires_year', $expires_year),
            ],
          ],
        ];
      }

      return parent::selection();
    }

    public function pre_confirmation_check() {
      if (MODULE_PAYMENT_PSIGATE_INPUT_MODE == 'Local') {
        include 'includes/classes/cc_validation.php';

        $cc_validation = new cc_validation();
        $result = $cc_validation->validate($_POST['psigate_cc_number'], $_POST['psigate_cc_expires_month'], $_POST['psigate_cc_expires_year']);

        $error = '';
        switch ($result) {
          case -1:
            $error = sprintf(TEXT_CCVAL_ERROR_UNKNOWN_CARD, substr($cc_validation->cc_number, 0, 4));
            break;
          case -2:
          case -3:
          case -4:
            $error = TEXT_CCVAL_ERROR_INVALID_DATE;
            break;
          case false:
            $error = TEXT_CCVAL_ERROR_INVALID_NUMBER;
            break;
        }

        if ( ($result == false) || ($result < 1) ) {
          $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&psigate_cc_owner=' . urlencode($_POST['psigate_cc_owner']) . '&psigate_cc_expires_month=' . $_POST['psigate_cc_expires_month'] . '&psigate_cc_expires_year=' . $_POST['psigate_cc_expires_year'];

          tep_redirect(tep_href_link('checkout_payment.php', $payment_error_return, 'SSL', true, false));
        }

        $this->cc_card_type = $cc_validation->cc_type;
        $this->cc_card_number = $cc_validation->cc_number;
        $this->cc_expiry_month = $cc_validation->cc_expiry_month;
        $this->cc_expiry_year = $cc_validation->cc_expiry_year;
      } else {
        return false;
      }
    }

    public function confirmation() {
      global $order;

      if (MODULE_PAYMENT_PSIGATE_INPUT_MODE == 'Local') {
        $confirmation = [
          'title' => $this->title . ': ' . $this->cc_card_type,
          'fields' => [
            [ 'title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_OWNER,
              'field' => $order->billing['name']],
            [ 'title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_NUMBER,
              'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)],
            [ 'title' => MODULE_PAYMENT_PSIGATE_TEXT_CREDIT_CARD_EXPIRES,
              'field' => strftime('%B, %Y', mktime(0, 0, 0, $_POST['psigate_cc_expires_month'], 1, '20' . $_POST['psigate_cc_expires_year']))],
          ],
        ];

        return $confirmation;
      } else {
        return false;
      }
    }

    public function process_button() {
      global $order, $currencies;

      switch (MODULE_PAYMENT_PSIGATE_TRANSACTION_MODE) {
        case 'Always Good':
          $transaction_mode = '1';
          break;
        case 'Always Duplicate':
          $transaction_mode = '2';
          break;
        case 'Always Decline':
          $transaction_mode = '3';
          break;
        case 'Production':
        default:
          $transaction_mode = '0';
          break;
      }

      switch (MODULE_PAYMENT_PSIGATE_TRANSACTION_TYPE) {
        case 'Sale':
          $transaction_type = '0';
          break;
        case 'PostAuth':
          $transaction_type = '2';
          break;
        case 'PreAuth':
        default:
          $transaction_type = '1';
          break;
      }

      $process_button_string = tep_draw_hidden_field('MerchantID', MODULE_PAYMENT_PSIGATE_MERCHANT_ID)
                             . tep_draw_hidden_field('FullTotal', number_format($order->info['total'] * $currencies->get_value(MODULE_PAYMENT_PSIGATE_CURRENCY), $currencies->currencies[MODULE_PAYMENT_PSIGATE_CURRENCY]['decimal_places']))
                             . tep_draw_hidden_field('ThanksURL', tep_href_link('checkout_process.php', '', 'SSL', true))
                             . tep_draw_hidden_field('NoThanksURL', tep_href_link('checkout_payment.php', 'payment_error=' . $this->code, 'NONSSL', true))
                             . tep_draw_hidden_field('Bname', $order->billing['name'])
                             . tep_draw_hidden_field('Baddr1', $order->billing['street_address'])
                             . tep_draw_hidden_field('Bcity', $order->billing['city']);

      if ($order->billing['country']['iso_code_2'] == 'US') {
        $billing_state_query = tep_db_query("SELECT zone_code FROM zones WHERE zone_id = " . (int)$order->billing['zone_id']);
        $billing_state = tep_db_fetch_array($billing_state_query);

        $process_button_string .= tep_draw_hidden_field('Bstate', $billing_state['zone_code']);
      } else {
        $process_button_string .= tep_draw_hidden_field('Bstate', $order->billing['state']);
      }

      $process_button_string .= tep_draw_hidden_field('Bzip', $order->billing['postcode'])
                              . tep_draw_hidden_field('Bcountry', $order->billing['country']['iso_code_2'])
                              . tep_draw_hidden_field('Phone', $order->customer['telephone'])
                              . tep_draw_hidden_field('Email', $order->customer['email_address'])
                              . tep_draw_hidden_field('Sname', $order->delivery['name'])
                              . tep_draw_hidden_field('Saddr1', $order->delivery['street_address'])
                              . tep_draw_hidden_field('Scity', $order->delivery['city'])
                              . tep_draw_hidden_field('Sstate', $order->delivery['state'])
                              . tep_draw_hidden_field('Szip', $order->delivery['postcode'])
                              . tep_draw_hidden_field('Scountry', $order->delivery['country']['iso_code_2'])
                              . tep_draw_hidden_field('ChargeType', $transaction_type)
                              . tep_draw_hidden_field('Result', $transaction_mode)
                              . tep_draw_hidden_field('IP', $_SERVER['REMOTE_ADDR']);

      if (MODULE_PAYMENT_PSIGATE_INPUT_MODE == 'Local') {
        $process_button_string .= tep_draw_hidden_field('CardNumber', $this->cc_card_number)
                                . tep_draw_hidden_field('ExpMonth', $this->cc_expiry_month)
                                . tep_draw_hidden_field('ExpYear', substr($this->cc_expiry_year, -2));
      }

      return $process_button_string;
    }

    public function get_error() {
      if (isset($_GET['ErrMsg']) && tep_not_null($_GET['ErrMsg'])) {
        $error = stripslashes(urldecode($_GET['ErrMsg']));
      } elseif (isset($_GET['Err']) && tep_not_null($_GET['Err'])) {
        $error = stripslashes(urldecode($_GET['Err']));
      } elseif (isset($_GET['error']) && tep_not_null($_GET['error'])) {
        $error = stripslashes(urldecode($_GET['error']));
      } else {
        $error = MODULE_PAYMENT_PSIGATE_TEXT_ERROR_MESSAGE;
      }

      return [
        'title' => MODULE_PAYMENT_PSIGATE_TEXT_ERROR,
        'error' => $error,
      ];
    }

    public function get_parameters() {
      return [
        'MODULE_PAYMENT_PSIGATE_STATUS' => [
          'title' => 'Enable PSiGate Module',
          'value' => 'True',
          'desc' => 'Do you want to accept PSiGate payments?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_PSIGATE_MERCHANT_ID' => [
          'title' => 'Merchant ID',
          'value' => 'teststorewithcard',
          'desc' => 'Merchant ID used for the PSiGate service',
        ],
        'MODULE_PAYMENT_PSIGATE_TRANSACTION_MODE' => [
          'title' => 'Transaction Mode',
          'value' => 'Always Good',
          'desc' => 'Transaction mode to use for the PSiGate service',
          'set_func' => "tep_cfg_select_option(['Production', 'Always Good', 'Always Duplicate', 'Always Decline'], ",
        ],
        'MODULE_PAYMENT_PSIGATE_TRANSACTION_TYPE' => [
          'title' => 'Transaction Type',
          'value' => 'PreAuth',
          'desc' => 'Transaction type to use for the PSiGate service',
          'set_func' => "tep_cfg_select_option(['Sale', 'PreAuth', 'PostAuth'], ",
        ],
        'MODULE_PAYMENT_PSIGATE_INPUT_MODE' => [
          'title' => 'Credit Card Collection',
          'value' => 'Local',
          'desc' => 'Should the credit card details be collected locally or remotely at PSiGate?',
          'set_func' => "tep_cfg_select_option(['Local', 'Remote'], ",
        ],
        'MODULE_PAYMENT_PSIGATE_CURRENCY' => [
          'title' => 'Transaction Currency',
          'value' => 'USD',
          'desc' => 'The currency to use for credit card transactions',
          'set_func' => "tep_cfg_select_option(['CAD', 'USD'], ",
        ],
        'MODULE_PAYMENT_PSIGATE_SORT_ORDER' => [
          'title' => 'Sort order of display.',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
        'MODULE_PAYMENT_PSIGATE_CC_NUMBER_MIN_LENGTH' => [
          'title' => 'Credit Card Number',
          'value' => '10',
          'desc' => 'Minimum length of credit card number',
        ],
        'MODULE_PAYMENT_PSIGATE_ZONE' => [
          'title' => 'Payment Zone',
          'value' => '0',
          'desc' => 'If a zone is selected, only enable this payment method for that zone.',
          'use_func' => 'tep_get_zone_class_title',
          'set_func' => 'tep_cfg_pull_down_zone_classes(',
        ],
        'MODULE_PAYMENT_PSIGATE_ORDER_STATUS_ID' => [
          'title' => 'Set Order Status',
          'value' => '0',
          'desc' => 'Set the status of orders made with this payment module to this value',
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
          'use_func' => 'tep_get_order_status_name',
        ],
      ];
    }

  }
