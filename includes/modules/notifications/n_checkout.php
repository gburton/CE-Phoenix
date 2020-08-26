<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class n_checkout extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_NOTIFICATIONS_CHECKOUT_';

    const TRIGGERS = [ 'checkout' ];
    const REQUIRES = [ 'address', 'greeting', 'name', 'email_address' ];

    public function notify($order) {
      global $order_id, $customer;

      if (DOWNLOAD_ENABLED == 'true') {
        $attributes_sql = <<<'EOSQL'
SELECT popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix,
       pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename
  FROM products_options popt, products_options_values poval, products_attributes pa
    LEFT JOIN products_attributes_download pad ON pa.products_attributes_id=pad.products_attributes_id
  WHERE pa.products_id = %d
    AND pa.options_id = %d
    AND pa.options_id = popt.products_options_id
    AND pa.options_values_id = %d
    AND pa.options_values_id = poval.products_options_values_id
    AND popt.language_id = %d
    AND poval.language_id = %d
EOSQL;
      } else {
        $attributes_sql = <<<'EOSQL'
SELECT popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix
  FROM products_options popt, products_options_values poval, products_attributes pa
  WHERE pa.products_id = %d
    AND pa.options_id = %d
    AND pa.options_id = popt.products_options_id
    AND pa.options_values_id = %d
    AND pa.options_values_id = poval.products_options_values_id
    AND popt.language_id = %d
    AND poval.language_id = %d
EOSQL;
      }

      ob_start();
      include $GLOBALS['oscTemplate']->map_to_template(__FILE__);
      $email_order = ob_get_clean();

      $parameters = ['order' => $order, 'email' => &$email_order];
      echo $GLOBALS['OSCOM_Hooks']->call('siteWide', 'orderMail', $parameters);

      $accepted = tep_mail($order->customer['name'], $order->customer['email_address'], MODULE_NOTIFICATIONS_CHECKOUT_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

      // send emails to other people
      if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
        tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, MODULE_NOTIFICATIONS_CHECKOUT_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      }

      return $accepted;
    }

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Checkout Notification module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
      ];
    }

  }

