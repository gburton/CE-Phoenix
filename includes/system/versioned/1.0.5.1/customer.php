<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class customer {

    private $id;
    private $data = [];
    private $unpersisted = [];

    /**
     * @param string $customer_id
     */
    public function __construct(string $customer_id = '') {
      $this->id = $customer_id;
    }

    protected function guarantee_customer_data() {
      global $customer_data;

      if (!isset($customer_data) || !($customer_data instanceof customer_data)) {
        $customer_data = new customer_data();
      }

      return $customer_data;
    }

    public function preload_columns(&$to) {
      $customer_data = $this->guarantee_customer_data();

      $customer_data->get('state', $to);
      $customer_data->get('zone_id', $to);
      $customer_data->get('country', $to);
      $customer_data->get('name', $to);
    }

    /**
     * @param int $to The ID of the customer's address.  Or 0 to use the customers table.
     * @return array Of the address information.
     */
    protected function fetch_address(int $to = 0) {
      if (isset($this->data[$to])) {
        return;
      }

      $customer_data = $this->guarantee_customer_data();

      if ($to > 0) {
        $address_query = tep_db_query($customer_data->build_read(['id', 'address'], 'address_book', ['id' => (int)$this->id, 'address_book_id' => (int)$to]));
      } else {
        $address_query = tep_db_query($customer_data->build_read($customer_data->list_all_capabilities(), 'both', ['id' => (int)$this->id]));
      }

      $this->data[$to] = array_filter(tep_db_fetch_array($address_query), function ($v) { return tep_not_null($v); });
      if (!is_null($this->data[$to])) {
        $this->preload_columns($this->data[$to]);
      }
    }

    /**
     * @param int $to The ID of the customer's address.
     * @return array Of the address information.
     */
    public function &fetch_to_address($to = null) {
      if (!empty($to) && is_array($to)) {
        if (empty($to['state'])) {
          $to['state'] = $to['zone_name'] ?? null;
        }

        if (!isset($to['country_id'])) {
          $to['country_id'] = $to['country']['id'] ?? null;
        }

        if (!isset($to['id'])) {
          $to['id'] = $this->id;
        }

        $this->preload_columns($to);
        return $to;
      } elseif (is_numeric($to ?? null)) {
        $this->fetch_address($to);
      } else {
        if (!isset($this->data[0])) {
          $customer_data = $this->guarantee_customer_data();
          $this->data[0] = array_fill_keys($customer_data->list_all_capabilities(), null);
          $customer_data->get('country', $this->data[0]);
        }
        $to = 0;
      }

      return $this->data[$to];
    }

    public function get_id() {
      return $this->id;
    }

    public function get($key, $to = 0) {
      if (!isset($this->fetch_to_address($to)[$key])) {
        $this->guarantee_customer_data()->get($key, $this->data[$to]);
      }

      return $this->data[$to][$key] ?? null;
    }

    public function set($key, $value, $to = 0) {
      $customer_details = $this->fetch_to_address($to);
      if (!isset($customer_details[$key])) {
        $this->guarantee_customer_data()->get($key, $customer_details);
      }

      if (!isset($customer_details[$key]) || $customer_details[$key] !== $value) {
        $this->unpersisted[$key] = $value;
        $this->data[$to][$key] = $value;
      }
    }

    public function persist($to = 0) {
      if ($to > 0) {
        $this->guarantee_customer_data()->update(
          $this->unpersisted,
          ['id' => $this->id, 'address_book_id' => (int)$to],
          'address_book');
      } else {
        $this->guarantee_customer_data()->update(
          $this->unpersisted,
          ['id' => $this->id, 'address_book_id' => (int)$this->data[0]['default_address_id']],
          'both');
      }

      $this->unpersisted = [];
    }

    /**
     * @return string A short version of the customer's name.
     */
    public function get_short_name() {
      return $this->get('short_name');
    }

    public function get_default_address_id() {
      return $this->get('default_address_id');
    }

    public function get_country_id() {
      return $this->get('country_id');
    }

    public function get_zone_id() {
      return $this->get('zone_id');
    }

    public function get_all_addresses_query() {
      return tep_db_query($this->guarantee_customer_data()->build_read(['address'], 'address_book', ['id' => (int)$this->id]));
//      return tep_db_query("select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from address_book where customers_id = '" . (int)$customer_id . "'");
    }

    public function get_all_addresses() {
      $addresses_query = $this->get_all_addresses_query();
      while ($address = tep_db_fetch_array($addresses_query)) {
        yield $address;
      }
    }

    public function count_addresses() {
      return tep_db_num_rows($this->get_all_addresses_query());
    }

    public function make_address_label($to = 0, $html = false, $boln = '', $eoln = "\n") {
      return $this->guarantee_customer_data()->get_module('address')->format($this->fetch_to_address($to), $html, $boln, $eoln);
    }

  }
