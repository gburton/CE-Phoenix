<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  class cd_password extends abstract_customer_data_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_PASSWORD_';

    const PROVIDES = [ 'password' ];
    const REQUIRES = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Password module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'GROUP' => [
          'title' => 'Customer data group',
          'value' => '6',
          'desc' => 'In what group should this appear?',
          'use_func' => 'tep_get_customer_data_group_title',
          'set_func' => 'tep_cfg_pull_down_customer_data_groups(',
        ],
        static::CONFIG_KEY_BASE . 'REQUIRED' => [
          'title' => 'Require Password module (if enabled)',
          'value' => 'True',
          'desc' => 'Do you want the password to be required in customer registration?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'MIN_LENGTH' => [
          'title' => 'Minimum Length',
          'value' => '5',
          'desc' => 'Minimum length of password',
        ],
        static::CONFIG_KEY_BASE . 'PAGES' => [
          'title' => 'Pages',
          'value' => 'account_password;create_account;customers',
          'desc' => 'On what pages should this appear?',
          'set_func' => 'tep_draw_account_edit_pages(',
          'use_func' => 'abstract_module::list_exploded',
        ],
        static::CONFIG_KEY_BASE . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '6200',
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
        case 'password':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $customer_details['password']
              ?? $customer_details['customers_password'] ?? null;
          }
          return $customer_details[$field];
      }
    }

    public function display_input($customer_details = null, $entry_base = 'ENTRY_PASSWORD') {
      $label_text = constant($entry_base);

      $input_id = 'inputPassword';
      $attribute = 'id="' . $input_id . '" autocapitalize="none" placeholder="' . constant($entry_base . '_TEXT') . '"';
      $postInput = '';
      if ($this->is_required()) {
        $attribute = self::REQUIRED_ATTRIBUTE . $attribute;
        $postInput = FORM_REQUIRED_INPUT;
      }

      if (isset($customer_details['password'])) {
        $attribute .= ' autocomplete="current-password"';
      } else {
        $attribute .= ' autocomplete="new-password"';
      }

      $input = tep_draw_input_field('password', null, $attribute, 'password')
             . $postInput;

      include $GLOBALS['oscTemplate']->map_to_template($this->base_constant('TEMPLATE'));
    }

    public function process(&$customer_details, $entry_base = 'ENTRY_PASSWORD') {
      $customer_details['password'] = tep_db_prepare_input($_POST['password']);

      if (strlen($customer_details['password']) < $this->base_constant('MIN_LENGTH')
        && ($this->is_required()
          || !empty($customer_details['password'])
          )
        )
      {
        $GLOBALS['messageStack']->add_classed(
          $GLOBALS['message_stack_area'] ?? 'customer_data',
          sprintf(constant($entry_base . '_ERROR'), $this->base_constant('MIN_LENGTH')));

        return false;
      }

      return true;
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      if (empty($customer_details['password'])) {
        return;
      }

      tep_guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_password'] = tep_encrypt_password($customer_details['password']);
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_password'] = 'password';
    }

  }
