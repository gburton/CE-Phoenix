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

    public static function create($db_tables, &$customer_details = []) {
      $foreign_keys = self::FOREIGN_KEYS;
      $tables = array_reverse(array_keys(static::TABLE_ALIASES));

      unset($db_tables['customers_info']);
      $parameters = [
        'data' => &$customer_details,
        'db' => &$db_tables,
        'keys' => &$foreign_keys,
        'tables' => &$tables,
      ];
      $GLOBALS['OSCOM_Hooks']->call('siteWide', 'accountCreationTables', $parameters);

      foreach ($tables as $db_table) {
        if (!isset($db_tables[$db_table])) {
          continue;
        }

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
      $parameters = [
        'db' => &$db_tables,
        'criteria' => &$criteria,
        'keys' => &$foreign_keys,
      ];

      $GLOBALS['OSCOM_Hooks']->call('siteWide', 'accountUpdateTables', $parameters);

      // do not update columns that are null
      $db_tables = array_map(function ($value) {
        return array_filter($value, function ($v) {
          return isset($v);
        });
      }, $db_tables);
      $db_tables = array_filter($db_tables);

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