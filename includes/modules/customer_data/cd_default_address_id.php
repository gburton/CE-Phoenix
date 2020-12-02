<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  class cd_default_address_id extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_DEFAULT_ADDRESS_ID_';

    const PROVIDES = [ 'default_address_id', 'default_billto', 'default_sendto' ];
    const REQUIRES = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Default Address ID module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
      ];
    }

    public function get($field, &$customer_details) {
      switch ($field) {
        case 'default_sendto':
        case 'default_billto':
        case 'default_address_id':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $customer_details['default_address_id']
              ?? $customer_details['customers_default_address_id'] ?? null;
          }

          return $customer_details[$field];
      }
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      Guarantor::guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_default_address_id'] = $this->get('default_address_id', $customer_details);
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      Guarantor::guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_default_address_id'] = 'default_address_id';
    }

  }
