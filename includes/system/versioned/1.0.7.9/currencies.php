<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

////
// Class to handle currencies
// TABLES: currencies
  class currencies {

    public $currencies;

// class constructor
    public function __construct() {
      $this->currencies = [];
      $currencies_query = tep_db_query("SELECT code, title, symbol_left, symbol_right, decimal_point, thousands_point, decimal_places, value FROM currencies");
      while ($currencies = tep_db_fetch_array($currencies_query)) {
        $this->currencies[$currencies['code']] = [
          'title' => $currencies['title'],
          'symbol_left' => $currencies['symbol_left'],
          'symbol_right' => $currencies['symbol_right'],
          'decimal_point' => $currencies['decimal_point'],
          'thousands_point' => $currencies['thousands_point'],
          'decimal_places' => (int)$currencies['decimal_places'],
          'value' => $currencies['value'],
        ];
      }
    }

// class methods
    public function format($number, $calculate_currency_value = true, $currency_type = null, $currency_value = null) {
      if (empty($currency_type)) {
        $currency_type = $_SESSION['currency'] ?? DEFAULT_CURRENCY;
      }

      if ($calculate_currency_value) {
        $number *= ($currency_value ?? $this->currencies[$currency_type]['value']);
      }

      return $this->currencies[$currency_type]['symbol_left']
           . number_format(
               tep_round($number, $this->currencies[$currency_type]['decimal_places']),
               $this->currencies[$currency_type]['decimal_places'],
               $this->currencies[$currency_type]['decimal_point'],
               $this->currencies[$currency_type]['thousands_point'])
           . $this->currencies[$currency_type]['symbol_right'];
    }

    public function calculate_price($products_price, $products_tax, $quantity = 1) {
      return tep_round(tep_add_tax($products_price, $products_tax), $this->currencies[$_SESSION['currency']]['decimal_places']) * $quantity;
    }

    public function is_set($code) {
      return isset($this->currencies[$code]) && tep_not_null($this->currencies[$code]);
    }

    public function get_value($code) {
      return $this->currencies[$code]['value'];
    }

    public function get_decimal_places($code) {
      return $this->currencies[$code]['decimal_places'];
    }

    public function display_price($products_price, $products_tax, $quantity = 1) {
      return $this->format($this->calculate_price($products_price, $products_tax, $quantity));
    }

    public function format_raw($number, $calculate_currency_value = true, $currency_type = null, $currency_value = null) {
      if (empty($currency_type)) {
        $currency_type = $_SESSION['currency'] ?? DEFAULT_CURRENCY;
      }

      if ($calculate_currency_value) {
        $number *= ($currency_value ?? $this->currencies[$currency_type]['value']);
      }

      return number_format(tep_round($number, $this->currencies[$currency_type]['decimal_places']), $this->currencies[$currency_type]['decimal_places'], '.', '');
    }

    public function display_raw($products_price, $products_tax, $quantity = 1) {
      return $this->format_raw($this->calculate_price($products_price, $products_tax, $quantity));
    }

    public function set_currency() {
      if (!isset($_SESSION['currency']) || isset($_GET['currency']) || ( (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && (LANGUAGE_CURRENCY != $_SESSION['currency']) ) ) {
        if (isset($_GET['currency']) && $GLOBALS['currencies']->is_set($_GET['currency'])) {
          $_SESSION['currency'] = $_GET['currency'];
        } else {
          $_SESSION['currency'] = ((USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && $GLOBALS['currencies']->is_set(LANGUAGE_CURRENCY)) ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
        }

        $GLOBALS['currency'] =& $_SESSION['currency'];
      }
    }

  }
