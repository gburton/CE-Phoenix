<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  class cd_country extends abstract_customer_data_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_COUNTRY_';

    const PROVIDES = [
      'country',
      'country_id',
      'entry_country_id',
      'country_name',
      'country_iso_code_3',
      'country_iso_code_2',
      'format_id',
      'address_format_id',
    ];
    const REQUIRES = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Country module',
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
          'title' => 'Require Country module (if enabled)',
          'value' => 'True',
          'desc' => 'Do you want the country to be required in customer registration?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
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
          'value' => '4900',
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
      if (isset($customer_details[$field])) {
        return $customer_details[$field];
      }

      switch ($field) {
        case 'entry_country_id':
        case 'country_id':
          $customer_details[$field] = $customer_details['country_id']
            ?? $customer_details['country']['id']
            ?? $customer_details['entry_country_id']
            ?? $customer_details['countries_id'] ?? null;
          return $customer_details[$field];
        case 'country_name':
          $customer_details[$field] = $customer_details['country_name']
            ?? $customer_details['country']['name']
            ?? $customer_details['countries_name'] ?? null;
          return $customer_details[$field];
        case 'country_iso_code_3':
          $customer_details[$field] = $customer_details['country']['iso_code_3']
            ?? $customer_details['countries_iso_code_3'] ?? null;
          return $customer_details[$field];
        case 'country_iso_code_2':
          $customer_details[$field] = $customer_details['country']['iso_code_2']
            ?? $customer_details['countries_iso_code_2'] ?? null;
          return $customer_details[$field];
        case 'address_format_id':
        case 'format_id':
          $customer_details[$field] = $customer_details['format_id']
            ?? $customer_details['country']['address_format_id']
            ?? $customer_details['address_format_id'] ?? null;
          return $customer_details[$field];
        case 'country':
          $customer_details[$field] = $customer_details['country'] ?? [
            'id' => $this->get('country_id', $customer_details),
            'name' => $this->get('country_name', $customer_details),
            'title' => $this->get('country_name', $customer_details),
            'iso_code_2' => $this->get('country_iso_code_2', $customer_details),
            'iso_code_3' => $this->get('country_iso_code_3', $customer_details),
            'address_format_id' => $this->get('format_id', $customer_details),
          ];

          if (isset($customer_details['country']['id']) && 6 > count(array_filter($customer_details['country'], function ($v) { return isset($v); }))) {
            $countries_query = tep_db_query("SELECT * FROM countries WHERE countries_id = " . (int)$customer_details['country_id']);
            $country = tep_db_fetch_array($countries_query);
            $customer_details['country'] = $this->get('country', $country);
          }
          return $customer_details[$field];
      }
    }

    public function display_input($customer_details = null) {
      $label_text = ENTRY_COUNTRY;

      $input_id = 'inputCountry';
      $attribute = 'id="' . $input_id . '"';
      $postInput = '';
      if ($this->is_required()) {
        $attribute = self::REQUIRED_ATTRIBUTE . $attribute;
        $postInput = FORM_REQUIRED_INPUT;
      }

      if (tep_not_null(ENTRY_COUNTRY_TEXT)) {
        $attribute .= ' aria-describedby="atCountry"';
        $postInput .= '<span id="atCountry" class="form-text">' . ENTRY_COUNTRY_TEXT . '</span>';
      }

      $country_id = null;
      if (isset($customer_details) && is_array($customer_details)) {
        $country_id = $this->get('country_id', $customer_details);
      }

      $input = $this->draw_country_list('country_id', $country_id, $attribute)
             . $postInput;

      include $GLOBALS['oscTemplate']->map_to_template($this->base_constant('TEMPLATE'));
    }

    public function process(&$customer_details) {
      $customer_details['country_id'] = tep_db_prepare_input($_POST['country_id']);

      if (($this->is_required() || '' !== $customer_details['country_id'])
        && (false === is_numeric($customer_details['country_id']))
        )
      {
        $GLOBALS['messageStack']->add_classed($GLOBALS['message_stack_area'] ?? 'customer_data', ENTRY_COUNTRY_ERROR);

        return false;
      }

      return true;
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      $country_id = $GLOBALS['customer_data']->get('country_id', $customer_details);
      tep_guarantee_subarray($db_tables, 'address_book');
      $db_tables['address_book']['entry_country_id'] = $country_id;
      if ('countries' === $table) {
        tep_guarantee_subarray($db_tables, 'countries');
        $db_tables['countries']['countries_id'] = $country_id;
      }
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'address_book');
      $db_tables['address_book']['entry_country_id'] = 'country_id';
      tep_guarantee_subarray($db_tables, 'countries');
      $db_tables['countries'] = [];
    }

    ////
    // Creates a pull-down list of countries
    public function draw_country_list($name, $selected = '', $parameters = '') {
      $countries = [['id' => '', 'text' => PULL_DOWN_DEFAULT]];
      $countries_query = tep_db_query("SELECT countries_id, countries_name FROM countries");
      while ($country = tep_db_fetch_array($countries_query)) {
        $countries[] = ['id' => $country['countries_id'], 'text' => $country['countries_name']];
      }

      return tep_draw_pull_down_menu($name, $countries, $selected, $parameters);
    }

  }
