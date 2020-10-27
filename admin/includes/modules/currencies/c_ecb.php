<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class c_ecb extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_ADMIN_CURRENCIES_ECB_';

    public static function execute() {
      $xml = Web::load_xml('https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
      $xml = json_decode(json_encode($xml), JSON_FORCE_OBJECT);

      $currency_query = tep_db_query("SELECT currencies_id, code, title FROM currencies");
      while ($currency = tep_db_fetch_array($currency_query)) {
        $to[$currency['code']] = $currency['code'];
      }

      $from = DEFAULT_CURRENCY;

      $ecb_currencies = ['EUR' => 1.0];
      foreach ($xml as $a) {
        foreach ($a['Cube']['Cube'] as $b) {
          $ecb_currencies[$b['@attributes']['currency']] = $b['@attributes']['rate'];
        }
      }

      if ($from !== 'EUR') {
        $exchange = $ecb_currencies[$from];
        foreach ($ecb_currencies as $x => $y) {
          $ecb_currencies[$x] = $y/$exchange;
        }
      }

      $to_exchange = array_intersect_key($ecb_currencies, $to);

      foreach ($to_exchange as $k => $v) {
        $rate = tep_db_prepare_input($v);
        tep_db_query("UPDATE currencies SET value = '" . tep_db_input($rate) . "', last_updated = NOW() WHERE code = '" . tep_db_input($k) . "'");

        $GLOBALS['messageStack']->add_session(sprintf(MODULE_ADMIN_CURRENCIES_ECB_CURRENCIES_UPDATED, $k), 'success');
      }

    }

    protected function get_parameters() {
      return [
        'MODULE_ADMIN_CURRENCIES_ECB_STATUS' => [
          'title' => 'Enable ECB Module',
          'value' => 'True',
          'desc' => 'Do you want to install this Currency Conversion Module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_ADMIN_CURRENCIES_ECB_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
