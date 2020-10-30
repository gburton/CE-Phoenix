<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  class cd_state extends abstract_customer_data_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_STATE_';

    const PROVIDES = [ 'state', 'entry_state', 'zone_id' ];
    const REQUIRES = [ 'country_id' ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable State module',
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
          'title' => 'Require State module (if enabled)',
          'value' => 'True',
          'desc' => 'Do you want the state to be required in customer registration?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'ENTRY_STATE_MIN_LENGTH' => [
          'title' => 'Minimum Length',
          'value' => '2',
          'desc' => 'Minimum length of state',
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
          'value' => '4600',
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
        case 'entry_state':
        case 'state':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $customer_details['state']
              ?? $customer_details['entry_state'] ?? null;
          }

          if (!$customer_details[$field]) {
            $customer_details[$field] = tep_get_zone_name($GLOBALS['customer_data']->get('country_id', $customer_details), $this->get('zone_id', $customer_details), null);
          }

          return $customer_details[$field];
        case 'zone_id':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = $customer_details['zone_id']
              ?? $customer_details['entry_zone_id'] ?? null;
          }
          return $customer_details[$field];
      }
    }

    public function display_input(&$customer_details = null) {
      $label_text = ENTRY_STATE;

      $input_id = 'inputState';
      $attribute = 'id="' . $input_id . '" autocomplete="address-level1"';

      $postInput = '';
      if ($this->is_required()) {
        $attribute = self::REQUIRED_ATTRIBUTE . $attribute;
        $postInput = FORM_REQUIRED_INPUT;
      }

      $state = null;
      $zones = null;
      if (isset($customer_details) && is_array($customer_details)) {
        $state = $this->get('state', $customer_details);
        $country_id = $GLOBALS['customer_data']->get('country_id', $customer_details);

        if ((int)$country_id > 0) {
          $zones = [];
          $zones_query = tep_db_query("SELECT zone_name FROM zones WHERE zone_country_id = " . (int)$country_id . " ORDER BY zone_name");
          while ($zones_values = tep_db_fetch_array($zones_query)) {
            $zones[] = ['id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']];
          }
        }
      }

      if (empty($zones)) {
        if (tep_not_null(ENTRY_STATE_TEXT)) {
          $attribute .= ' placeholder="' . ENTRY_STATE_TEXT . '"';
        }

        $input = tep_draw_input_field('state', $state, $attribute);
      } else {
        if (tep_not_null(ENTRY_STATE_TEXT)) {
          $attribute .= ' aria-describedby="atState"';
          $postInput .= '<span id="atState" class="form-text">' . ENTRY_STATE_TEXT . '</span>';
        }

        $input = tep_draw_pull_down_menu('state', $zones, $state, $attribute);
      }
      $input .= $postInput;

      include $GLOBALS['oscTemplate']->map_to_template($this->base_constant('TEMPLATE'));
    }

    public function fetch_zone_count($country_id) {
      static $check;

      if (!isset($check)) {
        $check_query = tep_db_query("SELECT COUNT(*) AS total FROM zones WHERE zone_country_id = " . (int)$country_id);
        $check = tep_db_fetch_array($check_query);
      }

      return $check['total'];
    }

    public function process(&$customer_details) {
      $customer_details['state'] = tep_db_prepare_input($_POST['state']);
      if (isset($_POST['zone_id'])) {
        $customer_details['zone_id'] = tep_db_prepare_input($_POST['zone_id']);
      } else {
        $customer_details['zone_id'] = false;
      }

      $customer_details['entry_state'] = $customer_details['state'];

      $country_id = $GLOBALS['customer_data']->get('country_id', $customer_details);
      if ((int)$country_id > 0 && $this->fetch_zone_count($country_id) > 0) {
        $zone_query = tep_db_query("SELECT DISTINCT zone_id FROM zones WHERE zone_country_id = " . (int)$country_id
          . " AND (zone_name = '" . tep_db_input($customer_details['state']) . "' OR zone_code = '" . tep_db_input($customer_details['state']) . "')");
        if (tep_db_num_rows($zone_query) === 1) {
          $zone = tep_db_fetch_array($zone_query);
          $customer_details['zone_id'] = (int)$zone['zone_id'];
          $customer_details['entry_state'] = '';
        } else {
          $GLOBALS['messageStack']->add_classed($GLOBALS['message_stack_area'] ?? 'customer_data', ENTRY_STATE_ERROR_SELECT);

          return false;
        }
      } elseif ($this->is_required() && (strlen($customer_details['state']) < ENTRY_STATE_MIN_LENGTH)) {
        $GLOBALS['messageStack']->add_classed(
          $GLOBALS['message_stack_area'] ?? 'customer_data',
          sprintf(ENTRY_STATE_ERROR, ENTRY_STATE_MIN_LENGTH));

        return false;
      }

      return true;
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'address_book');
      $db_tables['address_book']['entry_state'] = $customer_details['entry_state'];
      $db_tables['address_book']['entry_zone_id'] = $customer_details['zone_id'];
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      tep_guarantee_subarray($db_tables, 'address_book');
      $db_tables['address_book']['entry_state'] = 'state';
      $db_tables['address_book']['entry_zone_id'] = 'zone_id';
    }

  }
