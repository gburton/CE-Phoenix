<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
*/

  class cd_address_book_id extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_ADDRESS_BOOK_ID_';

    const PROVIDES = [ 'address_book_id' ];
    const REQUIRES = [  ];

    private $active_fields;

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Address Book ID module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
      ];
    }

    public function get($field, &$customer_details) {
      switch ($field) {
        case 'address_book_id':
          return ($customer_details[$field] ?? null);
      }
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'address_book');
      $db_tables['address_book']['address_book_id'] = $customer_details['address_book_id'];
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'address_book');
      $db_tables['address_book']['address_book_id'] = null;
    }

  }
