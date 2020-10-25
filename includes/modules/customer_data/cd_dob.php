<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  class cd_dob extends abstract_customer_data_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_DOB_';

    const PROVIDES = [ 'dob' ];
    const REQUIRES = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Date of Birth module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'GROUP' => [
          'title' => 'Customer data group',
          'value' => '1',
          'desc' => 'In what group should this appear?',
          'use_func' => 'tep_get_customer_data_group_title',
          'set_func' => 'tep_cfg_pull_down_customer_data_groups(',
        ],
        static::CONFIG_KEY_BASE . 'REQUIRED' => [
          'title' => 'Require Date of Birth module (if enabled)',
          'value' => 'True',
          'desc' => 'Do you want the date of birth to be required in customer registration?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'MIN_LENGTH' => [
          'title' => 'Minimum Length',
          'value' => '10',
          'desc' => 'Minimum length of date of birth',
        ],
        static::CONFIG_KEY_BASE . 'PAGES' => [
          'title' => 'Pages',
          'value' => 'account_edit;create_account;customers',
          'desc' => 'On what pages should this appear?',
          'set_func' => 'tep_draw_account_edit_pages(',
          'use_func' => 'abstract_module::list_exploded',
        ],
        static::CONFIG_KEY_BASE . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '2200',
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
        case 'dob':
          $customer_details[$field] = $customer_details['dob']
            ?? $customer_details['customers_dob'] ?? null;
          return $customer_details[$field];
      }
    }

    public function display_input($customer_details = null) {
      $label_text = ENTRY_DOB;

      $input_id = 'dob';
      $attribute = 'id="' . $input_id . '" autocomplete="bday" placeholder="' . ENTRY_DOB_TEXT . '"';
      $postInput = '';
      if ($this->is_required()) {
        $attribute = self::REQUIRED_ATTRIBUTE . $attribute;
        $postInput = FORM_REQUIRED_INPUT;
      }

      $dob = null;
      if (isset($customer_details) && is_array($customer_details)) {
        $dob = tep_date_short($this->get('dob', $customer_details));
      }

      $input = tep_draw_input_field('dob', $dob, $attribute)
             . $postInput;

      include $GLOBALS['oscTemplate']->map_to_template($this->base_constant('TEMPLATE'));
    }

    public function is_valid($date) {
      if (empty($date)) {
        return $this->is_required();
      }

      $raw = tep_cd_dob_date_raw($date);
      return ((strlen($date) >= $this->base_constant('MIN_LENGTH'))
        && is_numeric($raw)
        && @checkdate(substr($raw, 4, 2), substr($raw, 6, 2), substr($raw, 0, 4)));
    }

    public function process(&$customer_details) {
      $dob = tep_db_prepare_input($_POST['dob']);

      if (!$this->is_valid($dob)) {
        $GLOBALS['messageStack']->add_classed(
          $GLOBALS['message_stack_area'] ?? 'customer_data',
          sprintf(ENTRY_DOB_ERROR, $this->base_constant('MIN_LENGTH')) . tep_cd_dob_date_raw($customer_details['dob']));

        return false;
      }

      $customer_details['dob'] = tep_cd_dob_date_raw($dob);
      return true;
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_dob'] = $customer_details['dob'];
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_dob'] = 'dob';
    }

  }
