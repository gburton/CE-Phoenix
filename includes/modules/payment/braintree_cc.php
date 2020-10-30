<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class braintree_cc extends abstract_payment_module {

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

    const CONFIG_KEY_BASE = 'MODULE_PAYMENT_BRAINTREE_CC_';

    private $signature = 'braintree|braintree_cc|1.1|2.3';
    private $api_version = '1';
    private $token;
    private $result;

    public function __construct() {
      parent::__construct();

      if ( defined('MODULE_PAYMENT_BRAINTREE_CC_STATUS') ) {
        if ( MODULE_PAYMENT_BRAINTREE_CC_TRANSACTION_SERVER == 'Sandbox' ) {
          $this->title .= ' [Sandbox]';
          $this->public_title .= ' (' . $this->code . '; Sandbox)';
        }
      }

      $exts = array_filter(['xmlwriter', 'SimpleXML', 'openssl', 'dom', 'hash', 'curl'], function ($extension) { return !extension_loaded($extension); });

      $braintree_error = null;
      if ( !empty($exts) ) {
        $braintree_error = sprintf(MODULE_PAYMENT_BRAINTREE_CC_ERROR_ADMIN_PHP_EXTENSIONS, implode('<br>', $exts));
      }

      if ( !isset($braintree_error) && defined('MODULE_PAYMENT_BRAINTREE_CC_STATUS') ) {
        if ( !tep_not_null(MODULE_PAYMENT_BRAINTREE_CC_MERCHANT_ID) || !tep_not_null(MODULE_PAYMENT_BRAINTREE_CC_PUBLIC_KEY) || !tep_not_null(MODULE_PAYMENT_BRAINTREE_CC_PRIVATE_KEY) || !tep_not_null(MODULE_PAYMENT_BRAINTREE_CC_CLIENT_KEY) ) {
          $braintree_error = MODULE_PAYMENT_BRAINTREE_CC_ERROR_ADMIN_CONFIGURATION;
        }
      }

      if ( !isset($braintree_error) && defined('MODULE_PAYMENT_BRAINTREE_CC_STATUS') ) {
        $ma_error = true;

        if ( tep_not_null(MODULE_PAYMENT_BRAINTREE_CC_MERCHANT_ACCOUNTS) ) {
          $mas = explode(';', MODULE_PAYMENT_BRAINTREE_CC_MERCHANT_ACCOUNTS);

          foreach ( $mas as $a ) {
            $ac = explode(':', $a, 2);

            if ( isset($ac[1]) && ($ac[1] == DEFAULT_CURRENCY) ) {
              $ma_error = false;
              break;
            }
          }
        }

        if ( $ma_error === true ) {
          $braintree_error = sprintf(MODULE_PAYMENT_BRAINTREE_CC_ERROR_ADMIN_MERCHANT_ACCOUNTS, DEFAULT_CURRENCY);
        }
      }

      if ( isset($braintree_error) ) {
        $this->description = '<div class="alert alert-warning">' . $braintree_error . '</div>' . $this->description;

        $this->enabled = false;
      } else {
        if ( !class_exists('Braintree') ) {
          require DIR_FS_CATALOG . 'includes/apps/braintree_cc/Braintree.php';
        }

        spl_autoload_register('tep_braintree_autoloader');

        $this->api_version .= ' [' . Braintree_Version::get() . ']';
      }
    }

    public function pre_confirmation_check() {
      if ( isset($GLOBALS['oscTemplate']) && ($GLOBALS['oscTemplate'] instanceof oscTemplate) ) {
        $GLOBALS['oscTemplate']->addBlock('<style>.date-fields .form-control {width:auto;display:inline-block}</style>', 'header_tags');
        $GLOBALS['oscTemplate']->addBlock($this->getSubmitCardDetailsJavascript(), 'footer_scripts');
      }
    }

    public function confirmation() {
      global $order, $currencies;

      $months = [];

      for ($i = 1; $i <= 12; $i++) {
        $months[] = [
          'id' => tep_output_string(sprintf('%02d', $i)),
          'text' => tep_output_string_protected(sprintf('%02d', $i)),
        ];
      }

      $today = getdate();
      $years = [];

      for ($i = $today['year']; $i < $today['year'] + 10; $i++) {
        $years[] = [
          'id' => tep_output_string(strftime('%Y',mktime(0, 0, 0, 1, 1, $i))),
          'text' => tep_output_string_protected(strftime('%Y',mktime(0, 0, 0, 1, 1, $i))),
        ];
      }

      $content = '';

      if ( !$this->isValidCurrency($_SESSION['currency']) ) {
        $content .= sprintf(MODULE_PAYMENT_BRAINTREE_CC_CURRENCY_CHARGE, $currencies->format($order->info['total'], true, DEFAULT_CURRENCY), DEFAULT_CURRENCY, $_SESSION['currency']);
      }

      if ( MODULE_PAYMENT_BRAINTREE_CC_TOKENS == 'True' ) {
        $tokens_query = tep_db_query("SELECT id, card_type, number_filtered, expiry_date FROM customers_braintree_tokens WHERE customers_id = '" . (int)$_SESSION['customer_id'] . "' ORDER BY date_added");

        if ( tep_db_num_rows($tokens_query) > 0 ) {
          $content .= '<table class="table" id="braintree_table">';

          while ( $tokens = tep_db_fetch_array($tokens_query) ) {
            $content .= '<tr class="moduleRow" id="braintree_card_' . (int)$tokens['id'] . '">'
                      . '  <td><input type="radio" name="braintree_card" value="' . (int)$tokens['id'] . '" /></td>'
                      . '  <td>' . MODULE_PAYMENT_BRAINTREE_CC_CREDITCARD_LAST_4 . '&nbsp;' . tep_output_string_protected($tokens['number_filtered']) . '&nbsp;&nbsp;' . tep_output_string_protected(substr($tokens['expiry_date'], 0, 2) . '/' . substr($tokens['expiry_date'], 2)) . '&nbsp;&nbsp;' . tep_output_string_protected($tokens['card_type']) . '</td>'
                      . '</tr>';

            if ( MODULE_PAYMENT_BRAINTREE_CC_VERIFY_WITH_CVV == 'True' ) {
              $content .= '<tr class="moduleRowExtra" id="braintree_card_cvv_' . (int)$tokens['id'] . '">'
                        . '  <td>&nbsp;</td>'
                        . '  <td>' . MODULE_PAYMENT_BRAINTREE_CC_CREDITCARD_CVV . '&nbsp;<input type="text" size="5" maxlength="4" autocomplete="off" data-encrypted-name="token_cvv[' . (int)$tokens['id'] . ']" /></td>'
                        . '</tr>';
            }
          }

          $content .= '<tr class="moduleRow" id="braintree_card_0">'
                    . '  <td><input type="radio" name="braintree_card" value="0" /></td>'
                    . '  <td>' . MODULE_PAYMENT_BRAINTREE_CC_CREDITCARD_NEW . '</td>'
                    . '</tr>'
                    . '</table>';
        }
      }

      $content .= '<table class="table" id="braintree_table_new_card">'
                . '<tr>'
                . '  <td class="w-25">' . MODULE_PAYMENT_BRAINTREE_CC_CREDITCARD_OWNER . '</td>'
                . '  <td>' . tep_draw_input_field('name', $order->billing['firstname'] . ' ' . $order->billing['lastname']) . '</td>'
                . '</tr>'
                . '<tr>'
                . '  <td class="w-25">' . MODULE_PAYMENT_BRAINTREE_CC_CREDITCARD_NUMBER . '</td>'
                . '  <td><input type="text" maxlength="20" autocomplete="off" data-encrypted-name="number" /></td>'
                . '</tr>'
                . '<tr>'
                . '  <td class="w-25">' . MODULE_PAYMENT_BRAINTREE_CC_CREDITCARD_EXPIRY . '</td>'
                . '  <td class="date-fields">' . tep_draw_pull_down_menu('month', $months) . ' / ' . tep_draw_pull_down_menu('year', $years) . '</td>'
                . '</tr>';

      if ( MODULE_PAYMENT_BRAINTREE_CC_VERIFY_WITH_CVV == 'True' ) {
        $content .= '<tr>'
                  . '  <td class="w-25">' . MODULE_PAYMENT_BRAINTREE_CC_CREDITCARD_CVV . '</td>'
                  . '  <td><input type="text" size="5" maxlength="4" autocomplete="off" data-encrypted-name="cvv" /></td>'
                  . '</tr>';
      }

      if ( MODULE_PAYMENT_BRAINTREE_CC_TOKENS == 'True' ) {
        $content .= '<tr>'
                  . '  <td class="w-25">&nbsp;</td>'
                  . '  <td>' . tep_draw_checkbox_field('cc_save', 'true') . ' ' . MODULE_PAYMENT_BRAINTREE_CC_CREDITCARD_SAVE . '</td>'
                  . '</tr>';
      }

      $content .= '</table>';

      if ( !(($GLOBALS['oscTemplate'] ?? null) instanceof oscTemplate) ) {
        $content .= $this->getSubmitCardDetailsJavascript();
      }

      $confirmation = ['title' => $content];

      return $confirmation;
    }

    public function before_process() {
      global $order;

      $this->token = null;
      $braintree_token_cvv = null;

      if ( MODULE_PAYMENT_BRAINTREE_CC_TOKENS == 'True' ) {
        if ( isset($_POST['braintree_card']) && is_numeric($_POST['braintree_card']) && ($_POST['braintree_card'] > 0) ) {
          $token_query = tep_db_query("SELECT braintree_token FROM customers_braintree_tokens WHERE id = '" . (int)$_POST['braintree_card'] . "' AND customers_id = '" . (int)$_SESSION['customer_id'] . "'");

          if ( tep_db_num_rows($token_query) === 1 ) {
            $token = tep_db_fetch_array($token_query);

            $this->token = $token['braintree_token'];

            if ( MODULE_PAYMENT_BRAINTREE_CC_VERIFY_WITH_CVV == 'True' ) {

              if ( isset($_POST['token_cvv'][$_POST['braintree_card']]) ) {
                $braintree_token_cvv = $_POST['token_cvv'][$_POST['braintree_card']];
              }

              if ( empty($braintree_token_cvv) ) {
                tep_redirect(tep_href_link('checkout_payment.php', 'payment_error=' . $this->code . '&error=cardcvv', 'SSL'));
              }
            }
          }
        }
      }

      if ( !isset($this->token) ) {
        $cc_owner = $_POST['name'] ?? null;
        $cc_number = $_POST['number'] ?? null;
        $cc_expires_month = $_POST['month'] ?? null;
        $cc_expires_year = $_POST['year'] ?? null;

        if ( MODULE_PAYMENT_BRAINTREE_CC_VERIFY_WITH_CVV == 'True' ) {
          $cc_cvv = $_POST['cvv'] ?? null;
        }

        $months = [];

        for ($i = 1; $i <= 12; $i++) {
          $months[] = sprintf('%02d', $i);
        }

        $today = getdate();
        $years = [];

        for ($i = $today['year']; $i < $today['year'] + 10; $i++) {
          $years[] = strftime('%Y',mktime(0, 0, 0, 1, 1, $i));
        }

        if ( empty($cc_owner) ) {
          tep_redirect(tep_href_link('checkout_payment.php', 'payment_error=' . $this->code . '&error=cardowner', 'SSL'));
        }

        if ( empty($cc_number) ) {
          tep_redirect(tep_href_link('checkout_payment.php', 'payment_error=' . $this->code . '&error=cardnumber', 'SSL'));
        }

        if ( !isset($cc_expires_month) || !in_array($cc_expires_month, $months) ) {
          tep_redirect(tep_href_link('checkout_payment.php', 'payment_error=' . $this->code . '&error=cardexpires', 'SSL'));
        }

        if ( !isset($cc_expires_year) || !in_array($cc_expires_year, $years) ) {
          tep_redirect(tep_href_link('checkout_payment.php', 'payment_error=' . $this->code . '&error=cardexpires', 'SSL'));
        }

        if ( ($cc_expires_year == date('Y')) && ($cc_expires_month < date('m')) ) {
          tep_redirect(tep_href_link('checkout_payment.php', 'payment_error=' . $this->code . '&error=cardexpires', 'SSL'));
        }

        if ( MODULE_PAYMENT_BRAINTREE_CC_VERIFY_WITH_CVV == 'True' ) {
          if ( empty($cc_cvv) ) {
            tep_redirect(tep_href_link('checkout_payment.php', 'payment_error=' . $this->code . '&error=cardcvv', 'SSL'));
          }
        }
      }

      $this->result = null;

      Braintree_Configuration::environment(MODULE_PAYMENT_BRAINTREE_CC_TRANSACTION_SERVER == 'Live' ? 'production' : 'sandbox');
      Braintree_Configuration::merchantId(MODULE_PAYMENT_BRAINTREE_CC_MERCHANT_ID);
      Braintree_Configuration::publicKey(MODULE_PAYMENT_BRAINTREE_CC_PUBLIC_KEY);
      Braintree_Configuration::privateKey(MODULE_PAYMENT_BRAINTREE_CC_PRIVATE_KEY);

      $_SESSION['currency'] = $this->getTransactionCurrency();

      $data = [
        'amount' => $this->format_raw($order->info['total'], $_SESSION['currency']),
        'merchantAccountId' => $this->getMerchantAccountId($_SESSION['currency']),
        'creditCard' => ['cardholderName' => $cc_owner],
        'customer' => [
          'firstName' => $order->customer['firstname'],
          'lastName' => $order->customer['lastname'],
          'company' => $order->customer['company'],
          'phone' => $order->customer['telephone'],
          'email' => $order->customer['email_address'],
        ],
        'billing' => [
          'firstName' => $order->billing['firstname'],
          'lastName' => $order->billing['lastname'],
          'company' => $order->billing['company'],
          'streetAddress' => $order->billing['street_address'],
          'extendedAddress' => $order->billing['suburb'],
          'locality' => $order->billing['city'],
          'region' => tep_get_zone_name($order->billing['country_id'], $order->billing['zone_id'], $order->billing['state']),
          'postalCode' => $order->billing['postcode'],
          'countryCodeAlpha2' => $order->billing['country']['iso_code_2'],
        ],
        'options' => [],
      ];

      if ( MODULE_PAYMENT_BRAINTREE_CC_TRANSACTION_METHOD == 'Payment' ) {
        $data['options']['submitForSettlement'] = true;
      }

      if ( $order->content_type != 'virtual' ) {
        $data['shipping'] = [
          'firstName' => $order->delivery['firstname'],
          'lastName' => $order->delivery['lastname'],
          'company' => $order->delivery['company'],
          'streetAddress' => $order->delivery['street_address'],
          'extendedAddress' => $order->delivery['suburb'],
          'locality' => $order->delivery['city'],
          'region' => tep_get_zone_name($order->delivery['country_id'], $order->delivery['zone_id'], $order->delivery['state']),
          'postalCode' => $order->delivery['postcode'],
          'countryCodeAlpha2' => $order->delivery['country']['iso_code_2'],
        ];
      }

      if ( !isset($this->token) ) {
        $data['creditCard']['number'] = $cc_number;
        $data['creditCard']['expirationMonth'] = $cc_expires_month;
        $data['creditCard']['expirationYear'] = $cc_expires_year;

        if ( MODULE_PAYMENT_BRAINTREE_CC_VERIFY_WITH_CVV == 'True' ) {
          $data['creditCard']['cvv'] = $cc_cvv;
        }

        if ( (MODULE_PAYMENT_BRAINTREE_CC_TOKENS == 'True') && isset($_POST['cc_save']) && ($_POST['cc_save'] == 'true') ) {
          $data['options']['storeInVaultOnSuccess'] = true;
        }
      } else {
        $data['paymentMethodToken'] = $this->token;

        if ( MODULE_PAYMENT_BRAINTREE_CC_VERIFY_WITH_CVV == 'True' ) {
          $data['creditCard']['cvv'] = $braintree_token_cvv;
        }
      }

      $error = false;

      try {
        $this->result = Braintree_Transaction::sale($data);
      } catch ( Exception $e ) {
        $error = true;
      }

      if ( ($error === false) && ($this->result->success) ) {
        return true;
      }

      if ( $this->result->transaction) {
        if ( !empty($this->result->message) ) {
          $_SESSION['braintree_error'] = $this->result->message;
        }
      } else {
        $braintree_error = '';

        if ( isset($this->result->errors) ) {
          foreach ( $this->result->errors->deepAll() as $error ) {
            $braintree_error .= $error->message . ' ';
          }

          if ( !empty($braintree_error) ) {
            $braintree_error = substr($braintree_error, 0, -1);
          }
        }

        if ( !empty($braintree_error) ) {
          $_SESSION['braintree_error'] = $braintree_error;
        }
      }

      tep_redirect(tep_href_link('checkout_payment.php', 'payment_error=' . $this->code, 'SSL'));
    }

    public function after_process() {
      global $order_id;

      $status_comment = ['Transaction ID: ' . $this->result->transaction->id];

      if ( (MODULE_PAYMENT_BRAINTREE_CC_TOKENS == 'True') && isset($_POST['cc_save']) && ($_POST['cc_save'] == 'true') && !isset($this->token) && isset($this->result->transaction->creditCard['token']) ) {
        $token = tep_db_prepare_input($this->result->transaction->creditCard['token']);
        $type = tep_db_prepare_input($this->result->transaction->creditCard['cardType']);
        $number = tep_db_prepare_input($this->result->transaction->creditCard['last4']);
        $expiry = tep_db_prepare_input($this->result->transaction->creditCard['expirationMonth'] . $this->result->transaction->creditCard['expirationYear']);

        $check_query = tep_db_query("SELECT id FROM customers_braintree_tokens WHERE customers_id = '" . (int)$_SESSION['customer_id'] . "' AND braintree_token = '" . tep_db_input($token) . "' limit 1");
        if ( tep_db_num_rows($check_query) < 1 ) {
          $sql_data = [
            'customers_id' => (int)$_SESSION['customer_id'],
            'braintree_token' => $token,
            'card_type' => $type,
            'number_filtered' => $number,
            'expiry_date' => $expiry,
            'date_added' => 'NOW()',
          ];

          tep_db_perform('customers_braintree_tokens', $sql_data);
        }

        $status_comment[] = 'Token Created: Yes';
      } elseif ( isset($this->token) ) {
        $status_comment[] = 'Token Used: Yes';
      }

      $sql_data = [
        'orders_id' => $order_id,
        'orders_status_id' => MODULE_PAYMENT_BRAINTREE_CC_TRANSACTION_ORDER_STATUS_ID,
        'date_added' => 'NOW()',
        'customer_notified' => '0',
        'comments' => implode("\n", $status_comment),
      ];

      tep_db_perform('orders_status_history', $sql_data);
    }

    public function get_error() {
      $message = MODULE_PAYMENT_BRAINTREE_CC_ERROR_GENERAL;

      if ( !empty($_GET['error']) ) {
        switch ($_GET['error']) {
          case 'cardowner':
            $message = MODULE_PAYMENT_BRAINTREE_CC_ERROR_CARDOWNER;
            break;

          case 'cardnumber':
            $message = MODULE_PAYMENT_BRAINTREE_CC_ERROR_CARDNUMBER;
            break;

          case 'cardexpires':
            $message = MODULE_PAYMENT_BRAINTREE_CC_ERROR_CARDEXPIRES;
            break;

          case 'cardcvv':
            $message = MODULE_PAYMENT_BRAINTREE_CC_ERROR_CARDCVV;
            break;
        }
      } elseif ( isset($_SESSION['braintree_error']) ) {
        $message = $_SESSION['braintree_error'] . ' ' . $message;

        unset($_SESSION['braintree_error']);
      }

      $error = [
        'title' => MODULE_PAYMENT_BRAINTREE_CC_ERROR_TITLE,
        'error' => $message,
      ];

      return $error;
    }

    protected function get_parameters() {
      if ( tep_db_num_rows(tep_db_query("SHOW TABLES LIKE 'customers_braintree_tokens'")) != 1 ) {
        $sql = <<<EOSQL
CREATE TABLE customers_braintree_tokens (
  id int NOT NULL auto_increment,
  customers_id int NOT NULL,
  braintree_token varchar(255) NOT NULL,
  card_type varchar(32) NOT NULL,
  number_filtered varchar(20) NOT NULL,
  expiry_date char(6) NOT NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (id),
  KEY idx_cbraintreet_customers_id (customers_id),
  KEY idx_cbraintreet_token (braintree_token)
);
EOSQL;

        tep_db_query($sql);
      }

      return [
        'MODULE_PAYMENT_BRAINTREE_CC_STATUS' => [
          'title' => 'Enable Braintree Module',
          'desc' => 'Do you want to accept Braintree payments?',
          'value' => 'True',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_MERCHANT_ID' => [
          'title' => 'Merchant ID',
          'desc' => 'The Braintree account Merchant ID to use.',
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_PUBLIC_KEY' => [
          'title' => 'Public Key',
          'desc' => 'The Braintree account public key to use.',
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_PRIVATE_KEY' => [
          'title' => 'Private Key',
          'desc' => 'The Braintree account private key to use.',
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_CLIENT_KEY' => [
          'title' => 'Client Side Encryption Key',
          'desc' => 'The client side encryption key to use.',
          'set_func' => 'tep_cfg_braintree_cc_set_client_key(',
          'use_func' => 'tep_cfg_braintree_cc_show_client_key',
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_MERCHANT_ACCOUNTS' => [
          'title' => 'Merchant Accounts',
          'desc' => 'Merchant accounts and defined currencies.',
          'set_func' => 'tep_cfg_braintree_cc_set_merchant_accounts(',
          'use_func' => 'tep_cfg_braintree_cc_show_merchant_accounts',
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_TOKENS' => [
          'title' => 'Create Tokens',
          'desc' => 'Create and store tokens for card payments customers can use on their next purchase?',
          'value' => 'False',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_VERIFY_WITH_CVV' => [
          'title' => 'Verify With CVV',
          'desc' => 'Verify the credit card with the billing address with the Card Verification Value (CVV)?',
          'value' => 'True',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_TRANSACTION_METHOD' => [
          'title' => 'Transaction Method',
          'desc' => 'The processing method to use for each transaction.',
          'value' => 'Authorize',
          'set_func' => "tep_cfg_select_option(['Authorize', 'Payment'], ",
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_ORDER_STATUS_ID' => [
          'title' => 'Set Order Status',
          'desc' => 'Set the status of orders made with this payment module to this value',
          'value' => '0',
          'use_func' => 'tep_get_order_status_name',
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_TRANSACTION_ORDER_STATUS_ID' => [
          'title' => 'Transaction Order Status',
          'desc' => 'Include transaction information in this order status level',
          'value' => self::ensure_order_status('MODULE_PAYMENT_BRAINTREE_CC_TRANSACTION_ORDER_STATUS_ID', 'Braintree [Transactions]'),
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
          'use_func' => 'tep_get_order_status_name',
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_TRANSACTION_SERVER' => [
          'title' => 'Transaction Server',
          'desc' => 'Perform transactions on the production server or on the testing server.',
          'value' => 'Live',
          'set_func' => "tep_cfg_select_option(['Live', 'Sandbox'], ",
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_ZONE' => [
          'title' => 'Payment Zone',
          'desc' => 'If a zone is selected, only enable this payment method for that zone.',
          'value' => '0',
          'use_func' => 'tep_get_zone_class_title',
          'set_func' => 'tep_cfg_pull_down_zone_classes(',
        ],
        'MODULE_PAYMENT_BRAINTREE_CC_SORT_ORDER' => [
          'title' => 'Sort order of display.',
          'desc' => 'Sort order of display. Lowest is displayed first.',
          'value' => '0',
        ],
      ];
    }

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

    function getTransactionCurrency() {
      return $this->isValidCurrency($_SESSION['currency']) ? $_SESSION['currency'] : DEFAULT_CURRENCY;
    }

    function getMerchantAccountId($currency) {
      foreach ( explode(';', MODULE_PAYMENT_BRAINTREE_CC_MERCHANT_ACCOUNTS) as $ma ) {
        list($a, $c) = explode(':', $ma);

        if ( $c == $currency ) {
          return $a;
        }
      }

      return '';
    }

    function isValidCurrency($currency) {
      global $currencies;

      foreach ( explode(';', MODULE_PAYMENT_BRAINTREE_CC_MERCHANT_ACCOUNTS) as $combo ) {
        list($id, $c) = explode(':', $combo);

        if ( $c == $currency ) {
          return $currencies->is_set($c);
        }
      }

      return false;
    }

    function deleteCard($token, $token_id) {
      Braintree_Configuration::environment(MODULE_PAYMENT_BRAINTREE_CC_TRANSACTION_SERVER == 'Live' ? 'production' : 'sandbox');
      Braintree_Configuration::merchantId(MODULE_PAYMENT_BRAINTREE_CC_MERCHANT_ID);
      Braintree_Configuration::publicKey(MODULE_PAYMENT_BRAINTREE_CC_PUBLIC_KEY);
      Braintree_Configuration::privateKey(MODULE_PAYMENT_BRAINTREE_CC_PRIVATE_KEY);

      try {
        Braintree_CreditCard::delete($token);
      } catch ( Exception $e ) {
      }

      tep_db_query("DELETE FROM customers_braintree_tokens WHERE id = '" . (int)$token_id . "' AND customers_id = '" . (int)$_SESSION['customer_id'] . "' AND braintree_token = '" . tep_db_input(tep_db_prepare_input($token)) . "'");

      return (tep_db_affected_rows() === 1);
    }

    function getSubmitCardDetailsJavascript() {
      $braintree_client_key = MODULE_PAYMENT_BRAINTREE_CC_CLIENT_KEY;

      $js = <<<EOD
<script src="https://js.braintreegateway.com/v1/braintree.js"></script>
<script>
$(function() {
  $('form[name="checkout_confirmation"]').attr('id', 'braintree-payment-form');

  var braintree = Braintree.create('{$braintree_client_key}');
  braintree.onSubmitEncryptForm('braintree-payment-form');

  if ( $('#braintree_table').length > 0 ) {
    if ( typeof($('#braintree_table').parent().closest('table').attr('width')) == 'undefined' ) {
      $('#braintree_table').parent().closest('table').attr('width', '100%');
    }

    $('#braintree_table .moduleRowExtra').hide();

    $('#braintree_table_new_card').hide();

    $('form[name="checkout_confirmation"] input[name="braintree_card"]').change(function() {
      var selected = $(this).val();

      if ( selected == '0' ) {
        braintreeShowNewCardFields();
      } else {
        $('#braintree_table_new_card').hide();

        $('[id^="braintree_card_cvv_"]').hide();

        $('#braintree_card_cvv_' + selected).show();
      }

      $('tr[id^="braintree_card_"]').removeClass('moduleRowSelected');
      $('#braintree_card_' + selected).addClass('moduleRowSelected');
    });

    $('form[name="checkout_confirmation"] input[name="braintree_card"]:first').prop('checked', true).trigger('change');

    $('#braintree_table .moduleRow').hover(function() {
      $(this).addClass('moduleRowOver');
    }, function() {
      $(this).removeClass('moduleRowOver');
    }).click(function(event) {
      var target = $(event.target);

      if ( !target.is('input:radio') ) {
        $(this).find('input:radio').each(function() {
          if ( $(this).prop('checked') == false ) {
            $(this).prop('checked', true).trigger('change');
          }
        });
      }
    });
  } else {
    if ( typeof($('#braintree_table_new_card').parent().closest('table').attr('width')) == 'undefined' ) {
      $('#braintree_table_new_card').parent().closest('table').attr('width', '100%');
    }
  }
});

function braintreeShowNewCardFields() {
  $('[id^="braintree_card_cvv_"]').hide();

  $('#braintree_table_new_card').show();
}
</script>
EOD;

      return $js;
    }
  }

  function tep_cfg_braintree_cc_set_client_key($value, $name) {
    return tep_draw_textarea_field('configuration[' . $name . ']', '', '50', '12', $value);
  }

  function tep_cfg_braintree_cc_show_client_key($key) {
    $string = '';

    if ( strlen($key) > 0 ) {
      $string = substr($key, 0, 20) . ' ...';
    }

    return $string;
  }

  function tep_cfg_braintree_cc_get_data($value) {
    if (empty($value)) {
      return [];
    }

    $data = [];
    foreach ( explode(';', $value) as $ma ) {
      list($a, $currency) = explode(':', $ma);

      $data[$currency] = $a;
    }

    return $data;
  }

  function tep_cfg_braintree_cc_get_currencies() {
    $currencies = new currencies();

    $c_array = array_keys($currencies->currencies);
    sort($c_array);

    return $c_array;
  }

  function tep_cfg_braintree_cc_set_merchant_accounts($value, $key) {
    $data = tep_cfg_braintree_cc_get_data($value);

    $result = '';
    foreach ( tep_cfg_braintree_cc_get_currencies() as $c ) {
      $close = null;
      if ( $c == DEFAULT_CURRENCY ) {
        $result .= '<strong>';
        $close = '</strong>';
      }

      $result .= $c . ':';

      if ( isset($close) ) {
        $result .= $close;
      }

      $result .= '&nbsp;' . tep_draw_input_field('braintree_ma[' . $c . ']', ($data[$c] ?? '')) . '<br>';
    }

    if ( !empty($result) ) {
      $result = substr($result, 0, -strlen('<br>'));
    }

    $result .= tep_draw_hidden_field('configuration[' . $key . ']', $value);

    $result .= <<<EOD
<script>
$(function() {
  $('form[name="modules"]').submit(function() {
    var ma_string = '';

    $('form[name="modules"] input[name^="braintree_ma["]').each(function() {
      if ( $(this).val().length > 0 ) {
        ma_string += $(this).val() + ':' + $(this).attr('name').slice(13, -1) + ';';
      }
    });

    if ( ma_string.length > 0 ) {
      ma_string = ma_string.slice(0, -1);
    }

    $('form[name="modules"] input[name="configuration[{$key}]"]').val(ma_string);
  })
});
</script>
EOD;

    return $result;
  }

  function tep_cfg_braintree_cc_show_merchant_accounts($value) {
    $data = tep_cfg_braintree_cc_get_data($value);

    $result = '';
    foreach ( tep_cfg_braintree_cc_get_currencies() as $c ) {
      $close = null;
      if ( $c == DEFAULT_CURRENCY ) {
        $result .= '<strong>';
        $close = '</strong>';
      }

      $result .= $c . ':';

      if ( isset($close) ) {
        $result .= $close;
      }

      $result .= '&nbsp;' . ($data[$c] ?? '') . '<br>';
    }

    if ( !empty($result) ) {
      $result = substr($result, 0, -strlen('<br>'));
    }

    return $result;
  }

  function tep_braintree_autoloader($class) {
    if ( substr($class, 0, 10) == 'Braintree_' ) {
      $file = dirname(__FILE__, 3) . '/apps/braintree_cc/' . str_replace('_', '/', $class) . '.php';

      if ( file_exists($file) ) {
        include $file;
      }
    }
  }
