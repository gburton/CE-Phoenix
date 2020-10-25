<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  class cd_street_address extends abstract_customer_data_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_STREET_ADDRESS_';

    const PROVIDES = [ 'street_address' ];
    const REQUIRES = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Street Address module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'GROUP' => [
          'title' => 'Customer data group',
          'value' => '2',
          'desc' => 'In what group should this appear?',
          'use_func' => 'tep_get_customer_data_group_title',
          'set_func' => 'tep_cfg_pull_down_customer_data_groups(',
        ],
        static::CONFIG_KEY_BASE . 'REQUIRED' => [
          'title' => 'Require Street Address module (if enabled)',
          'value' => 'True',
          'desc' => 'Do you want the street address to be required in customer registration?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'MIN_LENGTH' => [
          'title' => 'Minimum Length',
          'value' => '3',
          'desc' => 'Minimum length of street address',
        ],
        static::CONFIG_KEY_BASE . 'PAGES' => [
          'title' => 'Pages',
          'value' => 'address_book;checkout_new_address;create_account;customers',
          'desc' => 'On what pages should this appear?',
          'set_func' => 'tep_draw_account_edit_pages(',
          'use_func' => 'abstract_module::list_exploded',
        ],
        static::CONFIG_KEY_BASE . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '4200',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
        static::CONFIG_KEY_BASE . 'TEMPLATE' => [
          'title' => 'Template',
          'value' => 'includes/modules/customer_data/cd_whole_row_input.php',
          'desc' => 'What template should be used to surround this input?',
        ],
      ];
    }

    public function get($field, &$customer_details) {
      switch ($field) {
        case 'street_address':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $customer_details['street_address']
              ?? $customer_details['entry_street_address'] ?? null;
          }
          return $customer_details[$field];
      }
    }

    public function display_input($customer_details = null) {
      $label_text = ENTRY_STREET_ADDRESS;

      $input_id = 'inputStreetAddress';
      $attribute = 'id="' . $input_id . '" autocomplete="address-line1" placeholder="' . ENTRY_STREET_ADDRESS_TEXT . '"';
      $postInput = '';
      if ($this->is_required()) {
        $attribute = self::REQUIRED_ATTRIBUTE . $attribute;
        $postInput = FORM_REQUIRED_INPUT;
      }

      $street_address = null;
      if (!empty($customer_details) && is_array($customer_details)) {
        $street_address = $this->get('street_address', $customer_details);
      }

      $input = tep_draw_input_field('street_address', $street_address, $attribute)
             . $postInput;

      include $GLOBALS['oscTemplate']->map_to_template($this->base_constant('TEMPLATE'));
    }

    public function process(&$customer_details) {
      $customer_details['street_address'] = tep_db_prepare_input($_POST['street_address']);

      if ((strlen($customer_details['street_address']) < $this->base_constant('MIN_LENGTH'))
        && $this->is_required()
        )
      {
        $GLOBALS['messageStack']->add_classed(
          $GLOBALS['message_stack_area'] ?? 'customer_data',
          sprintf(ENTRY_STREET_ADDRESS_ERROR, $this->base_constant('MIN_LENGTH')));

        return false;
      }

      return true;
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'address_book');
      $db_tables['address_book']['entry_street_address'] = $customer_details['street_address'];
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'address_book');
      $db_tables['address_book']['entry_street_address'] = 'street_address';
    }

  }
