<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  $sql_data = [
    'customers_id' => $customer_id,
    'customers_name' => $order->customer['name'],
    'customers_company' => $order->customer['company'],
    'customers_street_address' => $order->customer['street_address'],
    'customers_suburb' => $order->customer['suburb'],
    'customers_city' => $order->customer['city'],
    'customers_postcode' => $order->customer['postcode'],
    'customers_state' => $order->customer['state'],
    'customers_country' => $order->customer['country']['title'],
    'customers_telephone' => $order->customer['telephone'],
    'customers_email_address' => $order->customer['email_address'],
    'customers_address_format_id' => $order->customer['format_id'],
    'delivery_name' => $order->delivery['name'],
    'delivery_company' => $order->delivery['company'],
    'delivery_street_address' => $order->delivery['street_address'],
    'delivery_suburb' => $order->delivery['suburb'],
    'delivery_city' => $order->delivery['city'],
    'delivery_postcode' => $order->delivery['postcode'],
    'delivery_state' => $order->delivery['state'],
    'delivery_country' => $order->delivery['country']['title'],
    'delivery_address_format_id' => $order->delivery['format_id'],
    'billing_name' => $order->billing['name'],
    'billing_company' => $order->billing['company'],
    'billing_street_address' => $order->billing['street_address'],
    'billing_suburb' => $order->billing['suburb'],
    'billing_city' => $order->billing['city'],
    'billing_postcode' => $order->billing['postcode'],
    'billing_state' => $order->billing['state'],
    'billing_country' => $order->billing['country']['title'],
    'billing_address_format_id' => $order->billing['format_id'],
    'payment_method' => $order->info['payment_method'],
    'date_purchased' => 'now()',
    'orders_status' => $order->info['order_status'],
    'currency' => $order->info['currency'],
    'currency_value' => $order->info['currency_value'],
  ];

  tep_db_perform('orders', $sql_data);

  $order_id = tep_db_insert_id();

  foreach ($order_totals as $order_total) {
    $sql_data = [
      'orders_id' => $order_id,
      'title' => $order_total['title'],
      'text' => $order_total['text'],
      'value' => $order_total['value'],
      'class' => $order_total['code'],
      'sort_order' => $order_total['sort_order'],
    ];

    tep_db_perform('orders_total', $sql_data);
  }

  foreach ($order->products as $product) {
    $sql_data = [
      'orders_id' => $order_id,
      'products_id' => tep_get_prid($product['id']),
      'products_model' => $product['model'],
      'products_name' => $product['name'],
      'products_price' => $product['price'],
      'final_price' => $product['final_price'],
      'products_tax' => $product['tax'],
      'products_quantity' => $product['qty']];

    tep_db_perform('orders_products', $sql_data);

    $order_products_id = tep_db_insert_id();

    if (isset($product['attributes'])) {
      foreach ($product['attributes'] as $attribute) {
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
        $attributes_query = tep_db_query(sprintf($attributes_sql, (int)$product['id'], (int)$attribute['option_id'], (int)$attribute['value_id'], (int)$languages_id));
        $attributes_values = tep_db_fetch_array($attributes_query);

        $sql_data = [
          'orders_id' => $order_id,
          'orders_products_id' => $order_products_id,
          'products_options' => $attributes_values['products_options_name'],
          'products_options_values' => $attributes_values['products_options_values_name'],
          'options_values_price' => $attributes_values['options_values_price'],
          'price_prefix' => $attributes_values['price_prefix'],
        ];

        tep_db_perform('orders_products_attributes', $sql_data);

        if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
          $sql_data = [
            'orders_id' => $order_id,
            'orders_products_id' => $order_products_id,
            'orders_products_filename' => $attributes_values['products_attributes_filename'],
            'download_maxdays' => $attributes_values['products_attributes_maxdays'],
            'download_count' => $attributes_values['products_attributes_maxcount'],
          ];

          tep_db_perform('orders_products_download', $sql_data);
        }
      }
    }
  }
