<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  class cd_gender extends abstract_customer_data_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_GENDER_';

    const PROVIDES = [ 'gender' ];
    const REQUIRES = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Gender Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'GROUP' => [
          'title' => 'Customer data group',
          'value' => '0',
          'desc' => 'In what group should this appear?',
          'use_func' => 'tep_get_customer_data_group_title',
          'set_func' => 'tep_cfg_pull_down_customer_data_groups(',
        ],
        static::CONFIG_KEY_BASE . 'REQUIRED' => [
          'title' => 'Require Gender module (if enabled)',
          'value' => 'False',
          'desc' => 'Do you want the gender to be required in customer registration?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'PAGES' => [
          'title' => 'Pages',
          'value' => 'account_edit;address_book;checkout_new_address;create_account;customers',
          'desc' => 'On what pages should this appear?',
          'set_func' => 'tep_draw_account_edit_pages(',
          'use_func' => 'abstract_module::list_exploded',
        ],
        static::CONFIG_KEY_BASE . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '2010',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

    public function get($field, &$customer_details) {
      switch ($field) {
        case 'gender':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $customer_details['gender']
                                     ?? $customer_details['customers_gender']
                                     ?? $customer_details['entry_gender'] ?? null;
          }

          return $customer_details[$field];
      }
    }

    public function display_input($customer_details = null) {
      $gender = null;
      if (!empty($customer_details) && is_array($customer_details)) {
        $gender = $this->get('gender', $customer_details);
      }

      include $GLOBALS['oscTemplate']->map_to_template(__FILE__);
    }

    public function process(&$customer_details) {
      $customer_details['gender'] = isset($_POST['gender']) ? tep_db_prepare_input($_POST['gender']) : false;

      if ( ( ('m' !== $customer_details['gender']) && ('f' !== $customer_details['gender']) )
        && (!empty($customer_details['gender']) || $this->is_required())
         )
      {
        $GLOBALS['messageStack']->add_classed($GLOBALS['message_stack_area'] ?? 'customer_data', ENTRY_GENDER_ERROR);

        return false;
      }

      return true;
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      if ('both' == $table || 'customers' == $table) {
        tep_guarantee_subarray($db_tables, 'customers');
        $db_tables['customers']['customers_gender'] = $customer_details['gender'];
      }

      if ('both' == $table || 'address_book' == $table) {
        tep_guarantee_subarray($db_tables, 'address_book');
        $db_tables['address_book']['entry_gender'] = $customer_details['gender'];
      }
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      if ('both' == $table || 'customers' == $table) {
        tep_guarantee_subarray($db_tables, 'customers');
        $db_tables['customers']['customers_gender'] = 'gender';
      }

      if ('both' == $table || 'address_book' == $table) {
        tep_guarantee_subarray($db_tables, 'address_book');
        $db_tables['address_book']['entry_gender'] = (isset($db_tables['customers']['customers_gender'])) ? null : 'gender';
      }
    }

    public function get_template() {
      return substr(__FILE__, strlen(DIR_FS_CATALOG));
    }

  }
