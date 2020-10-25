<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  class cd_password_confirmation extends abstract_customer_data_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_PASSWORD_CONFIRMATION_';

    const PROVIDES = [ 'password_confirmation' ];
    const REQUIRES = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Password Confirmation module',
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
        static::CONFIG_KEY_BASE . 'PAGES' => [
          'title' => 'Pages',
          'value' => 'account_password;create_account;customers',
          'desc' => 'On what pages should this appear?',
          'set_func' => 'tep_draw_account_edit_pages(',
          'use_func' => 'abstract_module::list_exploded',
        ],
        static::CONFIG_KEY_BASE . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '6300',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
        static::CONFIG_KEY_BASE . 'TEMPLATE' => [
          'title' => 'Template',
          'value' => 'includes/modules/customer_data/cd_whole_row_input.php',
          'desc' => 'What template should be used to surround this input?',
        ],
      ];
    }

    public function display_input($customer_details = null) {
      $label_text = ENTRY_PASSWORD_CONFIRMATION;

      $input_id = 'inputPassword';
      if ('customers.php' === $GLOBALS['PHP_SELF']) {
        $attribute = 'id="' . $input_id . '" autocapitalize="none"';
        $post_input = '';
      } else {
        $attribute = self::REQUIRED_ATTRIBUTE . 'id="' . $input_id
                   . '" autocapitalize="none" autocomplete="new-password" placeholder="' . ENTRY_PASSWORD_CONFIRMATION_TEXT . '"';
        $post_input = FORM_REQUIRED_INPUT;
      }

      $input = tep_draw_input_field('password_confirmation', null, $attribute, 'password')
             . $post_input;

      include $GLOBALS['oscTemplate']->map_to_template($this->base_constant('TEMPLATE'));
    }

    public function process(&$customer_details, $entry_base = 'ENTRY_PASSWORD') {
      $customer_details['password_confirmation'] = tep_db_prepare_input($_POST['password_confirmation']);

      if ($customer_details['password_confirmation'] !== $GLOBALS['customer_data']->get('password', $customer_details)) {
        $GLOBALS['messageStack']->add_classed(
          $GLOBALS['message_stack_area'] ?? 'customer_data',
          constant($entry_base . '_ERROR_NOT_MATCHING'));

        return false;
      }

      return true;
    }

  }
