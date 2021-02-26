<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class customer_data extends requirements_manager {

    public $modules;
    private $grouped_modules;

    // class constructor
    function __construct() {
      if (defined('MODULE_CUSTOMER_DATA_INSTALLED') && tep_not_null(MODULE_CUSTOMER_DATA_INSTALLED)) {
        $this->modules = (array)explode(';', MODULE_CUSTOMER_DATA_INSTALLED);

        global $language;
        foreach ($this->modules as $basename) {
          $class = pathinfo($basename, PATHINFO_FILENAME);

          if (!isset($GLOBALS[$class])) {
            $GLOBALS[$class] = new $class();
          }

          if (!$GLOBALS[$class]->isEnabled()) {
            continue;
          }

          $this->objects[] = &$GLOBALS[$class];

          if (method_exists($GLOBALS[$class], 'get_group')) {
            $group = $GLOBALS[$class]->get_group();
            if (is_scalar($group)) {
              Guarantor::guarantee_subarray($this->grouped_modules, $group);
              $this->grouped_modules[$group][] = &$GLOBALS[$class];
            }
          }

          foreach ($GLOBALS[$class]::PROVIDES as $provided) {
            $this->providers[$provided] = &$GLOBALS[$class];
          }
        }

        foreach ($this->grouped_modules as &$modules) {
          uasort($modules, function ($a, $b) { return $a->sort_order <=> $b->sort_order; });
        }
        unset($modules);
      }
    }

    public function get_grouped_modules() {
      return $this->grouped_modules;
    }

    public function get($field, &$customer_details) {
      if (is_array($field)) {
        $customer_data = $this;
        return array_map(
          function ($v) use ($customer_data, &$customer_details) {
            return $customer_data->get($v, $customer_details);
          },
        $field);
      }

      if (!isset($customer_details[$field])) {
        if (!isset($this->providers[$field])) {
          return false;
        }

        $this->providers[$field]->get($field, $customer_details);
      }

      return $customer_details[$field] ?? null;
    }

    public function display_input($fields = null, &$customer_details = []) {
      if (!isset($fields)) {
        $fields = $this->list_all_capabilities();
      }

      $seen = [];
      foreach ((array)$fields as $field) {
        if (!isset($this->providers[$field]) || !method_exists($this->providers[$field], 'display_input') || in_array($this->providers[$field], $seen)) {
          continue;
        }

        $this->providers[$field]->display_input($customer_details);
        $seen[] = $this->providers[$field];
      }
    }

    public function get_fields_for_page($page) {
      return array_keys(array_unique(array_filter($this->providers,
        function ($p) use ($page) {
          return method_exists($p, 'has_page') && $p->has_page($page);
        }), SORT_REGULAR));
    }

    /**
     * @param array $requests A list of the customer data needed, e.g. name.
     * @param string $table From what table should duplicate data be loaded.  May be 'customers', 'address_book', or 'both'.
     * @param array $criteria An array of column values for the WHERE clause.
     */
    public function build_read($requests, $table = 'both', $criteria = []) {
      return customer_query::build_read($this->build_db_tables($requests, $table), $this->build_db_table_values($criteria, $table));
    }

    public function add_search_criteria($sql, $key) {
      $db_tables = [];
      foreach ($this->objects as $module) {
        if (method_exists($module, 'is_searchable') && $module->is_searchable()) {
          $module->build_db_aliases($db_tables);
        }
      }

      return customer_query::add_search_criteria($sql, $key, $db_tables);
    }

    public function get_failover($requirement) {
      if ('sortable_name' === $requirement) {
        return 'name';
      }

      return false;
    }

    public function add_order_by($sql, $criteria = ['id']) {
      $order_by_columns = [];
      foreach ($criteria as $index => $criterion) {
        $direction = null;
        if (is_string($index)) {
          // because we accept simple lists and associative arrays
          // we have to detect when it is an associative array
          // and move around the $criterion handle
          // we can't do it the other way as the direction is not unique
          $direction = $criterion;
          $criterion = $index;
        }

        while (!isset($this->providers[$criterion]) || !method_exists($this->providers[$criterion], 'add_order_by')) {
          $criterion = $this->get_failover($criterion);
          if (false === $criterion) {
            continue 2;
          }
        }

        $this->providers[$criterion]->add_order_by($order_by_columns, $criterion, $direction);
      }

      if ([] !== $order_by_columns) {
        return $sql . customer_query::add_order_by($order_by_columns);
      }

      return $sql . customer_query::add_order_by([ 'customers' => [ 'customers_id' => null ]]);
    }

    public function count_by_criteria($criteria, $table = 'both') {
      return customer_query::count_by_criteria($this->build_db_table_values($criteria, $table));
    }

    public function add_address(&$field_values) {
      customer_write::create($this->build_db_table_values($field_values, 'address_book'), $field_values);
    }

    public function create(&$field_values, $table = 'both') {
      customer_write::create($this->build_db_table_values($field_values, $table), $field_values);

      if (isset($field_values['address_book_id']) && isset($field_values['customers_id'])) {
        tep_db_query("UPDATE customers SET customers_default_address_id = " . (int)$field_values['address_book_id']
          . " WHERE customers_id = " . (int)$field_values['customers_id']);
      }

      tep_db_query("INSERT INTO customers_info (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) VALUES ("
        . (int)$field_values['customers_id'] . ", 0, NOW())");
    }

    public function update($field_values, $criteria = [], $table = 'both') {
      customer_write::update($this->build_db_table_values($field_values, $table), $this->build_db_table_values($criteria, $table));
    }

  }
