<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  class cd_email_address extends abstract_customer_data_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_EMAIL_ADDRESS_';

    const PROVIDES = [ 'email_address' ];
    const REQUIRES = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Email Address module',
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
          'title' => 'Require Email Address module (if enabled)',
          'value' => 'True',
          'desc' => 'Do you want the email address to be required in customer registration?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'ENTRY_EMAIL_ADDRESS_MIN_LENGTH' => [
          'title' => 'Minimum Length',
          'value' => '6',
          'desc' => 'Minimum length of email address',
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
          'value' => '2100',
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
        case 'email_address':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $customer_details['email_address']
              ?? $customer_details['customers_email_address'] ?? null;
          }
          return $customer_details[$field];
      }
    }

    public function display_input($customer_details = null) {
      $label_text = ENTRY_EMAIL_ADDRESS;

      $input_id = 'inputEmail';
      $attribute = 'id="' . $input_id . '" autocomplete="username email" placeholder="' . ENTRY_EMAIL_ADDRESS_TEXT . '"';
      $postInput = '';
      if ($this->is_required()) {
        $attribute = self::REQUIRED_ATTRIBUTE . $attribute;
        $postInput = FORM_REQUIRED_INPUT;
      }

      $email_address = null;
      if (isset($customer_details) && is_array($customer_details)) {
        $email_address = $this->get('email_address', $customer_details);
      }

      $input = tep_draw_input_field('email_address', $email_address, $attribute, 'email')
             . $postInput;

      include $GLOBALS['oscTemplate']->map_to_template($this->base_constant('TEMPLATE'));
    }

    public function process(&$customer_details) {
      $customer_details['email_address'] = tep_db_prepare_input($_POST['email_address']);

      if (($this->is_required() || !empty($customer_details['email_address']))
        && (strlen($customer_details['email_address']) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH)
        )
      {
        $GLOBALS['messageStack']->add_classed(
          $GLOBALS['message_stack_area'] ?? 'customer_data',
          sprintf(ENTRY_EMAIL_ADDRESS_ERROR, ENTRY_EMAIL_ADDRESS_MIN_LENGTH));

        return false;
      } elseif (!self::validate($customer_details['email_address'])) {
        $GLOBALS['messageStack']->add_classed($GLOBALS['message_stack_area'] ?? 'customer_data', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);

        return false;
      } else {
        $check_email_sql = "SELECT COUNT(*) AS total FROM customers WHERE customers_email_address = '" . tep_db_input($customer_details['email_address']) . "'";
        if (isset($_SESSION['customer_id']) || isset($customer_details['id'])) {
          $check_email_sql .= " AND customers_id != " . (int)($_SESSION['customer_id'] ?? $customer_details['id']);
        }

        $check_email_query = tep_db_query($check_email_sql);
        $check_email = tep_db_fetch_array($check_email_query);
        if ($check_email['total'] > 0) {
          $GLOBALS['messageStack']->add_classed($GLOBALS['message_stack_area'] ?? 'customer_data', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);

          return false;
        }
      }

      return true;
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_email_address'] = $customer_details['email_address'];
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_email_address'] = 'email_address';
    }

    public static function validate($email) {
      $email = trim($email);

      if ( ( strlen($email) > 255 ) || ( false === filter_var($email, FILTER_VALIDATE_EMAIL) ) ) {
        return false;
      }

      if (ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
        $domain = explode('@', $email);

        if ( !checkdnsrr($domain[1], "MX") && !checkdnsrr($domain[1], "A") ) {
          return false;
        }
      }

      return true;
    }

    public function is_searchable() {
      return true;
    }

  }
