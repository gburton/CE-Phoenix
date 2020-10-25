<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  class cd_postcode extends abstract_customer_data_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_POST_CODE_';

    const PROVIDES = [ 'postcode' ];
    const REQUIRES = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Post Code module',
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
          'title' => 'Require Post Code module (if enabled)',
          'value' => 'True',
          'desc' => 'Do you want the post code to be required in customer registration?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'MIN_LENGTH' => [
          'title' => 'Minimum Length',
          'value' => '3',
          'desc' => 'Minimum length of post code',
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
          'value' => '4800',
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
        case 'postcode':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $customer_details['postcode']
              ?? $customer_details['entry_postcode'] ?? null;
          }
          return $customer_details[$field];
      }
    }

    public function display_input($customer_details = null) {
      $label_text = ENTRY_POST_CODE;

      $input_id = 'inputPostCode';
      $attribute = 'id="' . $input_id . '" autocomplete="postal-code" placeholder="' . ENTRY_POST_CODE_TEXT . '"';
      $postInput = '';
      if ($this->is_required()) {
        $attribute = self::REQUIRED_ATTRIBUTE . $attribute;
        $postInput = FORM_REQUIRED_INPUT;
      }

      $postcode = null;
      if (isset($customer_details) && is_array($customer_details)) {
        $postcode = $this->get('postcode', $customer_details);
      }

      $input = tep_draw_input_field('postcode', $postcode, $attribute)
             . $postInput;

      include $GLOBALS['oscTemplate']->map_to_template($this->base_constant('TEMPLATE'));
    }

    public function process(&$customer_details) {
      $customer_details['postcode'] = tep_db_prepare_input($_POST['postcode']);

      if ($this->is_required() && (strlen($customer_details['postcode']) < $this->base_constant('MIN_LENGTH'))) {
        $GLOBALS['messageStack']->add_classed(
          $GLOBALS['message_stack_area'] ?? 'customer_data',
          sprintf(ENTRY_POST_CODE_ERROR, $this->base_constant('MIN_LENGTH')));

        return false;
      }

      return true;
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'address_book');
      $db_tables['address_book']['entry_postcode'] = $customer_details['postcode'];
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'address_book');
      $db_tables['address_book']['entry_postcode'] = 'postcode';
    }

  }
