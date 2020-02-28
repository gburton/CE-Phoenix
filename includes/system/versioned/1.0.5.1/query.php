<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class query {

    const WHERE = ' WHERE ';
    const ORDER_BY = ' ORDER BY ';
    const CRITERIA_INTERSECTION = ' AND ';
    const CRITERIA_UNION = ' OR ';
    const COLUMN_SEPARATOR = ', ';
    const TABLE_ALIASES = [];

    public static function rtrim_string_once($haystack, $needle) {
      $displacement = -strlen($needle);
      if (substr($haystack, $displacement) === $needle) {
        $haystack = substr($haystack, 0, $displacement);
      }

      return $haystack;
    }

    public static function add_search_criteria($sql, $key, $db_tables) {
      $key = tep_db_input($key);
      $criteria = [];
      foreach ($db_tables as $db_table => $columns) {
        $table_alias = static::TABLE_ALIASES[$db_table] ?? $db_table;
        foreach (array_keys($columns) as $name) {
          $criteria[] = "$table_alias.$name LIKE '%$key%'";
        }
      }

      if (empty($criteria)) {
        return $sql;
      }

      $where_position = strrpos($sql, self::WHERE);
      if (false === $where_position) {
        $sql .= self::WHERE;
      } elseif ($where_position + strlen(self::WHERE) + 1 < strlen($sql)) {
        $sql .= self::CRITERIA_INTERSECTION;
      }

      $sql .= '(' . implode(self::CRITERIA_UNION, $criteria) . ')';

      return $sql;
    }

    public static function add_order_by($criteria) {
      $sql = self::ORDER_BY;
      foreach ($criteria as $db_table => $column_directions) {
        $table_alias = static::TABLE_ALIASES[$db_table] ?? tep_db_input($db_table);
        foreach ($column_directions as $column => $direction) {
          $sql .= $table_alias . '.' . tep_db_input($column);
          if (!empty($direction) && 'DESC' === strtoupper($direction)) {
            $sql .= ' DESC';
          }
          $sql .= self::COLUMN_SEPARATOR;
        }
      }

      return self::rtrim_string_once($sql, self::COLUMN_SEPARATOR);
    }

    public static function build_specified_columns($db_tables) {
      $sql = '';

      foreach ($db_tables as $db_table => $columns) {
        $table_alias = static::TABLE_ALIASES[$db_table] ?? $db_table;
        foreach ($columns as $column => $alias) {
          if (isset($alias)) {
            $sql .= "$table_alias.$column AS $alias";
            $sql .= self::COLUMN_SEPARATOR;
          }
        }
      }

      return $sql;
    }

    public static function build_columns($db_tables) {
      $sql = '';

      foreach (static::TABLE_ALIASES as $db_table => $table_alias) {
        if (isset($db_tables[$db_table])) {
          $sql .= "$table_alias.*" . self::COLUMN_SEPARATOR;
        }
      }

      foreach (array_diff(array_keys($db_tables), array_keys(static::TABLE_ALIASES)) as $db_table) {
        $sql .= "$db_table.*" . self::COLUMN_SEPARATOR;
      }

      return $sql . static::build_specified_columns($db_tables);
    }

    public static function _build_columns($db_tables) {
      return self::rtrim_string_once(self::build_columns($db_tables), self::COLUMN_SEPARATOR);
    }

    private static function _build_criteria($alias, $column_values) {
      $sql = '';

      foreach ($column_values as $column => $value) {
        if (!is_null($alias)) {
          $sql .= "$alias.";
        }

        $sql .= "$column = ";

        if (is_int($value)) {
          $sql .= (int)$value;
        } else {
          // if not int, assume a string
          $sql .= "'" . tep_db_input($value) . "'";
        }
        $sql .= self::CRITERIA_INTERSECTION;
      }

      return $sql;
    }

    public static function build_criteria($db_table, $column_values) {
      return self::rtrim_string_once(self::_build_criteria(null, $column_values), self::CRITERIA_INTERSECTION);
    }

    public static function build_where($criteria, $skip_alias = false) {
      $sql = '';

      if (empty($criteria)) {
        // do nothing
      } elseif (is_string($criteria)) {
        $sql .= $criteria;
      } elseif (is_array($criteria)) {
        $sql .= self::WHERE;

        if ($skip_alias && count($criteria) === 1) {
          $sql .= self::_build_criteria(null, reset($criteria));
        } else {
          foreach ($criteria as $db_table => $column_values) {
            $sql .= self::_build_criteria(static::TABLE_ALIASES[$db_table], $column_values);
          }
        }

        $sql = self::rtrim_string_once($sql, self::CRITERIA_INTERSECTION);
      }

      return $sql;
    }

  }