<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
*/

  class cd_traditional_address extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_TRADITIONAL_ADDRESS_';

    const PROVIDES = [ 'address' ];
    const REQUIRES = [ 'name', 'street_address', 'postcode', 'city', 'country' ];
    const FIELDS = [ 'name', 'street_address', 'postcode', 'city', 'country_id', 'company', 'suburb', 'state' ];

    private $active_fields;

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Traditional Address module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
      ];
    }

    public function get($field, &$customer_details) {
      switch ($field) {
        case 'address':
          if (!isset($customer_details[$field])) {
            $customer_details[$field] = array_combine(
              $this->get_fields(),
              array_map(
                function ($v) use (&$customer_details) {
                  return $GLOBALS['customer_data']->get($v, $customer_details);
                },
                $this->get_fields()
              )
            );
          }

          return $customer_details[$field];
      }
    }

    public function process(&$customer_details) {
      $results = $GLOBALS['customer_data']->process($this->get_fields());
      $customer_details = array_merge($customer_details, $results);

      return !empty($results);
    }

    public function get_fields() {
      if (is_null($this->active_fields)) {
        global $customer_data;

        $customer_data->has(self::FIELDS);
        $this->active_fields = array_diff(self::FIELDS, $customer_data->get_last_missing_abilities());
      }

      return $this->active_fields;
    }

    public function get_purveyors() {
      return array_map([$GLOBALS['customer_data'], 'get_module'], $this->get_fields());
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      foreach ($this->get_purveyors() as $purveyor) {
        $purveyor->build_db_values($db_tables, $customer_details, $table);
      }
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      foreach ($this->get_purveyors() as $purveyor) {
        $purveyor->build_db_aliases($db_tables, $table);
      }
    }

    function format($address, $html, $boln, $eoln) {
      $address_format_id = $address['format_id'] ?? $address['address_format_id'] ?? $this->get_address_format_id($address['country_id'] ?? $address['country']['id'] ?? null);
      $address_format_query = tep_db_query("SELECT address_format AS format FROM address_format WHERE address_format_id = " . (int)$address_format_id);
      $address_format = tep_db_fetch_array($address_format_query);

      $company = htmlspecialchars($address['company'] ?? '');
      $name = htmlspecialchars($GLOBALS['customer_data']->get('name', $address) ?? '');

      $street = htmlspecialchars($address['street_address']);
      $suburb = htmlspecialchars($address['suburb'] ?? '');
      $city = htmlspecialchars($address['city']);
      $state = htmlspecialchars($address['state'] ?? '');
      if (!empty($address['country_id'])) {
        $country = tep_get_country_name($address['country_id']);

        if (!empty($address['zone_id'])) {
          $state = tep_get_zone_code($address['country_id'], $address['zone_id'], $state);
        }
      } elseif (!empty($address['country']) && is_array($address['country'])) {
        $country = htmlspecialchars($address['country']['title']);
      } else {
        $country = '';
      }
      $postcode = htmlspecialchars($address['postcode']);
      $zip = $postcode;

      if ($html) {
        // HTML Mode
        $HR = '<hr />';
        $hr = '<hr />';
        if ( ($boln == '') && ($eoln == "\n") ) { // Values not specified, use rational defaults
          $CR = '<br>';
          $cr = '<br>';
          $eoln = $cr;
        } else { // Use values supplied
          $CR = $eoln . $boln;
          $cr = $CR;
        }
      } else {
        // Text Mode
        $CR = $eoln;
        $cr = $CR;
        $HR = '----------------------------------------';
        $hr = '----------------------------------------';
      }

      $statecomma = '';
      $streets = $street;
      if ('' != $suburb) {
        $streets .= $cr . $suburb;
      }
      if ('' != $state) {
        $statecomma = $state . ', ';
      }

      $fmt = $address_format['format'];
      eval("\$address = \"$fmt\";");

      if (!empty($company)) {
        $address = $company . $cr . $address;
      }

      return $address;
    }

    function get_address_format_id($country_id) {
      $address_format_query = tep_db_query("SELECT address_format_id AS format_id FROM countries WHERE countries_id = " . (int)$country_id);
      $address_format = tep_db_fetch_array($address_format_query);

      return $address_format['format_id'] ?? '1';
    }

  }
