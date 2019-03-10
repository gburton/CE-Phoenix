<?php
/*
  Copyright (c) 2019, G Burton
  All rights reserved. 

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

  class c_ecb {
    var $code = 'c_ecb';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_ADMIN_CURRENCIES_ECB_TITLE;
      $this->description = MODULE_ADMIN_CURRENCIES_ECB_DESCRIPTION;

      if ( defined('MODULE_ADMIN_CURRENCIES_ECB_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_CURRENCIES_ECB_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_CURRENCIES_ECB_STATUS == 'True');
      }
    }

    static function execute() {
      global $messageStack;
      
      $xml = simplexml_load_file('https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
      $xml = json_decode(json_encode($xml), JSON_FORCE_OBJECT);
      
      $currency_query = tep_db_query("select currencies_id, code, title from currencies");
      while ($currency = tep_db_fetch_array($currency_query)) {
        $to[$currency['code']] = $currency['code'];
      }

      $from = DEFAULT_CURRENCY;
      
      $ecb_currencies = array('EUR' => 1);
      foreach ($xml as $a) {
        foreach ($a['Cube']['Cube'] as $b) {    
          $ecb_currencies[$b['@attributes']['currency']] = $b['@attributes']['rate'];
        }
      }

      if ($from != 'EUR') {
        $exchange = $ecb_currencies[$from];
        foreach ($ecb_currencies as $x => $y) {
          $ecb_currencies[$x] = $y/$exchange;
        }
      }
      
      $to_exchange = array_intersect_key($ecb_currencies, $to);
      
      foreach($to_exchange as $k => $v) {
        $rate = tep_db_prepare_input($v);
        tep_db_query("update currencies set value = '" . tep_db_input($rate) . "', last_updated = now() where code = '" . $k . "'");
        
        $messageStack->add_session(sprintf(MODULE_ADMIN_CURRENCIES_ECB_CURRENCIES_UPDATED, $k), 'success');
      }

    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_CURRENCIES_ECB_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable ECB Module', 'MODULE_ADMIN_CURRENCIES_ECB_STATUS', 'True', 'Do you want to install this Currency Conversion Module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_CURRENCIES_ECB_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_ADMIN_CURRENCIES_ECB_STATUS', 'MODULE_ADMIN_CURRENCIES_ECB_SORT_ORDER');
    }
  }
  