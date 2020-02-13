<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  function tep_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
    global $$link;

    $$link = mysqli_connect($server, $username, $password, $database);

    if ( !mysqli_connect_errno() ) {
      mysqli_set_charset($$link, 'utf8');
    }

    @mysqli_query($$link, 'SET SESSION sql_mode=""');

    return $$link;
  }

  function tep_db_close($link = 'db_link') {
    return mysqli_close($GLOBALS[$link]);
  }

  function tep_db_error($query, $errno, $error) {
    if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
      error_log("ERROR: [$errno] $error\n" . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    die('<font color="#000000"><strong>' . $errno . ' - ' . $error . '<br><br>' . $query . '<br><br><small><font color="#ff0000">[TEP STOP]</font></small><br><br></strong></font>');
  }

  function tep_db_query($query, $link = 'db_link') {
    global $$link;

    if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
      error_log('QUERY: ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    $result = mysqli_query($$link, $query) or tep_db_error($query, mysqli_errno($$link), mysqli_error($$link));

    return $result;
  }

  function tep_db_perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link') {
    if ($action == 'insert') {
      $query = 'INSERT INTO ' . $table . ' (';
      foreach(array_keys($data) as $columns) {
        $query .= $columns . ', ';
      }
      $query = substr($query, 0, -2) . ') VALUES (';
      foreach($data as $value) {
        switch ((string)$value) {
          case 'NOW()':
          case 'now()':
            $query .= 'NOW(), ';
            break;
          case 'NULL':
          case 'null':
            $query .= 'NULL, ';
            break;
          default:
            $query .= '\'' . tep_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ')';
    } elseif ($action == 'update') {
      $query = 'UPDATE ' . $table . ' SET ';
      foreach($data as $columns => $value) {
        switch ((string)$value) {
          case 'NOW()':
          case 'now()':
            $query .= $columns . ' = NOW(), ';
            break;
          case 'NULL':
          case 'null':
            $query .= $columns .= ' = NULL, ';
            break;
          default:
            $query .= $columns . ' = \'' . tep_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ' WHERE ' . $parameters;
    }

    return tep_db_query($query, $link);
  }

  function tep_db_fetch_array($db_query) {
    return mysqli_fetch_array($db_query, MYSQLI_ASSOC);
  }

  function tep_db_num_rows($db_query) {
    return mysqli_num_rows($db_query);
  }

  function tep_db_data_seek($db_query, $row_number) {
    return mysqli_data_seek($db_query, $row_number);
  }

  function tep_db_insert_id($link = 'db_link') {
    return mysqli_insert_id($GLOBALS[$link]);
  }

  function tep_db_free_result($db_query) {
    return mysqli_free_result($db_query);
  }

  function tep_db_fetch_fields($db_query) {
    return mysqli_fetch_field($db_query);
  }

  function tep_db_output($string) {
    return htmlspecialchars($string);
  }

  function tep_db_input($string, $link = 'db_link') {
    return mysqli_real_escape_string($GLOBALS[$link], $string);
  }

  function tep_db_prepare_input($input) {
    if (is_string($input)) {
      return trim(tep_sanitize_string(stripslashes($input)));
    }

    if (is_array($input)) {
      return array_map('tep_db_prepare_input', $input);
    }

    return $input;
  }

  function tep_db_affected_rows($link = 'db_link') {
    return mysqli_affected_rows($GLOBALS[$link]);
  }

  function tep_db_get_server_info($link = 'db_link') {
    return mysqli_get_server_info($GLOBALS[$link]);
  }
