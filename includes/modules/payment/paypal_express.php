<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  if ( !class_exists('OSCOM_PayPal') ) {
    include(DIR_FS_CATALOG . 'includes/apps/paypal/OSCOM_PayPal.php');
  }

  class paypal_express {

    public $code = 'paypal_express';
    public $title, $description, $enabled, $_app;

    function __construct() {
      global $PHP_SELF, $oscTemplate, $order, $request_type;

      $this->_app = new OSCOM_PayPal();
      $this->_app->loadLanguageFile('modules/EC/EC.php');

      $this->signature = 'paypal|paypal_express|' . $this->_app->getVersion() . '|2.3';
      $this->api_version = $this->_app->getApiVersion();

      $this->title = $this->_app->getDef('module_ec_title');
      $this->public_title = $this->_app->getDef('module_ec_public_title');
      $this->description = '<div align="center">' . $this->_app->drawButton($this->_app->getDef('module_ec_legacy_admin_app_button'), tep_href_link('paypal.php', 'action=configure&module=EC'), 'primary', null, true) . '</div>';
      $this->sort_order = defined('OSCOM_APP_PAYPAL_EC_SORT_ORDER') ? OSCOM_APP_PAYPAL_EC_SORT_ORDER : 0;
      $this->enabled = defined('OSCOM_APP_PAYPAL_EC_STATUS') && in_array(OSCOM_APP_PAYPAL_EC_STATUS, ['1', '0']);
      $this->order_status = defined('OSCOM_APP_PAYPAL_EC_ORDER_STATUS_ID') && ((int)OSCOM_APP_PAYPAL_EC_ORDER_STATUS_ID > 0) ? (int)OSCOM_APP_PAYPAL_EC_ORDER_STATUS_ID : 0;

      if ( defined('OSCOM_APP_PAYPAL_EC_STATUS') ) {
        if ( OSCOM_APP_PAYPAL_EC_STATUS == '0' ) {
          $this->title .= ' [Sandbox]';
          $this->public_title .= ' (' . $this->code . '; Sandbox)';
        }
      }

      if ( !function_exists('curl_init') ) {
        $this->description .= '<div class="alert alert-warning">' . $this->_app->getDef('module_ec_error_curl') . '</div>';

        $this->enabled = false;
      }

      if ( $this->enabled === true ) {
        if ( OSCOM_APP_PAYPAL_GATEWAY == '1' ) { // PayPal
          if ( !$this->_app->hasCredentials('EC') ) {
            $this->description .= '<div class="alert alert-warning">' . $this->_app->getDef('module_ec_error_credentials') . '</div>';

            $this->enabled = false;
          }
        } else { // Payflow
          if ( !$this->_app->hasCredentials('EC', 'payflow') ) {
            $this->description .= '<div class="alert alert-warning">' . $this->_app->getDef('module_ec_error_credentials_payflow') . '</div>';

            $this->enabled = false;
          }
        }
      }

      if ( $this->enabled === true ) {
        if ( isset($order) && is_object($order) ) {
          $this->update_status();
        }
      }

      if ( (basename($PHP_SELF) == 'shopping_cart.php') ) {
        if ( (OSCOM_APP_PAYPAL_GATEWAY == '1') && (OSCOM_APP_PAYPAL_EC_CHECKOUT_FLOW == '1') ) {
          header('X-UA-Compatible: IE=edge', true);
        }
      }

// When changing the shipping address due to no shipping rates being available, head straight to the checkout confirmation page
      if ((basename($PHP_SELF) == 'checkout_payment.php') && isset($_SESSION['appPayPalEcRightTurn']) ) {
        unset($_SESSION['appPayPalEcRightTurn']);

        if ( isset($_SESSION['payment']) && ($_SESSION['payment'] == $this->code) ) {
          tep_redirect(tep_href_link('checkout_confirmation.php'));
        }
      }

      if ( $this->enabled === true ) {
        if (basename($PHP_SELF) == 'shopping_cart.php') {
          if ( $this->templateClassExists() ) {
            if ( (OSCOM_APP_PAYPAL_GATEWAY == '1') && (OSCOM_APP_PAYPAL_EC_CHECKOUT_FLOW == '1') ) {
              $oscTemplate->addBlock('<style>#ppECButton { display: inline-block; }</style>', 'header_tags');
            }

            if ( file_exists(DIR_FS_CATALOG . 'ext/modules/payment/paypal/express.css') ) {
              $oscTemplate->addBlock('<link rel="stylesheet" type="text/css" href="ext/modules/payment/paypal/express.css" />', 'header_tags');
            }
          }
        }
      }
    }

    function update_status() {
      global $order;

      if ( $this->enabled && ((int)OSCOM_APP_PAYPAL_EC_ZONE > 0) ) {
        $check_query = tep_db_query("SELECT zone_id FROM zones_to_geo_zones WHERE geo_zone_id = " . (int)OSCOM_APP_PAYPAL_EC_ZONE . " and zone_country_id = " . (int)$GLOBALS['customer_data']->get('country_id', $order->delivery) . " ORDER BY zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if (($check['zone_id'] < 1) || ($check['zone_id'] == $GLOBALS['customer_data']->get('zone_id', $order->delivery))) {
            return;
          }
        }

        $this->enabled = false;
      }
    }

    function checkout_initialization_method() {
      $string = '';

      if (OSCOM_APP_PAYPAL_GATEWAY == '1') {
        if (OSCOM_APP_PAYPAL_EC_CHECKOUT_FLOW == '0') {
          if (OSCOM_APP_PAYPAL_EC_CHECKOUT_IMAGE == '1') {
            if (OSCOM_APP_PAYPAL_EC_STATUS == '1') {
              $image_button = 'https://fpdbs.paypal.com/dynamicimageweb?cmd=_dynamic-image';
            } else {
              $image_button = 'https://fpdbs.sandbox.paypal.com/dynamicimageweb?cmd=_dynamic-image';
            }

            $params = ['locale=' . $this->_app->getDef('module_ec_button_locale')];

            if ( $this->_app->hasCredentials('EC') ) {
              $response_array = $this->_app->getApiResult('EC', 'GetPalDetails');

              if ( isset($response_array['PAL']) ) {
                $params[] = 'pal=' . $response_array['PAL'];
                $params[] = 'ordertotal=' . $this->_app->formatCurrencyRaw($_SESSION['cart']->show_total());
              }
            }

            if ( !empty($params) ) {
              $image_button .= '&' . implode('&', $params);
            }
          } else {
            $image_button = $this->_app->getDef('module_ec_button_url');
          }

          $button_title = htmlspecialchars($this->_app->getDef('module_ec_button_title'));

          if ( OSCOM_APP_PAYPAL_EC_STATUS == '0' ) {
            $button_title .= ' (' . $this->code . '; Sandbox)';
          }

          $string .= '<a id="ppECButtonClassicLink" href="' . tep_href_link('ext/modules/payment/paypal/express.php') . '"><img id="ppECButtonClassic" src="' . $image_button . '" border="0" alt="" title="' . $button_title . '" /></a>';
        } else {
          $string .= '<script src="https://www.paypalobjects.com/api/checkout.js"></script>';

          $merchant_id = (OSCOM_APP_PAYPAL_EC_STATUS === '1') ? OSCOM_APP_PAYPAL_LIVE_MERCHANT_ID : OSCOM_APP_PAYPAL_SANDBOX_MERCHANT_ID;
          if (empty($merchant_id)) $merchant_id = ' ';

          $server = (OSCOM_APP_PAYPAL_EC_STATUS === '1') ? 'production' : 'sandbox';

          $ppecset_url = tep_href_link('ext/modules/payment/paypal/express.php', 'format=json');

          $ppecerror_url = tep_href_link('ext/modules/payment/paypal/express.php', 'osC_Action=setECError');

          switch (OSCOM_APP_PAYPAL_EC_INCONTEXT_BUTTON_COLOR) {
            case '3':
              $button_color = 'silver';
              break;

            case '2':
              $button_color = 'blue';
              break;

            default:
            case '1':
              $button_color = 'gold';
              break;
          }

          switch (OSCOM_APP_PAYPAL_EC_INCONTEXT_BUTTON_SIZE) {
            case '3':
              $button_size = 'medium';
              break;

            default:
            case '2':
              $button_size = 'small';
              break;

            case '1':
              $button_size = 'tiny';
              break;
          }

          switch (OSCOM_APP_PAYPAL_EC_INCONTEXT_BUTTON_SHAPE) {
            case '2':
              $button_shape = 'rect';
              break;

            default:
            case '1':
              $button_shape = 'pill';
              break;
          }

          $string .= <<<EOD
<span id="ppECButton"></span>
<script>
paypal.Button.render({
  env: '{$server}',
  style: {
    size: '${button_size}',
    color: '${button_color}',
    shape: '${button_shape}'
  },
  payment: function(resolve, reject) {
    paypal.request.post('${ppecset_url}')
      .then(function(data) {
        if ((data.token !== undefined) && (data.token.length > 0)) {
          resolve(data.token);
        } else {
          window.location = '${ppecerror_url}';
        }
      })
      .catch(function(err) {
        reject(err);

        window.location = '${ppecerror_url}';
      });
  },
  onAuthorize: function(data, actions) {
    return actions.redirect();
  },
  onCancel: function(data, actions) {
    return actions.redirect();
  }
}, '#ppECButton');
</script>
EOD;
        }
      } else {
        $image_button = $this->_app->getDef('module_ec_button_url');

        $button_title = htmlspecialchars($this->_app->getDef('module_ec_button_title'));

        if (OSCOM_APP_PAYPAL_EC_STATUS == '0') {
          $button_title .= ' (' . $this->code . '; Sandbox)';
        }

        $string .= '<a id="ppECButtonPfLink" href="' . tep_href_link('ext/modules/payment/paypal/express.php') . '"><img id="ppECButtonPf" src="' . $image_button . '" border="0" alt="" title="' . $button_title . '" /></a>';
      }

      return $string;
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      return [
        'id' => $this->code,
        'module' => $this->public_title
      ];
    }

    function pre_confirmation_check() {
      global $order;

      if ( !isset($_SESSION['appPayPalEcResult']) ) {
        tep_redirect(tep_href_link('ext/modules/payment/paypal/express.php'));
      }

      if ( OSCOM_APP_PAYPAL_GATEWAY == '1' ) { // PayPal
        if ( !in_array($_SESSION['appPayPalEcResult']['ACK'], ['Success', 'SuccessWithWarning']) ) {
          tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . stripslashes($_SESSION['appPayPalEcResult']['L_LONGMESSAGE0'])));
        } elseif ( !isset($_SESSION['appPayPalEcSecret']) || ($_SESSION['appPayPalEcResult']['PAYMENTREQUEST_0_CUSTOM'] != $_SESSION['appPayPalEcSecret']) ) {
          tep_redirect(tep_href_link('shopping_cart.php'));
        }
      } else { // Payflow
        if ($_SESSION['appPayPalEcResult']['RESULT'] != '0') {
          tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . urlencode($_SESSION['appPayPalEcResult']['OSCOM_ERROR_MESSAGE'])));
        } elseif ( !isset($_SESSION['appPayPalEcSecret']) || ($_SESSION['appPayPalEcResult']['CUSTOM'] != $_SESSION['appPayPalEcSecret']) ) {
          tep_redirect(tep_href_link('shopping_cart.php'));
        }
      }

      $order->info['payment_method'] = '<img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" border="0" alt="PayPal Logo" style="padding: 3px;" />';
    }

    function confirmation() {
      return false;
    }

    function process_button() {
      return false;
    }

    function before_process() {
      if ( OSCOM_APP_PAYPAL_GATEWAY == '1' ) {
        $this->before_process_paypal();
      } else {
        $this->before_process_payflow();
      }
    }

    function before_process_paypal() {
      global $order, $response_array, $customer_data;

      if ( !isset($_SESSION['appPayPalEcResult']) ) {
        tep_redirect(tep_href_link('ext/modules/payment/paypal/express.php'));
      }

      if ( in_array($_SESSION['appPayPalEcResult']['ACK'], ['Success', 'SuccessWithWarning']) ) {
        if ( !isset($_SESSION['appPayPalEcSecret']) || ($_SESSION['appPayPalEcResult']['PAYMENTREQUEST_0_CUSTOM'] != $_SESSION['appPayPalEcSecret']) ) {
          tep_redirect(tep_href_link('shopping_cart.php'));
        }
      } else {
        tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . stripslashes($_SESSION['appPayPalEcResult']['L_LONGMESSAGE0'])));
      }

      if (empty($_SESSION['comments']) && isset($_POST['ppecomments']) && tep_not_null($_POST['ppecomments'])) {
        $_SESSION['comments'] = tep_db_prepare_input($_POST['ppecomments']);

        $order->info['comments'] = $_SESSION['comments'];
      }

      $params = [
        'TOKEN' => $_SESSION['appPayPalEcResult']['TOKEN'],
        'PAYERID' => $_SESSION['appPayPalEcResult']['PAYERID'],
        'PAYMENTREQUEST_0_AMT' => $this->_app->formatCurrencyRaw($order->info['total']),
        'PAYMENTREQUEST_0_CURRENCYCODE' => $order->info['currency']
      ];

      if (is_numeric($_SESSION['sendto']) && ($_SESSION['sendto'] > 0)) {
        $customer_data->get('country', $order->delivery);
        $params['PAYMENTREQUEST_0_SHIPTONAME'] = $customer_data->get('name', $order->delivery);
        $params['PAYMENTREQUEST_0_SHIPTOSTREET'] = $customer_data->get('street_address', $order->delivery);
        $params['PAYMENTREQUEST_0_SHIPTOSTREET2'] = $customer_data->get('suburb', $order->delivery);
        $params['PAYMENTREQUEST_0_SHIPTOCITY'] = $customer_data->get('city', $order->delivery);
        $params['PAYMENTREQUEST_0_SHIPTOSTATE'] = tep_get_zone_code(
          $customer_data->get('country_id', $order->delivery),
          $customer_data->get('zone_id', $order->delivery),
          $customer_data->get('state', $order->delivery));
        $params['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = $customer_data->get('country_iso_code_2', $order->delivery);
        $params['PAYMENTREQUEST_0_SHIPTOZIP'] = $customer_data->get('postcode', $order->delivery);
      }

      $response_array = $this->_app->getApiResult('EC', 'DoExpressCheckoutPayment', $params);

      if ( !in_array($response_array['ACK'], ['Success', 'SuccessWithWarning']) ) {
        if ( $response_array['L_ERRORCODE0'] == '10486' ) {
          if ( OSCOM_APP_PAYPAL_EC_STATUS == '1' ) {
            $paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout';
          } else {
            $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout';
          }

          $paypal_url .= '&token=' . $_SESSION['appPayPalEcResult']['TOKEN'];

          tep_redirect($paypal_url);
        }

        tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . stripslashes($response_array['L_LONGMESSAGE0'])));
      }
    }

    function before_process_payflow() {
      global $order, $response_array, $customer_data;

      if ( !isset($_SESSION['appPayPalEcResult']) ) {
        tep_redirect(tep_href_link('ext/modules/payment/paypal/express.php'));
      }

      if ( $_SESSION['appPayPalEcResult']['RESULT'] == '0' ) {
        if ( !isset($_SESSION['appPayPalEcSecret']) || ($_SESSION['appPayPalEcResult']['CUSTOM'] != $_SESSION['appPayPalEcSecret']) ) {
          tep_redirect(tep_href_link('shopping_cart.php'));
        }
      } else {
        tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . urlencode($_SESSION['appPayPalEcResult']['OSCOM_ERROR_MESSAGE'])));
      }

      if ( empty($_SESSION['comments']) && isset($_POST['ppecomments']) && tep_not_null($_POST['ppecomments']) ) {
        $_SESSION['comments'] = tep_db_prepare_input($_POST['ppecomments']);

        $order->info['comments'] = $_SESSION['comments'];
      }

      $params = [
        'EMAIL' => $customer_data->get('email_address', $order->customer),
        'TOKEN' => $_SESSION['appPayPalEcResult']['TOKEN'],
        'PAYERID' => $_SESSION['appPayPalEcResult']['PAYERID'],
        'AMT' => $this->_app->formatCurrencyRaw($order->info['total']),
        'CURRENCY' => $order->info['currency'],
      ];

      if ( is_numeric($_SESSION['sendto']) && ($_SESSION['sendto'] > 0) ) {
        $customer_data->get('country', $order->delivery);
        $params['SHIPTONAME'] = $customer_data->get('name', $order->delivery);
        $params['SHIPTOSTREET'] = $customer_data->get('street_address', $order->delivery);
        $params['SHIPTOSTREET2'] = $customer_data->get('suburb', $order->delivery);
        $params['SHIPTOCITY'] = $customer_data->get('city', $order->delivery);
        $params['SHIPTOSTATE'] = tep_get_zone_code(
          $customer_data->get('country_id', $order->delivery),
          $customer_data->get('zone_id', $order->delivery),
          $customer_data->get('state', $order->delivery));
        $params['SHIPTOCOUNTRY'] = $customer_data->get('country_iso_code_2', $order->delivery);
        $params['SHIPTOZIP'] = $customer_data->get('postcode', $order->delivery);
      }

      $response_array = $this->_app->getApiResult('EC', 'PayflowDoExpressCheckoutPayment', $params);

      if ( $response_array['RESULT'] != '0' ) {
        tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . urlencode($response_array['OSCOM_ERROR_MESSAGE'])));
      }
    }

    function after_process() {
      if ( OSCOM_APP_PAYPAL_GATEWAY == '1' ) {
        $this->after_process_paypal();
      } else {
        $this->after_process_payflow();
      }
    }

    function after_process_paypal() {
      global $response_array, $order_id;

      $pp_result = 'Transaction ID: ' . htmlspecialchars($response_array['PAYMENTINFO_0_TRANSACTIONID']) . "\n" .
                   'Payer Status: ' . htmlspecialchars($_SESSION['appPayPalEcResult']['PAYERSTATUS']) . "\n" .
                   'Address Status: ' . htmlspecialchars($_SESSION['appPayPalEcResult']['ADDRESSSTATUS']) . "\n" .
                   'Payment Status: ' . htmlspecialchars($response_array['PAYMENTINFO_0_PAYMENTSTATUS']) . "\n" .
                   'Payment Type: ' . htmlspecialchars($response_array['PAYMENTINFO_0_PAYMENTTYPE']) . "\n" .
                   'Pending Reason: ' . htmlspecialchars($response_array['PAYMENTINFO_0_PENDINGREASON']);

      $sql_data = [
        'orders_id' => $order_id,
        'orders_status_id' => OSCOM_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID,
        'date_added' => 'NOW()',
        'customer_notified' => '0',
        'comments' => $pp_result,
      ];

      tep_db_perform('orders_status_history', $sql_data);

      unset($_SESSION['appPayPalEcResult']);
      unset($_SESSION['appPayPalEcSecret']);
    }

    function after_process_payflow() {
      global $response_array, $order_id;

      $pp_result = 'Transaction ID: ' . htmlspecialchars($response_array['PNREF']) . "\n" .
                   'Gateway: Payflow' . "\n" .
                   'PayPal ID: ' . htmlspecialchars($response_array['PPREF']) . "\n" .
                   'Payer Status: ' . htmlspecialchars($_SESSION['appPayPalEcResult']['PAYERSTATUS']) . "\n" .
                   'Address Status: ' . htmlspecialchars($_SESSION['appPayPalEcResult']['ADDRESSSTATUS']) . "\n" .
                   'Payment Status: ' . htmlspecialchars($response_array['PENDINGREASON']) . "\n" .
                   'Payment Type: ' . htmlspecialchars($response_array['PAYMENTTYPE']) . "\n" .
                   'Response: ' . htmlspecialchars($response_array['RESPMSG']) . "\n";

      $sql_data = [
        'orders_id' => $order_id,
        'orders_status_id' => OSCOM_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID,
        'date_added' => 'NOW()',
        'customer_notified' => '0',
        'comments' => $pp_result,
      ];

      tep_db_perform('orders_status_history', $sql_data);

      unset($_SESSION['appPayPalEcResult']);
      unset($_SESSION['appPayPalEcSecret']);

// Manually call PayflowInquiry to retrieve more details about the transaction and to allow admin post-transaction actions
      $response = $this->_app->getApiResult('APP', 'PayflowInquiry', ['ORIGID' => $response_array['PNREF']]);

      if ( isset($response['RESULT']) && ($response['RESULT'] == '0') ) {
        $result = 'Transaction ID: ' . htmlspecialchars($response['ORIGPNREF']) . "\n" .
                  'Gateway: Payflow' . "\n";

        $pending_reason = $response['TRANSSTATE'];
        $payment_status = null;

        switch ( $response['TRANSSTATE'] ) {
          case '3':
            $pending_reason = 'authorization';
            $payment_status = 'Pending';
            break;

          case '4':
            $pending_reason = 'other';
            $payment_status = 'In-Progress';
            break;

          case '6':
            $pending_reason = 'scheduled';
            $payment_status = 'Pending';
            break;

          case '8':
          case '9':
            $pending_reason = 'None';
            $payment_status = 'Completed';
            break;
        }

        if ( isset($payment_status) ) {
          $result .= 'Payment Status: ' . htmlspecialchars($payment_status) . "\n";
        }

        $result .= 'Pending Reason: ' . htmlspecialchars($pending_reason) . "\n";

        switch ( $response['AVSADDR'] ) {
          case 'Y':
            $result .= 'AVS Address: Match' . "\n";
            break;

          case 'N':
            $result .= 'AVS Address: No Match' . "\n";
            break;
        }

        switch ( $response['AVSZIP'] ) {
          case 'Y':
            $result .= 'AVS ZIP: Match' . "\n";
            break;

          case 'N':
            $result .= 'AVS ZIP: No Match' . "\n";
            break;
        }

        switch ( $response['IAVS'] ) {
          case 'Y':
            $result .= 'IAVS: International' . "\n";
            break;

          case 'N':
            $result .= 'IAVS: USA' . "\n";
            break;
        }

        switch ( $response['CVV2MATCH'] ) {
          case 'Y':
            $result .= 'CVV2: Match' . "\n";
            break;

          case 'N':
            $result .= 'CVV2: No Match' . "\n";
            break;
        }

        $sql_data = [
          'orders_id' => $order_id,
          'orders_status_id' => OSCOM_APP_PAYPAL_TRANSACTIONS_ORDER_STATUS_ID,
          'date_added' => 'NOW()',
          'customer_notified' => '0',
          'comments' => $result,
        ];

        tep_db_perform('orders_status_history', $sql_data);
      }
    }

    function get_error() {
      return false;
    }

    function check() {
      $check_query = tep_db_query("SELECT configuration_value FROM configuration WHERE configuration_key = 'OSCOM_APP_PAYPAL_EC_STATUS'");
      if ( tep_db_num_rows($check_query) ) {
        $check = tep_db_fetch_array($check_query);

        return tep_not_null($check['configuration_value']);
      }

      return false;
    }

    function install() {
      tep_redirect(tep_href_link('paypal.php', 'action=configure&subaction=install&module=EC'));
    }

    function remove() {
      tep_redirect(tep_href_link('paypal.php', 'action=configure&subaction=uninstall&module=EC'));
    }

    function keys() {
      return ['OSCOM_APP_PAYPAL_EC_SORT_ORDER'];
    }

    function getProductType($id, $attributes) {
      foreach ( $attributes as $a ) {
        $virtual_check_query = tep_db_query("SELECT pad.products_attributes_id FROM products_attributes pa, products_attributes_download pad WHERE pa.products_id = '" . (int)$id . "' and pa.options_values_id = '" . (int)$a['value_id'] . "' AND pa.products_attributes_id = pad.products_attributes_id LIMIT 1");

        if ( tep_db_num_rows($virtual_check_query) == 1 ) {
          return 'Digital';
        }
      }

      return 'Physical';
    }

    function templateClassExists() {
      return class_exists('oscTemplate') && isset($GLOBALS['oscTemplate']) && is_object($GLOBALS['oscTemplate']) && (get_class($GLOBALS['oscTemplate']) == 'oscTemplate');
    }

  }
