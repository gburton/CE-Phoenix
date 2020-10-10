<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  class cd_newsletter extends abstract_customer_data_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_NEWSLETTER_';

    const PROVIDES = [ 'newsletter' ];
    const REQUIRES = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Newsletter Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'GROUP' => [
          'title' => 'Customer data group',
          'value' => '3',
          'desc' => 'In what group should this appear?',
          'use_func' => 'tep_get_customer_data_group_title',
          'set_func' => 'tep_cfg_pull_down_customer_data_groups(',
        ],
        static::CONFIG_KEY_BASE . 'PAGES' => [
          'title' => 'Pages',
          'value' => 'account_newsletters;create_account;customers',
          'desc' => 'On what pages should this appear?',
          'set_func' => 'tep_draw_account_edit_pages(',
          'use_func' => 'abstract_module::list_exploded',
        ],
        static::CONFIG_KEY_BASE . 'REQUIRED' => [
          'title' => 'Require Newsletter (if enabled)',
          'value' => 'False',
          'desc' => 'Do you want the newsletter to be required in customer registration?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '5800',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

    public function get($field, &$customer_details) {
      switch ($field) {
        case 'newsletter':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $customer_details['newsletter']
                                     ?? $customer_details['customers_newsletter'] ?? null;
          }

          return $customer_details[$field];
      }
    }

    public function display_input(&$customer_details = null) {
      $attribute = '';
      if ($this->is_required()) {
        $attribute = self::REQUIRED_ATTRIBUTE;
      }

      $newsletter = false;
      if (!empty($customer_details) && is_array($customer_details)) {
        $newsletter = $this->get('newsletter', $customer_details);
      }

      include $GLOBALS['oscTemplate']->map_to_template(__FILE__);
    }

    public function process(&$customer_details) {
      $customer_details['newsletter'] = isset($_POST['newsletter']) ? tep_db_prepare_input($_POST['newsletter']) : false;

      if ( ( ('1' !== $customer_details['newsletter']) )
        && (!empty($customer_details['newsletter']) || $this->is_required())
         )
      {
        $GLOBALS['messageStack']->add_classed($GLOBALS['message_stack_area'] ?? 'customer_data', ENTRY_NEWSLETTER_ERROR);

        return false;
      }

      return true;
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_newsletter'] = $customer_details['newsletter'];
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_newsletter'] = 'newsletter';
    }

    public function get_template() {
      return substr(__FILE__, strlen(DIR_FS_CATALOG));
    }

  }
