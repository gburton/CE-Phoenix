<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ot_shipping extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_ORDER_TOTAL_SHIPPING_';

    public $output = [];

    public static function can_ship_free_to($country_id) {
      switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
        case 'national':
          return $country_id == STORE_COUNTRY;
        case 'international':
          return $country_id != STORE_COUNTRY;
        case 'both':
          return true;
      }

      return false;
    }

    public static function is_eligible_free_shipping($country_id, $amount) {
      return defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING')
        && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'True')
        && self::can_ship_free_to($order->delivery['country_id'])
        && ($amount >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER);
    }

    function process() {
      global $order, $currencies;

      if (self::is_eligible_free_shipping($order->delivery['country_id'], $order->info['total'] - $order->info['shipping_cost'])) {
        $order->info['shipping_method'] = FREE_SHIPPING_TITLE;
        $order->info['total'] -= $order->info['shipping_cost'];
        $order->info['shipping_cost'] = 0;
      }

      $module = substr($GLOBALS['shipping']['id'], 0, strpos($GLOBALS['shipping']['id'], '_'));

      if (tep_not_null($order->info['shipping_method'])) {
        if ($GLOBALS[$module]->tax_class > 0) {
          $shipping_tax = tep_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $shipping_tax_description = tep_get_tax_description($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);

          $order->info['tax'] += tep_calculate_tax($order->info['shipping_cost'], $shipping_tax);
          $order->info['tax_groups']["$shipping_tax_description"] += tep_calculate_tax($order->info['shipping_cost'], $shipping_tax);
          $order->info['total'] += tep_calculate_tax($order->info['shipping_cost'], $shipping_tax);

          if (DISPLAY_PRICE_WITH_TAX == 'true') {
            $order->info['shipping_cost'] += tep_calculate_tax($order->info['shipping_cost'], $shipping_tax);
          }
        }

        $this->output[] = [
          'title' => $order->info['shipping_method'] . ':',
          'text' => $currencies->format($order->info['shipping_cost'], true, $order->info['currency'], $order->info['currency_value']),
          'value' => $order->info['shipping_cost'],
        ];
      }
    }

    public function get_parameters() {
      return [
        'MODULE_ORDER_TOTAL_SHIPPING_STATUS' => [
          'title' => 'Display Shipping',
          'value' => 'True',
          'desc' => 'Do you want to display the order shipping cost?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '2',
          'desc' => 'Sort order of display.',
        ],
        'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING' => [
          'title' => 'Allow Free Shipping',
          'value' => 'False',
          'desc' => 'Do you want to allow free shipping?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER' => [
          'title' => 'Free Shipping For Orders Over',
          'value' => '50',
          'desc' => 'Provide free shipping for orders over the set amount.',
          'use_func' => 'currencies->format',
        ],
        'MODULE_ORDER_TOTAL_SHIPPING_DESTINATION' => [
          'title' => 'Provide Free Shipping For Orders Made',
          'value' => 'national',
          'desc' => 'Provide free shipping for orders sent to the set destination.',
          'set_func' => "tep_cfg_select_option(['national', 'international', 'both'], ",
        ],
      ];
    }

  }
