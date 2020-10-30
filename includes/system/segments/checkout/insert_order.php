<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  $order =& $GLOBALS['order'];

  $GLOBALS['customer_data']->get('country', $order->customer);
  $GLOBALS['customer_data']->get('country', $order->delivery);
  $GLOBALS['customer_data']->get('country', $order->billing);

  $sql_data = [];
  $sql_data['orders'] = [
    'customers_id' => $_SESSION['customer_id'],
    'customers_name' => $GLOBALS['customer_data']->get('name', $order->customer),
    'customers_company' => $GLOBALS['customer_data']->get('company', $order->customer),
    'customers_street_address' => $GLOBALS['customer_data']->get('street_address', $order->customer),
    'customers_suburb' => $GLOBALS['customer_data']->get('suburb', $order->customer),
    'customers_city' => $GLOBALS['customer_data']->get('city', $order->customer),
    'customers_postcode' => $GLOBALS['customer_data']->get('postcode', $order->customer),
    'customers_state' => $GLOBALS['customer_data']->get('state', $order->customer),
    'customers_country' => $GLOBALS['customer_data']->get('country_name', $order->customer),
    'customers_telephone' => $GLOBALS['customer_data']->get('telephone', $order->customer),
    'customers_email_address' => $GLOBALS['customer_data']->get('email_address', $order->customer),
    'customers_address_format_id' => $GLOBALS['customer_data']->get('format_id', $order->customer),
    'delivery_name' => $GLOBALS['customer_data']->get('name', $order->delivery),
    'delivery_company' => $GLOBALS['customer_data']->get('company', $order->delivery),
    'delivery_street_address' => $GLOBALS['customer_data']->get('street_address', $order->delivery),
    'delivery_suburb' => $GLOBALS['customer_data']->get('suburb', $order->delivery),
    'delivery_city' => $GLOBALS['customer_data']->get('city', $order->delivery),
    'delivery_postcode' => $GLOBALS['customer_data']->get('postcode', $order->delivery),
    'delivery_state' => $GLOBALS['customer_data']->get('state', $order->delivery),
    'delivery_country' => $GLOBALS['customer_data']->get('country_name', $order->delivery),
    'delivery_address_format_id' => $GLOBALS['customer_data']->get('format_id', $order->delivery),
    'billing_name' => $GLOBALS['customer_data']->get('name', $order->billing),
    'billing_company' => $GLOBALS['customer_data']->get('company', $order->billing),
    'billing_street_address' => $GLOBALS['customer_data']->get('street_address', $order->billing),
    'billing_suburb' => $GLOBALS['customer_data']->get('suburb', $order->billing),
    'billing_city' => $GLOBALS['customer_data']->get('city', $order->billing),
    'billing_postcode' => $GLOBALS['customer_data']->get('postcode', $order->billing),
    'billing_state' => $GLOBALS['customer_data']->get('state', $order->billing),
    'billing_country' => $GLOBALS['customer_data']->get('country_name', $order->billing),
    'billing_address_format_id' => $GLOBALS['customer_data']->get('format_id', $order->billing),
    'payment_method' => $order->info['payment_method'],
    'date_purchased' => 'NOW()',
    'orders_status' => $order->info['order_status'],
    'currency' => $order->info['currency'],
    'currency_value' => $order->info['currency_value'],
  ];

  $order_id =& $GLOBALS['order_id'];

  $sql_data['orders_total'] = [];
  foreach ($GLOBALS['order']->totals as $order_total) {
    $sql_data['orders_total'][] = [
      'title' => $order_total['title'],
      'text' => $order_total['text'],
      'value' => $order_total['value'],
      'class' => $order_total['code'],
      'sort_order' => $order_total['sort_order'],
    ];
  }

  if (DOWNLOAD_ENABLED == 'true') {
    $attributes_sql = <<<'EOSQL'
SELECT popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix,
       pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename
  FROM products_options popt
    INNER JOIN products_attributes pa ON pa.options_id = popt.products_options_id
    INNER JOIN products_options_values poval ON pa.options_values_id = poval.products_options_values_id AND popt.language_id = poval.language_id
    LEFT JOIN products_attributes_download pad ON pa.products_attributes_id=pad.products_attributes_id
  WHERE pa.products_id = %d
    AND pa.options_id = %d
    AND pa.options_values_id = %d
    AND popt.language_id = %d
EOSQL;
  } else {
    $attributes_sql = <<<'EOSQL'
SELECT popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix
  FROM products_options popt
    INNER JOIN products_attributes pa ON pa.options_id = popt.products_options_id
    INNER JOIN products_options_values poval ON pa.options_values_id = poval.products_options_values_id AND popt.language_id = poval.language_id
  WHERE pa.products_id = %d
    AND pa.options_id = %d
    AND pa.options_values_id = %d
    AND popt.language_id = %d
EOSQL;
  }

  $sql_data['orders_products'] = [];
  $sql_data['orders_products_attributes'] = [];
  $sql_data['orders_products_download'] = [];
  foreach ($order->products as $i => $product) {
    $sql_data['orders_products'][$i] = [
      'products_id' => tep_get_prid($product['id']),
      'products_model' => $product['model'],
      'products_name' => $product['name'],
      'products_price' => $product['price'],
      'final_price' => $product['final_price'],
      'products_tax' => $product['tax'],
      'products_quantity' => $product['qty'],
    ];

    $sql_data['orders_products_attributes'][$i] = [];
    $sql_data['orders_products_download'][$i] = [];
    foreach (($product['attributes'] ?? []) as $attribute) {
      $attributes_query = tep_db_query(sprintf($attributes_sql, (int)$product['id'], (int)$attribute['option_id'], (int)$attribute['value_id'], (int)$_SESSION['languages_id']));
      $attributes_values = tep_db_fetch_array($attributes_query);

      $sql_data['orders_products_attributes'][$i][] = [
        'products_options' => $attributes_values['products_options_name'],
        'products_options_values' => $attributes_values['products_options_values_name'],
        'options_values_price' => $attributes_values['options_values_price'],
        'price_prefix' => $attributes_values['price_prefix'],
      ];


      if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
        $sql_data['orders_products_download'][$i][] = [
          'orders_products_filename' => $attributes_values['products_attributes_filename'],
          'download_maxdays' => $attributes_values['products_attributes_maxdays'],
          'download_count' => $attributes_values['products_attributes_maxcount'],
        ];
      }
    }
  }

  $parameters = [ 'sql_data' => &$sql_data ];
  $GLOBALS['OSCOM_Hooks']->call('siteWide', 'insertOrder', $parameters);

  tep_db_perform('orders', $sql_data['orders']);
  $GLOBALS['order_id'] = tep_db_insert_id();
  $order->set_id($GLOBALS['order_id']);

  foreach ($sql_data['orders_total'] as $order_total) {
    $order_total['orders_id'] = $order->get_id();
    tep_db_perform('orders_total', $order_total);
  }

  foreach ($sql_data['orders_products'] as $i => $product) {
    $product['orders_id'] = $order->get_id();
    tep_db_perform('orders_products', $product);
    $order_products_id = tep_db_insert_id();

    foreach ($sql_data['orders_products_attributes'][$i] as $attribute) {
      $attribute['orders_id'] = $order->get_id();
      $attribute['orders_products_id'] = $order_products_id;
      tep_db_perform('orders_products_attributes', $attribute);
    }

    foreach ($sql_data['orders_products_download'][$i] as $download) {
      $download['orders_id'] = $order->get_id();
      $download['orders_products_id'] = $order_products_id;
      tep_db_perform('orders_products_download', $download);
    }
  }
