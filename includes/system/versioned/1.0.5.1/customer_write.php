<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class customer_write extends customer_query {

    const FOREIGN_KEYS = [
      'customers_id' => [ 'address_book' ],
    ];

    const IDENTIFIER_SUFFIX = '_id';

    public static function upsert_customer_data($data, $id) {
      $sql = <<<'EOSQL'
INSERT INTO customers_data (customers_id, customers_data_key, customers_data_value)
  VALUES
EOSQL;

      foreach ($data as $key => $value) {
        $sql .= '(' . (int)$id . ", '"
          . tep_db_input($key) . "', '"
            . tep_db_input($value) . "')" . self::COLUMN_SEPARATOR;
      }

      $sql = self::rtrim_string_once($sql, self::COLUMN_SEPARATOR);
      $sql .= ' ON DUPLICATE KEY UPDATE customers_data_value = VALUES(customers_data_value)';

      tep_db_query($sql);
    }

    public static function create($db_tables, &$customer_details = []) {
      $foreign_keys = self::FOREIGN_KEYS;

      unset($db_tables['customers_info']);
      $GLOBALS['OSCOM_Hooks']->call('siteWide', 'accountCreationTables', $parameters = [
        'data' => &$customer_details,
        'db' => &$db_tables,
        'keys' => &$foreign_keys,
      ]);

      if (!empty($db_tables['customers_data'])) {
        if (isset($criteria['id'])) {
          self::upsert_customer_data($db_tables['customers_data'], $criteria['id']);
        }

        unset($db_tables['customers_data']);
      }

      foreach ($db_tables as $db_table => $columns) {
        tep_db_perform($db_table, $db_tables[$db_table]);
        $key = $db_table . self::IDENTIFIER_SUFFIX;
        $customer_details[$key] = tep_db_insert_id();
        if (isset($foreign_keys[$key]) && is_array($foreign_keys[$key])) {
          foreach ($foreign_keys[$key] as $table) {
            $db_tables[$table][$key] = $customer_details[$key];
          }
        }
      }
    }

    public static function update($db_tables, $criteria = []) {
      $foreign_keys = self::FOREIGN_KEYS;

      $GLOBALS['OSCOM_Hooks']->call('siteWide', 'accountUpdateTables', $parameters = [
        'db' => $db_tables,
        'criteria' => $criteria,
        'keys' => $foreign_keys,
      ]);

      // do not update columns that are null
      $db_tables = array_map(function ($value) {
        return array_filter($value, function ($v) {
          return isset($v);
        });
      }, $db_tables);
      $db_tables = array_filter($db_tables);

      if (isset($db_tables['customers_data'])) {
        if (isset($criteria['id'])) {
          self::upsert_customer_data($db_tables['customers_data'], $criteria['id']);
        }

        unset($db_tables['customers_data']);
      }

      foreach ($foreign_keys as $foreign_key => $tables) {
        foreach ($tables as $db_table) {
          tep_guarantee_subarray($criteria, $db_table);
          if (!isset($criteria[$db_table][$foreign_key])) {
            $foreign_table = self::rtrim_string_once($foreign_key, self::IDENTIFIER_SUFFIX);
            $criteria[$db_table][$foreign_key] = $criteria[$foreign_table][$foreign_key];
          }
        }
      }

      // the values of $criteria should be arrays
      // remove nulls and empty arrays and falsey values
      // but the only falsey values that should appear are nulls and empty arrays
      $criteria = array_filter($criteria);

      foreach ($db_tables as $db_table => $column_values) {
        tep_db_perform($db_table, $column_values, 'update',
          self::build_criteria($db_table, $criteria[$db_table]));
      }
    }

  }