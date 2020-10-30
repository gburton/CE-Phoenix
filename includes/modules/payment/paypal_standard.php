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

  class paypal_standard {

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

    public $code = 'paypal_standard';
    public $title, $description, $enabled, $_app;

    function __construct() {
      global $order;

      $this->_app = new OSCOM_PayPal();
      $this->_app->loadLanguageFile('modules/PS/PS.php');

      $this->signature = 'paypal|paypal_standard|' . $this->_app->getVersion() . '|2.3';
      $this->api_version = $this->_app->getApiVersion();

      $this->title = $this->_app->getDef('module_ps_title');
      $this->public_title = $this->_app->getDef('module_ps_public_title');
      $this->description = '<div align="center">' . $this->_app->drawButton($this->_app->getDef('module_ps_legacy_admin_app_button'), tep_href_link('paypal.php', 'action=configure&module=PS'), 'primary', null, true) . '</div>';
      $this->sort_order = defined('OSCOM_APP_PAYPAL_PS_SORT_ORDER') ? OSCOM_APP_PAYPAL_PS_SORT_ORDER : 0;
      $this->enabled = defined('OSCOM_APP_PAYPAL_PS_STATUS') && in_array(OSCOM_APP_PAYPAL_PS_STATUS, ['1', '0']);
      $this->order_status = defined('OSCOM_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID') && ((int)OSCOM_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID > 0) ? (int)OSCOM_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID : 0;

      if ( defined('OSCOM_APP_PAYPAL_PS_STATUS') ) {
        if ( OSCOM_APP_PAYPAL_PS_STATUS == '0' ) {
          $this->title .= ' [Sandbox]';
          $this->public_title .= ' (' . $this->code . '; Sandbox)';
        }

        if ( OSCOM_APP_PAYPAL_PS_STATUS == '1' ) {
          $this->form_action_url = 'https://www.paypal.com/cgi-bin/webscr';
        } else {
          $this->form_action_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        }
      }

      if ( !function_exists('curl_init') ) {
        $this->description .= '<div class="alert alert-warning">' . $this->_app->getDef('module_ps_error_curl') . '</div>';

        $this->enabled = false;
      }

      if ( $this->enabled === true ) {
        if ( !$this->_app->hasCredentials('PS', 'email') ) {
          $this->description .= '<div class="alert alert-warning">' . $this->_app->getDef('module_ps_error_credentials') . '</div>';

          $this->enabled = false;
        }
      }

      if ( $this->enabled === true ) {
        if ( !defined('OSCOM_APP_PAYPAL_PS_PDT_IDENTITY_TOKEN') || (!tep_not_null(OSCOM_APP_PAYPAL_PS_PDT_IDENTITY_TOKEN) && !$this->_app->hasCredentials('PS')) ) {
          $this->description .= '<div class="alert alert-warning">' . $this->_app->getDef('module_ps_error_credentials_pdt_api') . '</div>';

          $this->enabled = false;
        }
      }

      if ( $this->enabled === true ) {
        if ( isset($order) && is_object($order) ) {
          $this->update_status();
        }
      }

// Before the stock quantity check is performed in checkout_process.php, detect if the quantity
// has already been deducted in the IPN to avoid a quantity == 0 redirect
      if ( $this->enabled === true ) {
        if ('checkout_process.php' === basename($GLOBALS['PHP_SELF'])) {
          if ( isset($_SESSION['payment']) && ($_SESSION['payment'] == $this->code) ) {
            $this->pre_before_check();
          }
        }
      }
    }

    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)OSCOM_APP_PAYPAL_PS_ZONE > 0) ) {
        $check_query = tep_db_query("SELECT zone_id FROM zones_to_geo_zones WHERE geo_zone_id = " . (int)OSCOM_APP_PAYPAL_PS_ZONE . " AND zone_country_id = " . (int)$order->billing['country']['id'] . " ORDER BY zone_id");
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
      return substr($_SESSION['cart_PayPal_Standard_ID'], strpos($_SESSION['cart_PayPal_Standard_ID'], '-') + 1);
    }

    function selection() {
      if (isset($_SESSION['cart_PayPal_Standard_ID'])) {
        $order_id = $this->extract_order_id();

        $check_query = tep_db_query('SELECT orders_id FROM orders_status_history WHERE orders_id = ' . (int)$order_id . ' LIMIT 1');

        if (tep_db_num_rows($check_query) < 1) {
          tep_delete_order($order_id);

          unset($_SESSION['cart_PayPal_Standard_ID']);
        }
      }

      return [
        'id' => $this->code,
        'module' => $this->public_title
      ];
    }

    function pre_confirmation_check() {
      global $order;

      if (empty($_SESSION['cart']->cartID)) {
        $_SESSION['cartID'] = $_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();
      }

      $order->info['payment_method_raw'] = $order->info['payment_method'];
      $order->info['payment_method'] = '<img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" border="0" alt="PayPal Logo" style="padding: 3px;" />';
    }

    function confirmation() {
      if (isset($_SESSION['cartID'])) {
        $insert_order = false;

        if (isset($_SESSION['cart_PayPal_Standard_ID'])) {
          $order_id = $this->extract_order_id();

          $curr_check = tep_db_query("SELECT currency FROM orders WHERE orders_id = " . (int)$order_id);
          $curr = tep_db_fetch_array($curr_check);

          if ( ($curr['currency'] != $GLOBALS['order']->info['currency']) || ($_SESSION['cartID'] != substr($_SESSION['cart_PayPal_Standard_ID'], 0, strlen($_SESSION['cartID']))) ) {
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

          $_SESSION['cart_PayPal_Standard_ID'] = $_SESSION['cartID'] . '-' . $GLOBALS['order']->get_id();
        }
      }

      return false;
    }

    function process_button() {
      global $order, $customer_data;

      $total_tax = $order->info['tax'];

// remove shipping tax in total tax value
      if ( isset($_SESSION['shipping']['cost']) ) {
        $total_tax -= ($order->info['shipping_cost'] - $_SESSION['shipping']['cost']);
      }

      $ipn_language = null;

      $lng = new language();

      if ( count($lng->catalog_languages) > 1 ) {
        foreach ( $lng->catalog_languages as $key => $value ) {
          if ( $value['directory'] == $_SESSION['language'] ) {
            $ipn_language = $key;
            break;
          }
        }
      }

      $process_button_string = '';
      $parameters = [
        'cmd' => '_cart',
        'upload' => '1',
        'item_name_1' => STORE_NAME,
        'shipping_1' => $this->_app->formatCurrencyRaw($order->info['shipping_cost']),
        'business' => $this->_app->getCredentials('PS', 'email'),
        'amount_1' => $this->_app->formatCurrencyRaw($order->info['total'] - $order->info['shipping_cost'] - $total_tax),
        'currency_code' => $_SESSION['currency'],
        'invoice' => $this->extract_order_id(),
        'custom' => $_SESSION['customer_id'],
        'notify_url' => tep_href_link('ext/modules/payment/paypal/standard_ipn.php', (isset($ipn_language) ? 'language=' . $ipn_language : ''), 'SSL', false, false),
        'rm' => '2',
        'return' => tep_href_link('checkout_process.php', '', 'SSL'),
        'cancel_return' => tep_href_link('checkout_payment.php', '', 'SSL'),
        'bn' => $this->_app->getIdentifier(),
        'paymentaction' => (OSCOM_APP_PAYPAL_PS_TRANSACTION_METHOD == '1') ? 'sale' : 'authorization',
      ];

      $return_link_title = $this->_app->getDef('module_ps_button_return_to_store', ['storename' => STORE_NAME]);

      if ( strlen($return_link_title) <= 60 ) {
        $parameters['cbt'] = $return_link_title;
      }

      if (is_numeric($_SESSION['sendto']) && ($_SESSION['sendto'] > 0)) {
        $parameters['address_override'] = '1';
        $customer_data->get('country', $order->delivery);
        $parameters['first_name'] = $customer_data->get('firstname', $order->delivery);
        $parameters['last_name'] = $customer_data->get('lastname', $order->delivery);
        $parameters['address1'] = $customer_data->get('street_address', $order->delivery);
        $parameters['address2'] = $customer_data->get('suburb', $order->delivery);
        $parameters['city'] = $customer_data->get('city', $order->delivery);
        $parameters['state'] = tep_get_zone_code(
          $customer_data->get('country_id', $order->delivery),
          $customer_data->get('zone_id', $order->delivery),
          $customer_data->get('state', $order->delivery));
        $parameters['zip'] = $customer_data->get('postcode', $order->delivery);
        $parameters['country'] = $customer_data->get('country_iso_code_2', $order->delivery);
      } else {
        $parameters['no_shipping'] = '1';
        $customer_data->get('country', $order->billing);
        $parameters['first_name'] = $customer_data->get('firstname', $order->billing);
        $parameters['last_name'] = $customer_data->get('lastname', $order->billing);
        $parameters['address1'] = $customer_data->get('street_address', $order->billing);
        $parameters['address2'] = $customer_data->get('suburb', $order->billing);
        $parameters['city'] = $customer_data->get('city', $order->billing);
        $parameters['state'] = tep_get_zone_code(
          $customer_data->get('country_id', $order->billing),
          $customer_data->get('zone_id', $order->billing),
          $customer_data->get('state', $order->billing));
        $parameters['zip'] = $customer_data->get('postcode', $order->billing);
        $parameters['country'] = $customer_data->get('country_iso_code_2', $order->billing);
      }

      $item_params = [];

      $line_item_no = 1;

      foreach ($order->products as $product) {
        if ( DISPLAY_PRICE_WITH_TAX == 'true' ) {
          $product_price = $this->_app->formatCurrencyRaw($product['final_price'] + tep_calculate_tax($product['final_price'], $product['tax']));
        } else {
          $product_price = $this->_app->formatCurrencyRaw($product['final_price']);
        }

        $item_params['item_name_' . $line_item_no] = $product['name'];
        $item_params['amount_' . $line_item_no] = $product_price;
        $item_params['quantity_' . $line_item_no] = $product['qty'];

        $line_item_no++;
      }

      $items_total = $this->_app->formatCurrencyRaw($order->info['subtotal']);

      $has_negative_price = false;

// order totals are processed on checkout confirmation but not captured into a variable
      foreach (($GLOBALS['order_total_modules']->modules ?? []) as $value) {
        $class = pathinfo($value, PATHINFO_FILENAME);

        if ($GLOBALS[$class]->enabled) {
          foreach ($GLOBALS[$class]->output as $order_total) {
            if (tep_not_null($order_total['title']) && tep_not_null($order_total['text'])) {
              if ( !in_array($GLOBALS[$class]->code, ['ot_subtotal', 'ot_shipping', 'ot_tax', 'ot_total']) ) {
                $item_params['item_name_' . $line_item_no] = $order_total['title'];
                $item_params['amount_' . $line_item_no] = $this->_app->formatCurrencyRaw($order_total['value']);

                $items_total += $item_params['amount_' . $line_item_no];

                if ( $item_params['amount_' . $line_item_no] < 0 ) {
                  $has_negative_price = true;
                }

                $line_item_no++;
              }
            }
          }
        }
      }

      $paypal_item_total = $items_total + $parameters['shipping_1'];

      if ( DISPLAY_PRICE_WITH_TAX == 'false' ) {
        $item_params['tax_cart'] = $this->_app->formatCurrencyRaw($total_tax);

        $paypal_item_total += $item_params['tax_cart'];
      }

      if ( ($has_negative_price == false) && ($this->_app->formatCurrencyRaw($paypal_item_total) == $this->_app->formatCurrencyRaw($order->info['total'])) ) {
        $parameters = array_merge($parameters, $item_params);
      } else {
        $parameters['tax_cart'] = $this->_app->formatCurrencyRaw($total_tax);
      }

      if ( OSCOM_APP_PAYPAL_PS_EWP_STATUS == '1' ) {
        $parameters['cert_id'] = OSCOM_APP_PAYPAL_PS_EWP_PUBLIC_CERT_ID;

        $random_string = rand(100000, 999999) . '-' . $_SESSION['customer_id'] . '-';

        $data = '';
        foreach ($parameters as $key => $value) {
          $data .= $key . '=' . $value . "\n";
        }

        $fp = fopen(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt', 'w');
        fwrite($fp, $data);
        fclose($fp);

        unset($data);

        if (function_exists('openssl_pkcs7_sign') && function_exists('openssl_pkcs7_encrypt')) {
          openssl_pkcs7_sign(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt', OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt', file_get_contents(OSCOM_APP_PAYPAL_PS_EWP_PUBLIC_CERT), file_get_contents(OSCOM_APP_PAYPAL_PS_EWP_PRIVATE_KEY), ['From' => $this->_app->getCredentials('PS', 'email')], PKCS7_BINARY);

          unlink(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt');

// remove headers from the signature
          $signed = file_get_contents(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');
          $signed = explode("\n\n", $signed);
          $signed = base64_decode($signed[1]);

          $fp = fopen(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt', 'w');
          fwrite($fp, $signed);
          fclose($fp);

          unset($signed);

          openssl_pkcs7_encrypt(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt', OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt', file_get_contents(OSCOM_APP_PAYPAL_PS_EWP_PAYPAL_CERT), ['From' => $this->_app->getCredentials('PS', 'email')], PKCS7_BINARY);

          unlink(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');

// remove headers from the encrypted result
          $data = file_get_contents(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
          $data = explode("\n\n", $data);
          $data = '-----BEGIN PKCS7-----' . "\n" . $data[1] . "\n" . '-----END PKCS7-----';

          unlink(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
        } else {
          exec(OSCOM_APP_PAYPAL_PS_EWP_OPENSSL . ' smime -sign -in ' . OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt -signer ' . OSCOM_APP_PAYPAL_PS_EWP_PUBLIC_CERT . ' -inkey ' . OSCOM_APP_PAYPAL_PS_EWP_PRIVATE_KEY . ' -outform der -nodetach -binary > ' . OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');
          unlink(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'data.txt');

          exec(OSCOM_APP_PAYPAL_PS_EWP_OPENSSL . ' smime -encrypt -des3 -binary -outform pem ' . OSCOM_APP_PAYPAL_PS_EWP_PAYPAL_CERT . ' < ' . OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt > ' . OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
          unlink(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'signed.txt');

          $fh = fopen(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt', 'rb');
          $data = fread($fh, filesize(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt'));
          fclose($fh);

          unlink(OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY . '/' . $random_string . 'encrypted.txt');
        }

        $process_button_string = tep_draw_hidden_field('cmd', '_s-xclick')
                               . tep_draw_hidden_field('encrypted', $data);

        unset($data);
      } else {
        foreach ($parameters as $key => $value) {
          $process_button_string .= tep_draw_hidden_field($key, $value);
        }
      }

      return $process_button_string;
    }

    function pre_before_check() {
      global $order_id;

      $result = false;

      $pptx_params = [];

      $seller_accounts = [$this->_app->getCredentials('PS', 'email')];

      if ( tep_not_null($this->_app->getCredentials('PS', 'email_primary')) ) {
        $seller_accounts[] = $this->_app->getCredentials('PS', 'email_primary');
      }

      if ( (isset($_POST['receiver_email']) && in_array($_POST['receiver_email'], $seller_accounts)) || (isset($_POST['business']) && in_array($_POST['business'], $seller_accounts)) ) {
        $parameters = 'cmd=_notify-validate&';

        foreach ( $_POST as $key => $value ) {
          if ( $key != 'cmd' ) {
            $parameters .= $key . '=' . urlencode(stripslashes($value)) . '&';
          }
        }

        $parameters = substr($parameters, 0, -1);

        $result = $this->_app->makeApiCall($this->form_action_url, $parameters);

        foreach ( $_POST as $key => $value ) {
          $pptx_params[$key] = stripslashes($value);
        }

        foreach ( $_GET as $key => $value ) {
          $pptx_params['GET ' . $key] = stripslashes($value);
        }

        $this->_app->log('PS', '_notify-validate', ($result == 'VERIFIED') ? 1 : -1, $pptx_params, $result, (OSCOM_APP_PAYPAL_PS_STATUS == '1') ? 'live' : 'sandbox');
      } elseif ( isset($_GET['tx']) ) { // PDT
        if ( tep_not_null(OSCOM_APP_PAYPAL_PS_PDT_IDENTITY_TOKEN) ) {
          $pptx_params['cmd'] = '_notify-synch';

          $parameters = 'cmd=_notify-synch&tx=' . urlencode(stripslashes($_GET['tx'])) . '&at=' . urlencode(OSCOM_APP_PAYPAL_PS_PDT_IDENTITY_TOKEN);

          $pdt_raw = $this->_app->makeApiCall($this->form_action_url, $parameters);

          if ( !empty($pdt_raw) ) {
            $pdt = explode("\n", trim($pdt_raw));

            if ( isset($pdt[0]) ) {
              if ( 'SUCCESS' === $pdt[0] ) {
                $result = 'VERIFIED';

                unset($pdt[0]);
              } else {
                $result = $pdt_raw;
              }
            }

            if ( !empty($pdt) && is_array($pdt) ) {
              foreach ( $pdt as $line ) {
                $p = explode('=', $line, 2);

                if ( count($p) === 2 ) {
                  $pptx_params[trim($p[0])] = trim(urldecode($p[1]));
                }
              }
            }
          }

          foreach ( $_GET as $key => $value ) {
            $pptx_params['GET ' . $key] = stripslashes($value);
          }

          $this->_app->log('PS', $pptx_params['cmd'], ($result == 'VERIFIED') ? 1 : -1, $pptx_params, $result, (OSCOM_APP_PAYPAL_PS_STATUS == '1') ? 'live' : 'sandbox');
        } else {
          $details = $this->_app->getApiResult('APP', 'GetTransactionDetails', ['TRANSACTIONID' => stripslashes($_GET['tx'])], (OSCOM_APP_PAYPAL_PS_STATUS == '1') ? 'live' : 'sandbox');

          if ( in_array($details['ACK'], ['Success', 'SuccessWithWarning']) ) {
            $result = 'VERIFIED';

            $pptx_params = [
              'txn_id' => $details['TRANSACTIONID'],
              'invoice' => $details['INVNUM'],
              'custom' => $details['CUSTOM'],
              'payment_status' => $details['PAYMENTSTATUS'],
              'payer_status' => $details['PAYERSTATUS'],
              'mc_gross' => $details['AMT'],
              'mc_currency' => $details['CURRENCYCODE'],
              'pending_reason' => $details['PENDINGREASON'],
              'reason_code' => $details['REASONCODE'],
              'address_status' => $details['ADDRESSSTATUS'],
              'payment_type' => $details['PAYMENTTYPE'],
            ];
          }
        }
      } else {
        foreach ( $_POST as $key => $value ) {
          $pptx_params[$key] = stripslashes($value);
        }

        foreach ( $_GET as $key => $value ) {
          $pptx_params['GET ' . $key] = stripslashes($value);
        }

        $this->_app->log('PS', 'UNKNOWN', ($result == 'VERIFIED') ? 1 : -1, $pptx_params, $result, (OSCOM_APP_PAYPAL_PS_STATUS == '1') ? 'live' : 'sandbox');
      }

      if ( $result != 'VERIFIED' ) {
        $GLOBALS['messageStack']->add_session('header', $this->_app->getDef('module_ps_error_invalid_transaction'));

        tep_redirect(tep_href_link('shopping_cart.php'));
      }

      $this->verifyTransaction($pptx_params);

      $order_id = $this->extract_order_id();

      $check_query = tep_db_query("SELECT orders_status FROM orders WHERE orders_id = " . (int)$order_id . " AND customers_id = " . (int)$_SESSION['customer_id']);

      if (!tep_db_num_rows($check_query) || ($order_id != $pptx_params['invoice']) || ($_SESSION['customer_id'] != $pptx_params['custom'])) {
        tep_redirect(tep_href_link('shopping_cart.php'));
      }

      $check = tep_db_fetch_array($check_query);

// skip before_process() if order was already processed in IPN
      if ( $check['orders_status'] != OSCOM_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID ) {
        if ( !empty($_SESSION['comments']) ) {
          $sql_data = [
            'orders_id' => $order_id,
            'orders_status_id' => (int)$check['orders_status'],
            'date_added' => 'NOW()',
            'customer_notified' => '0',
            'comments' => $_SESSION['comments'],
          ];

          tep_db_perform('orders_status_history', $sql_data);
        }

// load the after_process function from the payment modules
        $this->after_process();
      }
    }

    function before_process() {
      global $order;

      $order->set_id($this->extract_order_id());

      $order->info['order_status'] = DEFAULT_ORDERS_STATUS_ID;
      if ( OSCOM_APP_PAYPAL_PS_ORDER_STATUS_ID > 0) {
        $order->info['order_status'] = OSCOM_APP_PAYPAL_PS_ORDER_STATUS_ID;
      }

      tep_db_query("UPDATE orders SET orders_status = " . (int)$order->info['order_status'] . ", last_modified = NOW() WHERE orders_id = " . (int)$order->get_id());

      $GLOBALS['hooks']->register_pipeline('after');
      require 'includes/system/segments/checkout/insert_history.php';

// load the after_process function from the payment modules
      $this->after_process();
    }

    function after_process() {
      unset($_SESSION['cart_PayPal_Standard_ID']);

      $GLOBALS['hooks']->register_pipeline('reset');

      tep_redirect(tep_href_link('checkout_success.php', '', 'SSL'));
    }

    function get_error() {
      return false;
    }

    function check() {
      $check_query = tep_db_query("SELECT configuration_value FROM configuration WHERE configuration_key = 'OSCOM_APP_PAYPAL_PS_STATUS'");
      if ( $check = tep_db_fetch_array($check_query) ) {
        return tep_not_null($check['configuration_value']);
      }

      return false;
    }

    function install() {
      tep_redirect(tep_href_link('paypal.php', 'action=configure&subaction=install&module=PS'));
    }

    function remove() {
      tep_redirect(tep_href_link('paypal.php', 'action=configure&subaction=uninstall&module=PS'));
    }

    function keys() {
      return ['OSCOM_APP_PAYPAL_PS_SORT_ORDER'];
    }

    function verifyTransaction($pptx_params, $is_ipn = false) {
      if ( isset($pptx_params['invoice']) && is_numeric($pptx_params['invoice']) && ($pptx_params['invoice'] > 0) && isset($pptx_params['custom']) && is_numeric($pptx_params['custom']) && ($pptx_params['custom'] > 0) ) {
        $order_query = tep_db_query("SELECT orders_id, currency, currency_value FROM orders WHERE orders_id = " . (int)$pptx_params['invoice'] . " AND customers_id = " . (int)$pptx_params['custom']);

        if ( tep_db_num_rows($order_query) === 1 ) {
          $order = tep_db_fetch_array($order_query);

          $total_query = tep_db_query("SELECT value FROM orders_total WHERE orders_id = " . (int)$order['orders_id'] . " AND class = 'ot_total' limit 1");
          $total = tep_db_fetch_array($total_query);

          $comment_status = 'Transaction ID: ' . tep_output_string_protected($pptx_params['txn_id']) . "\n"
                          . 'Payer Status: ' . tep_output_string_protected($pptx_params['payer_status']) . "\n"
                          . 'Address Status: ' . tep_output_string_protected($pptx_params['address_status']) . "\n"
                          . 'Payment Status: ' . tep_output_string_protected($pptx_params['payment_status']) . "\n"
                          . 'Payment Type: ' . tep_output_string_protected($pptx_params['payment_type']) . "\n"
                          . 'Pending Reason: ' . tep_output_string_protected($pptx_params['pending_reason']);

          if ( $pptx_params['mc_gross'] != $this->_app->formatCurrencyRaw($total['value'], $order['currency'], $order['currency_value']) ) {
            $comment_status .= "\n" . 'OSCOM Error Total Mismatch: PayPal transaction value (' . tep_output_string_protected($pptx_params['mc_gross']) . ') does not match order value (' . $this->_app->formatCurrencyRaw($total['value'], $order['currency'], $order['currency_value']) . ')';
          }

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
