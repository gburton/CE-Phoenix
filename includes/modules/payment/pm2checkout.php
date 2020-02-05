<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class pm2checkout extends abstract_payment_module {

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

    const CONFIG_KEY_BASE = 'MODULE_PAYMENT_2CHECKOUT_';

    private $signature = '2checkout|pm2checkout|1.2|2.2';

    public $form_action_url = 'https://www.2checkout.com/2co/buyer/purchase';

    public function pre_confirmation_check() {
      if (MODULE_PAYMENT_2CHECKOUT_ROUTINE == 'Single-Page') {
        $this->form_action_url = 'https://www.2checkout.com/checkout/spurchase';
      }
    }

    public function process_button() {
      global $customer_id, $order, $languages_id, $cartID;

      $process_button_string = tep_draw_hidden_field('sid', MODULE_PAYMENT_2CHECKOUT_LOGIN)
                             . tep_draw_hidden_field('total', $this->format_raw($order->info['total'], MODULE_PAYMENT_2CHECKOUT_CURRENCY))
                             . tep_draw_hidden_field('cart_order_id', date('YmdHis') . '-' . $customer_id . '-' . $cartID)
                             . tep_draw_hidden_field('fixed', 'Y')
                             . tep_draw_hidden_field('first_name', $order->billing['firstname'])
                             . tep_draw_hidden_field('last_name', $order->billing['lastname'])
                             . tep_draw_hidden_field('street_address', $order->billing['street_address'])
                             . tep_draw_hidden_field('city', $order->billing['city'])
                             . tep_draw_hidden_field('state', $order->billing['state'])
                             . tep_draw_hidden_field('zip', $order->billing['postcode'])
                             . tep_draw_hidden_field('country', $order->billing['country']['title'])
                             . tep_draw_hidden_field('email', $order->customer['email_address'])
                             . tep_draw_hidden_field('phone', $order->customer['telephone'])
                             . tep_draw_hidden_field('ship_name', $order->delivery['name'])
                             . tep_draw_hidden_field('ship_street_address', $order->delivery['street_address'])
                             . tep_draw_hidden_field('ship_city', $order->delivery['city'])
                             . tep_draw_hidden_field('ship_state', $order->delivery['state'])
                             . tep_draw_hidden_field('ship_zip', $order->delivery['postcode'])
                             . tep_draw_hidden_field('ship_country', $order->delivery['country']['title']);

      foreach ($order->products as $product) {
        $process_button_string .= tep_draw_hidden_field('c_prod_' . ($i+1), (int)$product['id'] . ',' . (int)$product['qty'])
                                . tep_draw_hidden_field('c_name_' . ($i+1), $product['name'])
                                . tep_draw_hidden_field('c_description_' . ($i+1), $product['name'])
                                . tep_draw_hidden_field('c_price_' . ($i+1), $this->format_raw(tep_add_tax($product['final_price'], $product['tax']), MODULE_PAYMENT_2CHECKOUT_CURRENCY));
      }

      $process_button_string .= tep_draw_hidden_field('id_type', '1')
                              . tep_draw_hidden_field('skip_landing', '1');

      if (MODULE_PAYMENT_2CHECKOUT_TESTMODE == 'Test') {
        $process_button_string .= tep_draw_hidden_field('demo', 'Y');
      }

      $process_button_string .= tep_draw_hidden_field('return_url', tep_href_link('shopping_cart.php'));

      $lang_query = tep_db_query("SELECT code FROM languages WHERE languages_id = '" . (int)$languages_id . "'");
      $lang = tep_db_fetch_array($lang_query);

      switch (strtolower($lang['code'])) {
        case 'es':
          $process_button_string .= tep_draw_hidden_field('lang', 'sp');
          break;
      }

      $process_button_string .= tep_draw_hidden_field('cart_brand_name', PROJECT_VERSION)
                              . tep_draw_hidden_field('cart_version_name', tep_get_version());

      return $process_button_string;
    }

    public function before_process() {
      if ( ($_POST['credit_card_processed'] != 'Y') && ($_POST['credit_card_processed'] != 'K') ){
        tep_redirect(tep_href_link('checkout_payment.php', 'payment_error=' . $this->code, 'SSL', true, false));
      }
    }

    public function after_process() {
      global $order, $order_id;

      if (MODULE_PAYMENT_2CHECKOUT_TESTMODE == 'Test') {
        $sql_data = [
          'orders_id' => (int)$order_id,
          'orders_status_id' => (int)$order->info['order_status'],
          'date_added' => 'NOW()',
          'customer_notified' => '0',
          'comments' => MODULE_PAYMENT_2CHECKOUT_TEXT_WARNING_DEMO_MODE,
        ];

        tep_db_perform('orders_status_history', $sql_data);
      } elseif (tep_not_null(MODULE_PAYMENT_2CHECKOUT_SECRET_WORD) && (MODULE_PAYMENT_2CHECKOUT_TESTMODE == 'Production')) {
// The KEY value returned from the gateway is intentionally broken for Test transactions so it is only checked in Production mode
        if (strtoupper(md5(MODULE_PAYMENT_2CHECKOUT_SECRET_WORD . MODULE_PAYMENT_2CHECKOUT_LOGIN . $_POST['order_number'] . $this->order_format($order->info['total'], MODULE_PAYMENT_2CHECKOUT_CURRENCY))) != strtoupper($_POST['key'])) {
          $sql_data = [
            'orders_id' => (int)$order_id,
            'orders_status_id' => (int)$order->info['order_status'],
            'date_added' => 'NOW()',
            'customer_notified' => '0',
            'comments' => MODULE_PAYMENT_2CHECKOUT_TEXT_WARNING_TRANSACTION_ORDER,
          ];

          tep_db_perform('orders_status_history', $sql_data);
        }
      }
    }

    public function get_error() {
      return [
        'title' => '',
        'error' => MODULE_PAYMENT_2CHECKOUT_TEXT_ERROR_MESSAGE,
      ];
    }

    protected function get_parameters() {
      return [
        'MODULE_PAYMENT_2CHECKOUT_STATUS' => [
          'title' => 'Enable 2Checkout',
          'value' => 'False',
          'desc' => 'Do you want to accept 2CheckOut payments?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_PAYMENT_2CHECKOUT_LOGIN' => [
          'title' => 'Vendor Account',
          'value' => '',
          'desc' => 'The vendor account number for the 2Checkout gateway.',
        ],
        'MODULE_PAYMENT_2CHECKOUT_TESTMODE' => [
          'title' => 'Transaction Mode',
          'value' => 'Test',
          'desc' => 'Transaction mode used for the 2Checkout gateway.',
          'set_func' => "tep_cfg_select_option(['Test', 'Production'], ",
        ],
        'MODULE_PAYMENT_2CHECKOUT_SECRET_WORD' => [
          'title' => 'Secret Word',
          'value' => '',
          'desc' => 'The secret word to confirm transactions with. (Must be the same as defined on the Vendor Admin interface)',
        ],
        'MODULE_PAYMENT_2CHECKOUT_ROUTINE' => [
          'title' => 'Payment Routine',
          'value' => 'Multi-Page',
          'desc' => 'The payment routine to use on the 2Checkout gateway.',
          'set_func' => "tep_cfg_select_option(['Multi-Page', 'Single-Page'], ",
        ],
        'MODULE_PAYMENT_2CHECKOUT_CURRENCY' => [
          'title' => 'Processing Currency',
          'value' => '" . DEFAULT_CURRENCY . "',
          'desc' => 'The currency to process transactions in. (Must be the same as defined on the Vendor Admin interface)',
          'set_func' => 'pm2checkout::getCurrencies(',
        ],
        'MODULE_PAYMENT_2CHECKOUT_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. (Lowest is displayed first)',
        ],
        'MODULE_PAYMENT_2CHECKOUT_ZONE' => [
          'title' => 'Payment Zone',
          'value' => '0',
          'desc' => 'If a zone is selected, only enable this payment method for that zone.',
          'use_func' => 'tep_get_zone_class_title',
          'set_func' => 'tep_cfg_pull_down_zone_classes(',
        ],
        'MODULE_PAYMENT_2CHECKOUT_ORDER_STATUS_ID' => [
          'title' => 'Set Order Status',
          'value' => '0',
          'desc' => 'Set the status of orders made with this payment module to this value.',
          'set_func' => 'tep_cfg_pull_down_order_statuses(',
          'use_func' => 'tep_get_order_status_name',
        ],
      ];
    }

// format prices without currency formatting
    function format_raw($number, $currency_code = '', $currency_value = '') {
      global $currencies, $currency;

      if (empty($currency_code) || !$currencies->is_set($currency_code)) {
        $currency_code = $currency;
      }

      if (empty($currency_value) || !is_numeric($currency_value)) {
        $currency_value = $currencies->currencies[$currency_code]['value'];
      }

      return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '.', '');
    }

    function getCurrencies($value, $key = '') {
      $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

      $currencies_array = [];

      $currencies_query = tep_db_query("SELECT code, title FROM currencies ORDER BY title");
      while ($currencies = tep_db_fetch_array($currencies_query)) {
        $currencies_array[] = [
          'id' => $currencies['code'],
          'text' => $currencies['title'],
        ];
      }

      return tep_draw_pull_down_menu($name, $currencies_array, $value);
    }

  }
