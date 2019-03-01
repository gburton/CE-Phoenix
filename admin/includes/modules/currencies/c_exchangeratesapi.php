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

  class c_exchangeratesapi {
    var $code = 'c_exchangeratesapi';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_ADMIN_CURRENCIES_EXCHANGERATESAPI_TITLE;
      $this->description = MODULE_ADMIN_CURRENCIES_EXCHANGERATESAPI_DESCRIPTION;

      if ( defined('MODULE_ADMIN_CURRENCIES_EXCHANGERATESAPI_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_CURRENCIES_EXCHANGERATESAPI_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_CURRENCIES_EXCHANGERATESAPI_STATUS == 'True');
      }
    }

    static function execute() {
      global $messageStack;
      
      $currency_query = tep_db_query("select currencies_id, code, title from currencies where code != '" . DEFAULT_CURRENCY . "'");
      while ($currency = tep_db_fetch_array($currency_query)) {
        $to[] = $currency['code'];
      }
      
      $from = DEFAULT_CURRENCY;
      $to   = implode(',', $to);
      
      $ce_data = array('base'    => $from,
                       'symbols' => $to);
    
      $ce_data_query = http_build_query($ce_data);
      
      $ch = curl_init('https://api.exchangeratesapi.io/latest?' . $ce_data_query);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $data = curl_exec($ch); 
      curl_close($ch);

      $ce_currencies = json_decode($data, true);
      
      if (isset($ce_currencies['error'])) {
        // not multi-lang capable :(
        $error = htmlspecialchars($ce_currencies['error']);
        
        $messageStack->add_session($error, 'error');
      }
      else {
        foreach($ce_currencies['rates'] as $k => $v) {
          tep_db_query("update currencies set value = '" . $v . "', last_updated = now() where code = '" . $k . "'");
          
          $messageStack->add_session(sprintf(MODULE_ADMIN_CURRENCIES_EXCHANGERATESAPI_CURRENCIES_UPDATED, $k), 'success');
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_CURRENCIES_EXCHANGERATESAPI_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Foreign Exchange Rates API Module', 'MODULE_ADMIN_CURRENCIES_EXCHANGERATESAPI_STATUS', 'True', 'Do you want to install this Currency Conversion Module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_CURRENCIES_EXCHANGERATESAPI_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_ADMIN_CURRENCIES_EXCHANGERATESAPI_STATUS', 'MODULE_ADMIN_CURRENCIES_EXCHANGERATESAPI_SORT_ORDER');
    }
  }
  