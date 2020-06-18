<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  chdir('../../../../');
  require 'includes/application_top.php';

// if there is nothing in the customer's cart, redirect them to the shopping cart page
  if ($_SESSION['cart']->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

// initialize variables if the customer is not logged in
  if (!isset($_SESSION['customer_id'])) {
    $customer_id = 0;
  }

  require "includes/languages/$language/create_account.php";

  $paypal_pro_payflow_ec = new paypal_pro_payflow_ec();

  if (!$paypal_pro_payflow_ec->check() || !$paypal_pro_payflow_ec->enabled) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

  if ( !isset($_SESSION['sendto']) ) {
    if ( $customer instanceof customer ) {
      $_SESSION['sendto'] = $customer->get('default_address_id');
    } else {
      $country = [ 'country_id' => STORE_COUNTRY ];
      $country = $customer_data->get('country', $country);

      $_SESSION['sendto'] = [
        'firstname' => '',
        'lastname' => '',
        'name' => '',
        'company' => '',
        'street_address' => '',
        'suburb' => '',
        'postcode' => '',
        'city' => '',
        'zone_id' => STORE_ZONE,
        'zone_name' => tep_get_zone_name(STORE_COUNTRY, STORE_ZONE, ''),
        'country_id' => STORE_COUNTRY,
        'country_name' => $country['name'],
        'country_iso_code_2' => $country['iso_code_2'],
        'country_iso_code_3' => $country['iso_code_3'],
        'address_format_id' => $country['format_id'],
      ];
    }
  }

  if ( !isset($_SESSION['billto']) ) {
    $_SESSION['billto'] = $_SESSION['sendto'];
  }

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
  $_SESSION['cartID'] = $_SESSION['cart']->cartID;

  switch ($_GET['osC_Action']) {
    case 'retrieve':
      $response_array = $paypal_pro_payflow_ec->getExpressCheckoutDetails($_GET['token']);

      if ($response_array['RESULT'] == '0') {
        if ( !isset($_SESSION['ppeuk_secret']) || ($response_array['CUSTOM'] != $ppeuk_secret) ) {
          tep_redirect(tep_href_link('shopping_cart.php', '', 'SSL'));
        }

        $_SESSION['payment'] = $paypal_pro_payflow_ec->code;
        $_SESSION['ppeuk_token'] = $response_array['TOKEN'];
        $_SESSION['ppeuk_payerid'] = $response_array['PAYERID'];
        $_SESSION['ppeuk_payerstatus'] = $response_array['PAYERSTATUS'];
        $_SESSION['ppeuk_addressstatus'] = $response_array['ADDRESSSTATUS'];

// check if e-mail address exists in database and login or create customer account
        if (!isset($_SESSION['customer_id'])) {
          $email_address = tep_db_prepare_input($response_array['EMAIL']);

          $customer_data->build_read(['id', 'password'], 'customers', ['email_address' => $email_address]);
          $check_query = tep_db_query("SELECT * FROM customers WHERE customers_email_address = '" . tep_db_input($email_address) . "' LIMIT 1");
          if (tep_db_num_rows($check_query)) {
            $check = tep_db_fetch_array($check_query);

// Force the customer to log into their local account if payerstatus is unverified and a local password is set
            if ( ($response_array['PAYERSTATUS'] == 'unverified') && !empty($check['customers_password']) ) {
              $messageStack->add_session('login', MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_WARNING_LOCAL_LOGIN_REQUIRED, 'warning');

              $_SESSION['navigation']->set_snapshot();

              $login_url = tep_href_link('login.php', '', 'SSL');
              $login_email_address = tep_output_string($response_array['EMAIL']);

      $output = <<<EOD
<form name="pe" action="{$login_url}" method="post" target="_top">
  <input type="hidden" name="email_address" value="{$login_email_address}" />
</form>
<script>
document.pe.submit();
</script>
EOD;

              echo $output;
              exit;
            } else {
              $_SESSION['customer_id'] = $check['customers_id'];
            }
          } else {
            $customers_firstname = tep_db_prepare_input($response_array['FIRSTNAME']);
            $customers_lastname = tep_db_prepare_input($response_array['LASTNAME']);
            $name = tep_db_prepare_input($response_array['FIRSTNAME'] . ' ' . $response_array['LASTNAME']);

            $customer_details = [
              'firstname' => $customers_firstname,
              'lastname' => $customers_lastname,
              'email_address' => $email_address,
              'telephone' => '',
              'fax' => '',
              'newsletter' => '0',
              'password' => '',
            ];

            ;

            if (isset($response_array['PHONENUM']) && tep_not_null($response_array['PHONENUM'])) {
              $customer_details['telephone'] = tep_db_prepare_input($response_array['PHONENUM']);
            }

            $email_text = null;
            // Only generate a password and send an email if the Set Password Content Module is not enabled
            if ( $customer_data->has(['password']) && ( !defined('MODULE_CONTENT_ACCOUNT_SET_PASSWORD_STATUS') || (MODULE_CONTENT_ACCOUNT_SET_PASSWORD_STATUS != 'True') )) {
              $customer_details['password'] = tep_create_random_value(max(ENTRY_PASSWORD_MIN_LENGTH, 8));

              // build the message content
              $email_text = sprintf(EMAIL_GREET_NONE, $customer->get('short_name'))
                          . EMAIL_WELCOME
                          . sprintf(MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_EMAIL_PASSWORD, $email_address, $customer_details['password'])
                          . EMAIL_TEXT . EMAIL_CONTACT . EMAIL_WARNING;
            }

            $customer_data->create($customer_details);

            $_SESSION['customer_id'] = $customer_data->get('id', $customer_details);

            if (isset($email_text)) {
              tep_mail($customer_data->get('name', $customer_details), $email_address, EMAIL_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
            }

            $OSCOM_Hooks->call('siteWide', 'postAccountCreation');
          }

          $OSCOM_Hooks->call('siteWide', 'postLogin');
          if (SESSION_RECREATE == 'True') {
            tep_session_recreate();
          }

          $customer_id =& $_SESSION['customer_id'];
          tep_reset_session_token();
        }

// check if paypal shipping address exists in the address book
        $ship_firstname = tep_db_prepare_input(substr($response_array['SHIPTONAME'], 0, strpos($response_array['SHIPTONAME'], ' ')));
        $ship_lastname = tep_db_prepare_input(substr($response_array['SHIPTONAME'], strpos($response_array['SHIPTONAME'], ' ')+1));
        $ship_address = tep_db_prepare_input($response_array['SHIPTOSTREET']);
        $ship_city = tep_db_prepare_input($response_array['SHIPTOCITY']);
        $ship_zone = tep_db_prepare_input($response_array['SHIPTOSTATE']);
        $ship_zone_id = 0;
        $ship_postcode = tep_db_prepare_input($response_array['SHIPTOZIP']);
        $ship_country = tep_db_prepare_input($response_array['SHIPTOCOUNTRY']);
        $ship_country_id = 0;
        $ship_address_format_id = 1;

        $country_query = tep_db_query("SELECT countries_id, address_format_id FROM countries WHERE countries_iso_code_2 = '" . tep_db_input($ship_country) . "' LIMIT 1");
        if ($country = tep_db_fetch_array($country_query)) {
          $ship_country_id = $country['countries_id'];
          $ship_address_format_id = $country['address_format_id'];
        }

        if ($ship_country_id > 0) {
          $zone_query = tep_db_query("SELECT zone_id FROM zones WHERE zone_country_id = " . (int)$ship_country_id . " AND (zone_name = '" . tep_db_input($ship_zone) . "' OR zone_code = '" . tep_db_input($ship_zone) . "') LIMIT 1");
          if ($zone = tep_db_fetch_array($zone_query)) {
            $ship_zone_id = $zone['zone_id'];
          }
        }

        $check_query = tep_db_query("SELECT address_book_id FROM address_book WHERE customers_id = " . (int)$_SESSION['customer_id'] . " AND entry_firstname = '" . tep_db_input($ship_firstname) . "' and entry_lastname = '" . tep_db_input($ship_lastname) . "' and entry_street_address = '" . tep_db_input($ship_address) . "' and entry_postcode = '" . tep_db_input($ship_postcode) . "' and entry_city = '" . tep_db_input($ship_city) . "' AND (entry_state = '" . tep_db_input($ship_zone) . "' OR entry_zone_id = " . (int)$ship_zone_id . ") AND entry_country_id = " . (int)$ship_country_id . " LIMIT 1");
        if ($check = tep_db_fetch_array($check_query)) {
          $_SESSION['sendto'] = $check['address_book_id'];
        } else {
          $customer_details = [
            'customers_id' => $_SESSION['customer_id'],
            'entry_firstname' => $ship_firstname,
            'entry_lastname' => $ship_lastname,
            'entry_street_address' => $ship_address,
            'entry_postcode' => $ship_postcode,
            'entry_city' => $ship_city,
            'entry_country_id' => $ship_country_id,
          ];

          if ($customer_data->has(['state'])) {
            if ($ship_zone_id > 0) {
              $customer_details['entry_zone_id'] = $ship_zone_id;
              $customer_details['entry_state'] = '';
            } else {
              $customer_details['entry_zone_id'] = '0';
              $customer_details['entry_state'] = $ship_zone;
            }
          }

          tep_db_perform('address_book', $customer_details);

          $_SESSION['sendto'] = tep_db_insert_id();

          if ($customer->get('default_address_id') < 1) {
            tep_db_query("UPDATE customers SET customers_default_address_id = " . (int)$_SESSION['sendto'] . " WHERE customers_id = " . (int)$_SESSION['customer_id']);
          }
        }

        $_SESSION['billto'] = $_SESSION['sendto'];
        $billto =& $_SESSION['billto'];
        $sendto =& $_SESSION['sendto'];

        $order = new order();

        if ($_SESSION['cart']->get_content_type() === 'virtual') {
          $_SESSION['shipping'] = false;
          $_SESSION['sendto'] = false;
        } else {
          $total_weight = $_SESSION['cart']->show_weight();
          $total_count = $_SESSION['cart']->count_contents();

// load all enabled shipping modules
          $shipping_modules = new shipping();

          $_SESSION['shipping'] = false;
          $shipping =& $_SESSION['shipping'];

          if ( ot_shipping::is_eligible_free_shipping($order->delivery['country_id'], $order->info['total']) ) {
            include "includes/languages/$language/modules/order_total/ot_shipping.php";

            $shipping = 'free_free';
          } elseif ( tep_count_shipping_modules() > 0 ) {
            $shipping_modules->quote();

// select cheapest shipping method
            $shipping = $shipping_modules->cheapest();
            $shipping = $shipping['id'];
          } elseif ( defined('SHIPPING_ALLOW_UNDEFINED_ZONES') && (SHIPPING_ALLOW_UNDEFINED_ZONES == 'False') ) {
            unset($_SESSION['shipping']);

            $messageStack->add_session('checkout_address', MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_NO_SHIPPING_AVAILABLE_TO_SHIPPING_ADDRESS, 'error');

            $_SESSION['ppecuk_right_turn'] = true;

            tep_redirect(tep_href_link('checkout_shipping_address.php', '', 'SSL'));
          }

          if (strpos($shipping, '_')) {
            list($module, $method) = explode('_', $shipping, 2);

            if ( is_object($$module) || ('free_free' === $shipping) ) {
              if ('free_free' === $shipping) {
                $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
                $quote[0]['methods'][0]['cost'] = '0';
              } else {
                $quote = $shipping_modules->quote($method, $module);
              }

              if (isset($quote['error'])) {
                unset($_SESSION['shipping']);

                tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
              } elseif ( isset($quote[0]['methods'][0]['title'], $quote[0]['methods'][0]['cost']) ) {
                $shipping = [
                  'id' => $shipping,
                  'title' => (('free_free' === $shipping) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'),
                  'cost' => $quote[0]['methods'][0]['cost'],
                ];
              }
            }
          }
        }

/* useraction=commit       tep_redirect(tep_href_link('checkout_process.php', '', 'SSL')); */
        tep_redirect(tep_href_link('checkout_confirmation.php', '', 'SSL'));
      } else {
        tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . urlencode($response_array['OSCOM_ERROR_MESSAGE']), 'SSL'));
      }

      break;

    default:
      if (MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_TRANSACTION_SERVER == 'Live') {
        $paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout';
      } else {
        $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout';
      }

      $order = new order();

      $params = [
        'CURRENCY' => $order->info['currency'],
        'EMAIL' => $order->customer['email_address'],
        'ALLOWNOTE' => '0',
      ];

// A billing address is required for digital orders so we use the shipping address PayPal provides
//      if ($order->content_type == 'virtual') {
//        $params['NOSHIPPING'] = '1';
//      }

      $item_params = [];

      $line_item_no = 0;

      foreach ($order->products as $product) {
        if ( DISPLAY_PRICE_WITH_TAX == 'true' ) {
          $product_price = $paypal_pro_payflow_ec->format_raw($product['final_price'] + tep_calculate_tax($product['final_price'], $product['tax']));
        } else {
          $product_price = $paypal_pro_payflow_ec->format_raw($product['final_price']);
        }

        $item_params['L_NAME' . $line_item_no] = $product['name'];
        $item_params['L_COST' . $line_item_no] = $product_price;
        $item_params['L_QTY' . $line_item_no] = $product['qty'];

        $line_item_no++;
      }

      $params['BILLTOFIRSTNAME'] = $order->billing['firstname'];
      $params['BILLTOLASTNAME'] = $order->billing['lastname'];
      $params['BILLTOSTREET'] = $order->billing['street_address'];
      $params['BILLTOCITY'] = $order->billing['city'];
      $params['BILLTOSTATE'] = tep_get_zone_code($order->billing['country']['id'], $order->billing['zone_id'], $order->billing['state']);
      $params['BILLTOCOUNTRY'] = $order->billing['country']['iso_code_2'];
      $params['BILLTOZIP'] = $order->billing['postcode'];

      if (tep_not_null($order->delivery['street_address'])) {
        $params['SHIPTONAME'] = $order->delivery['name'];
        $params['SHIPTOSTREET'] = $order->delivery['street_address'];
        $params['SHIPTOCITY'] = $order->delivery['city'];
        $params['SHIPTOSTATE'] = tep_get_zone_code($order->delivery['country']['id'], $order->delivery['zone_id'], $order->delivery['state']);
        $params['SHIPTOCOUNTRY'] = $order->delivery['country']['iso_code_2'];
        $params['SHIPTOZIP'] = $order->delivery['postcode'];
      }

      if ($_SESSION['cart']->get_content_type() !== 'virtual') {
        $total_weight = $_SESSION['cart']->show_weight();
        $total_count = $_SESSION['cart']->count_contents();

// load all enabled shipping modules
        $shipping_modules = new shipping();

        if ( ot_shipping::is_eligible_free_shipping($order->delivery['country_id'], $order->info['total']) ) {
          include "includes/languages/$language/modules/order_total/ot_shipping.php";

          $quotes[] = [
            'id' => 'free_free',
            'name' => FREE_SHIPPING_TITLE,
            'label' => '',
            'cost' => '0.00',
            'tax' => '0',
          ];
        } elseif ( tep_count_shipping_modules() > 0 ) {
          foreach ($shipping_modules->quote() as $quote) {
            if (isset($quote['error'])) {
              continue;
            }

            foreach ($quote['methods'] as $rate) {
              $quotes[] = [
                'id' => $quote['id'] . '_' . $rate['id'],
                'name' => $quote['module'],
                'label' => $rate['title'],
                'cost' => $rate['cost'],
                'tax' => $quote['tax'],
              ];
            }
          }
        } else {
          if ( defined('SHIPPING_ALLOW_UNDEFINED_ZONES') && (SHIPPING_ALLOW_UNDEFINED_ZONES == 'False') ) {
            unset($_SESSION['shipping']);

            $messageStack->add_session('checkout_address', MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_ERROR_NO_SHIPPING_AVAILABLE_TO_SHIPPING_ADDRESS);

            tep_redirect(tep_href_link('checkout_shipping_address.php', '', 'SSL'));
          }
        }
      }

      $counter = 0;
      $cheapest_rate = null;
      $expensive_rate = 0;
      $cheapest_counter = $counter;
      $default_shipping = null;

      foreach ($quotes as $quote) {
        $shipping_rate = $paypal_pro_payflow_ec->format_raw($quote['cost'] + tep_calculate_tax($quote['cost'], $quote['tax']));

        if (is_null($cheapest_rate) || ($shipping_rate < $cheapest_rate)) {
          $cheapest_rate = $shipping_rate;
          $cheapest_counter = $counter;
        }

        if ($shipping_rate > $expensive_rate) {
          $expensive_rate = $shipping_rate;
        }

        if (isset($_SESSION['shipping']) && ($_SESSION['shipping']['id'] == $quote['id'])) {
          $default_shipping = $counter;
        }

        $counter++;
      }

      if (isset($default_shipping)) {
        $cheapest_counter = $default_shipping;
      } else {
        if ( empty($quotes) ) {
          $_SESSION['shipping'] = false;
        } else {
          $_SESSION['shipping'] = [
            'id' => $quotes[$cheapest_counter]['id'],
            'title' => trim($quotes[$cheapest_counter]['name'] . ' ' . $quotes[$cheapest_counter]['label']),
            'cost' => $paypal_pro_payflow_ec->format_raw($quotes[$cheapest_counter]['cost']),
          ];

          $default_shipping = $cheapest_counter;
        }

        $shipping =& $_SESSION['shipping'];
      }

// set shipping for order total calculations; shipping in $item_params includes taxes
      if (isset($default_shipping)) {
        $order->info['shipping_method'] = trim($quotes[$default_shipping]['name'] . ' ' . $quotes[$default_shipping]['label']);
        $order->info['shipping_cost'] = $paypal_pro_payflow_ec->format_raw($quotes[$default_shipping]['cost']
                                      + tep_calculate_tax($quotes[$default_shipping]['cost'], $quotes[$default_shipping]['tax']));

        $order->info['total'] = $order->info['subtotal'] + $order->info['shipping_cost'];

        if ( DISPLAY_PRICE_WITH_TAX == 'false' ) {
          $order->info['total'] += $order->info['tax'];
        }
      }

      $order_total_modules = new order_total();
      $order->totals = $order_total_modules->process();

// Remove shipping tax from total that was added again in ot_shipping
      if (DISPLAY_PRICE_WITH_TAX == 'true') {
        $order->info['shipping_cost'] = $order->info['shipping_cost'] / (1.0 + ($quotes[$default_shipping]['tax'] / 100));
      }

      $module = substr($shipping['id'], 0, strpos($shipping['id'], '_'));
      $tax = tep_calculate_tax($order->info['shipping_cost'], $quotes[$default_shipping]['tax']);
      $order->info['tax'] -= $tax;
      $order->info['tax_groups'][tep_get_tax_description($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id'])] -= $tax;
      $order->info['total'] -= $tax;

      $items_total = $paypal_pro_payflow_ec->format_raw($order->info['subtotal']);

      foreach ($order->totals as $ot) {
        if ( !in_array($ot['code'], ['ot_subtotal', 'ot_shipping', 'ot_tax', 'ot_total']) ) {
          $item_params['L_NAME' . $line_item_no] = $ot['title'];
          $item_params['L_COST' . $line_item_no] = $paypal_pro_payflow_ec->format_raw($ot['value']);
          $item_params['L_QTY' . $line_item_no] = 1;

          $items_total += $paypal_pro_payflow_ec->format_raw($ot['value']);

          $line_item_no++;
        }
      }

      $params['AMT'] = $paypal_pro_payflow_ec->format_raw($order->info['total']);

      // safely pad higher for dynamic shipping rates (eg, USPS express)
      $item_params['MAXAMT'] = $paypal_pro_payflow_ec->format_raw($params['AMT'] + $expensive_rate + 100, '', 1);
      $item_params['ITEMAMT'] = $items_total;
      $item_params['FREIGHTAMT'] = $paypal_pro_payflow_ec->format_raw($order->info['shipping_cost']);

      $paypal_item_total = $item_params['ITEMAMT'] + $item_params['FREIGHTAMT'];

      if ( DISPLAY_PRICE_WITH_TAX == 'false' ) {
        $item_params['TAXAMT'] = $paypal_pro_payflow_ec->format_raw($order->info['tax']);

        $paypal_item_total += $item_params['TAXAMT'];
      }

      if ( $paypal_pro_payflow_ec->format_raw($paypal_item_total) == $params['AMT'] ) {
        $params = array_merge($params, $item_params);
      }

      if (tep_not_null(MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PAGE_STYLE)) {
        $params['PAGESTYLE'] = MODULE_PAYMENT_PAYPAL_PRO_PAYFLOW_EC_PAGE_STYLE;
      }

      $_SESSION['ppeuk_secret'] = tep_create_random_value(16, 'digits');
      $params['CUSTOM'] = $_SESSION['ppeuk_secret'];

      $response_array = $paypal_pro_payflow_ec->setExpressCheckout($params);

      if ($response_array['RESULT'] == '0') {
        tep_redirect($paypal_url . '&token=' . $response_array['TOKEN'] /*. '&useraction=commit'*/);
      } else {
        tep_redirect(tep_href_link('shopping_cart.php', 'error_message=' . urlencode($response_array['OSCOM_ERROR_MESSAGE']), 'SSL'));
      }

      break;
  }

  tep_redirect(tep_href_link('shopping_cart.php', '', 'SSL'));

  require 'includes/application_bottom.php';
