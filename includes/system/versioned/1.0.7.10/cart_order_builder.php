<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cart_order_builder {

    public static $column_keys = null;
    public static $attributes_sql = <<<'EOSQL'
SELECT
   popt.products_options_name AS `option`,
   poval.products_options_values_name AS value,
   pa.options_id AS option_id,
   pa.options_values_id AS value_id,
   pa.price_prefix AS prefix,
   pa.options_values_price AS price
 FROM products_options popt
  INNER JOIN products_attributes pa ON pa.options_id = popt.products_options_id
  INNER JOIN products_options_values poval ON pa.options_values_id = poval.products_options_values_id AND popt.language_id = poval.language_id
 WHERE pa.products_id = %d AND pa.options_id = %d AND pa.options_values_id = %d AND popt.language_id = %d
EOSQL;

    protected $order;

    public function __construct(&$order) {
      if (is_null(static::$column_keys)) {
        static::$column_keys = [
          'qty' => 'quantity',
          'name' => 'name',
          'model' => 'model',
          'price' => 'price',
          'final_price' => 'final_price',
          'weight' => 'weight',
          'id' => 'id',
        ];
        $parameters = [
          'column_keys' => &static::$column_keys,
          'attributes_sql' => &static::$attributes_sql,
        ];
        $GLOBALS['OSCOM_Hooks']->call('siteWide', 'cartOrderProductColumns', $parameters);
      }

      $this->order =& $order;
    }

    public function build_info() {
      $this->order->info = [
        'order_status' => DEFAULT_ORDERS_STATUS_ID,
        'currency' => $_SESSION['currency'],
        'currency_value' => $GLOBALS['currencies']->currencies[$_SESSION['currency']]['value'],
        'payment_method' => $_SESSION['payment'] ?? null,
        'shipping_method' => $_SESSION['shipping']['title'] ?? null,
        'shipping_cost' => $_SESSION['shipping']['cost'] ?? null,
        'subtotal' => 0,
        'tax' => 0,
        'tax_groups' => [],
        'comments' => ($_SESSION['comments'] ?? ''),
      ];

      if (is_string($_SESSION['payment'] ?? null) && (($GLOBALS[$_SESSION['payment']] ?? null) instanceof $_SESSION['payment'])) {
        $this->order->info['payment_method'] = $GLOBALS[$_SESSION['payment']]->public_title ?? $GLOBALS[$_SESSION['payment']]->title;

        if ( is_numeric($GLOBALS[$_SESSION['payment']]->order_status ?? null) && ($GLOBALS[$_SESSION['payment']]->order_status > 0) ) {
          $this->order->info['order_status'] = $GLOBALS[$_SESSION['payment']]->order_status;
        }
      }
    }

    public function build_addresses() {
      global $customer;

      $this->order->customer = $customer->fetch_to_address(0);
      $this->order->billing = $customer->fetch_to_address($_SESSION['billto'] ?? null);

      if ( !$_SESSION['sendto'] && ('virtual' !== $this->order->content_type) ) {
        $_SESSION['sendto'] = $customer->get('default_sendto');
      }

      $this->order->delivery = $customer->fetch_to_address($_SESSION['sendto']);
    }

    public function build_tax_address() {
      if ('virtual' === $this->order->content_type) {
        return [
          'entry_country_id' => $GLOBALS['customer_data']->get('country_id', $this->order->billing),
          'entry_zone_id' => $GLOBALS['customer_data']->get('zone_id', $this->order->billing),
        ];
      }

      return [
        'entry_country_id' => $GLOBALS['customer_data']->get('country_id', $this->order->delivery),
        'entry_zone_id' => $GLOBALS['customer_data']->get('zone_id', $this->order->delivery),
      ];
    }

    public function update_per_product($current) {
      $shown_price = $GLOBALS['currencies']->calculate_price($current['final_price'], $current['tax'], $current['qty']);
      $this->order->info['subtotal'] += $shown_price;

      if (DISPLAY_PRICE_WITH_TAX == 'true') {
        $tax = $shown_price - ($shown_price / ((($current['tax'] < 10) ? "1.0" : "1.") . str_replace('.', '', $current['tax'])));
      } else {
        $tax = ($current['tax'] / 100) * $shown_price;
      }
      $this->order->info['tax'] += $tax;

      $tax_description = $current['tax_description'];
      if (!isset($this->order->info['tax_groups']["$tax_description"])) {
        $this->order->info['tax_groups']["$tax_description"] = 0;
      }
      $this->order->info['tax_groups']["$tax_description"] += $tax;
    }

    public function build_attributes($product) {
      $attributes = [];
      foreach ($product['attributes'] as $option => $value) {
        $attributes_query = tep_db_query(sprintf(
          static::$attributes_sql,
          (int)$product['id'],
          (int)$option,
          (int)$value,
          (int)$_SESSION['languages_id']));

        $attributes[] = tep_db_fetch_array($attributes_query);
      }

      return $attributes;
    }

    public function build_products() {
      $tax_address = $this->build_tax_address();

      foreach ($_SESSION['cart']->get_products() as $product) {
        $current = [];
        foreach (static::$column_keys as $order_key => $cart_key) {
          $current[$order_key] = $product[$cart_key];
        }
        $current['tax'] = tep_get_tax_rate($product['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']);
        $current['tax_description'] = tep_get_tax_description($product['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']);

        if ($product['attributes']) {
          $current['attributes'] = $this->build_attributes($product);
        }

        $this->update_per_product($current);

        $this->order->products[] = $current;
      }
    }

    public static function build(&$order) {
      $builder = new cart_order_builder($order);
      $builder->build_info();

      $order->content_type = $_SESSION['cart']->get_content_type();
      $builder->build_addresses();

      $builder->build_products();

      $order->info['total'] = $order->info['subtotal'] + $order->info['shipping_cost'];
      if (DISPLAY_PRICE_WITH_TAX != 'true') {
        $order->info['total'] += $order->info['tax'];
      }

      return $order;
    }

  }
