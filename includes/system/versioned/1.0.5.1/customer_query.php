<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class customer_query extends query {

    const TABLE_ALIASES = [
      'customers_info' => 'ci',
      'zones' => 'z',
      'products_notifications' => 'pn',
      'countries' => 'co',
      'address_book' => 'ab',
      'customers' => 'c',
    ];

    public static function build_joins($db_tables, $criteria) {
      $sql = '';

      if (!isset($criteria['address_book_id']) && !isset($db_tables['customers']) && isset($db_tables['address_book'], $criteria['customers_id'])) {
        $db_tables['customers'] = [];
      }

      if (isset($db_tables['customers'])) {
        $sql .= ' customers ' . self::TABLE_ALIASES['customers'];
      }

      if (isset($db_tables['address_book'])) {
        $suffix = '';
        if (isset($db_tables['customers'])) {
          $sql .= ' LEFT JOIN';
          $suffix = ' ON ' . self::TABLE_ALIASES['customers'] . '.customers_id = ' . self::TABLE_ALIASES['address_book'] . '.customers_id';
          if (!isset($criteria['address_book_id'])) {
            $suffix .= ' AND ' . self::TABLE_ALIASES['customers'] . '.customers_default_address_id = ' . self::TABLE_ALIASES['address_book'] . '.address_book_id';
          }
        }
        $sql .= ' address_book ' . self::TABLE_ALIASES['address_book'] . $suffix;
      }

      if (isset($db_tables['zones'])) {
        if (isset($db_tables['address_book'])) {
          $sql .= ' LEFT JOIN zones ' . self::TABLE_ALIASES['zones']
          . ' ON ' . self::TABLE_ALIASES['address_book'] . '.entry_zone_id = '
            . self::TABLE_ALIASES['zones'] . '.zone_id';
        }
      }

      if (isset($db_tables['countries'])) {
        if (isset($db_tables['address_book'])) {
          $sql .= ' LEFT JOIN countries ' . self::TABLE_ALIASES['countries']
          . ' ON ' . self::TABLE_ALIASES['address_book'] . '.entry_country_id = '
            . self::TABLE_ALIASES['countries'] . '.countries_id';
        }
      }

      if (isset($db_tables['customers_info'])) {
        if (isset($db_tables['customers'])) {
          $sql .= ' INNER JOIN customers_info ' . self::TABLE_ALIASES['customers_info']
                . ' ON ' . self::TABLE_ALIASES['customers'] . '.customers_id = '
                . self::TABLE_ALIASES['customers_info'] . '.customers_info_id';
        } elseif (isset($db_tables['address_book'])) {
          $sql .= ' INNER JOIN customers_info ' . self::TABLE_ALIASES['customers_info']
                . ' ON ' . self::TABLE_ALIASES['address_book'] . '.customers_id = '
                . self::TABLE_ALIASES['customers_info'] . '.customers_info_id';
        }
      }

      return $sql;
    }

    public static function build_read($db_tables, $criteria) {
      foreach ($db_tables as $db_table => &$columns) {
        $primary_key = $db_table . '_id';
        if (!array_key_exists($primary_key, $columns)) {
          $columns[$primary_key] = null;
        }
      }
      unset($columns);

      $sql = 'SELECT ' . self::_build_columns($db_tables);
      $sql .= ' FROM' . self::build_joins($db_tables, $criteria);
      $sql .= self::build_where($criteria);

      return $sql;
    }

    public static function count_by_criteria($criteria) {
      $sql = 'SELECT COUNT(*) AS total FROM';
      $sql .= self::build_joins($criteria, $criteria);
      $sql .= self::build_where($criteria);

      $query = tep_db_query($sql);
      $result = tep_db_fetch_array($query);

      return $result['total'] ?? null;
    }

  }