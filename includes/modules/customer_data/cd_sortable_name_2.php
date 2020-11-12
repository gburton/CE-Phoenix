<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
*/

  class cd_sortable_name_2 extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_SORTABLE_NAME_2_';

    const PROVIDES = [ 'sortable_name' ];
    const REQUIRES = [ 'firstname', 'lastname' ];
    const OFFERS = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Two Part Sortable Name module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
      ];
    }

    public function get($field, &$customer_details) {
      switch ($field) {
        case 'sortable_name':
          if (!isset($customer_details[$field])) {
            global $customer_data;
            $customer_details[$field] = $customer_data->get('lastname', $customer_details) . ', '
                                      . $customer_data->get('firstname', $customer_details);
          }

          return $customer_details[$field];
      }
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      global $customer_data;

      foreach ([$customer_data->get_module('firstname'), $customer_data->get_module('lastname')] as $purveyor) {
        $purveyor->build_db_values($db_tables, $customer_details, $table);
      }
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      global $customer_data;

      foreach ([$customer_data->get_module('firstname'), $customer_data->get_module('lastname')] as $purveyor) {
        $purveyor->build_db_aliases($db_tables, $table);
      }
    }

    public function add_order_by(&$columns, $criterion, $direction) {
      tep_guarantee_subarray($columns, 'customers');
      $columns['customers']['customers_lastname'] = $direction;
      $columns['customers']['customers_firstname'] = $direction;
    }

  }
