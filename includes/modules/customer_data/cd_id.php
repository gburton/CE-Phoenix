<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
*/

  class cd_id extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_ID_';

    const PROVIDES = [ 'id' ];
    const REQUIRES = [  ];
    const OFFERS = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Identifier module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], "
        ],
      ];
    }

    public function get($field, &$customer_details) {
      switch ($field) {
        case 'id':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $customer_details['id']
              ?? $customer_details['customers_id']
              ?? $customer_details['customers_info_id']
              ?? $customer_details['customer_id'] ?? null;
          }

          return $customer_details[$field];
      }
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      if ('both' === $table) {
        $table = 'customers';
      }

      tep_guarantee_subarray($db_tables, $table);
      $db_tables[$table]['customers_id'] = $this->get('id', $customer_details);
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      if ('both' === $table) {
        $table = 'customers';
      }

      tep_guarantee_subarray($db_tables, $table);
      $db_tables[$table]['customers_id'] = 'id';
    }

    public function add_order_by(&$columns, $criterion, $direction) {
      tep_guarantee_subarray($columns, 'customers');
      $columns['customers']['customers_id'] = $direction;
    }

  }
