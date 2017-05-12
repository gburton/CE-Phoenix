<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  function osc_db_connect($server, $username, $password, $link = 'db_link') {
    global $$link, $db_error;

    $db_error = false;

    if (!$server) {
      $db_error = 'No Server selected.';
      return false;
    }

    $$link = @mysqli_connect($server, $username, $password);

    if ( !mysqli_connect_errno() ) {
      mysqli_set_charset($$link, 'utf8');

      @mysqli_query($$link, 'set session sql_mode=""');
    } else {
      $db_error = mysqli_connect_error();
    }

    return $$link;
  }

  function osc_db_select_db($database, $link = 'db_link') {
    global $$link, $db_error;

    if ( empty($database) ) {
      $db_error = 'No Database selected.';
      return false;
    }

    if ( !@mysqli_select_db($$link, $database) ) {
      $db_error = 'Could not open database "' . $database . '".';
      return false;
    }

    return true;
  }

  function osc_db_query($query, $link = 'db_link') {
    global $$link;

    if (defined('OSCOM_DB_TABLE_PREFIX')) {
      $query = str_replace(':table_', OSCOM_DB_TABLE_PREFIX, $query);
    }

    return mysqli_query($$link, $query);
  }

  function osc_db_num_rows($db_query) {
    return mysqli_num_rows($db_query);
  }

  function osc_db_install($database, $sql_file, $table_prefix = null, $link = 'db_link') {
    global $$link, $db_error;

    $db_error = false;

    if (!@osc_db_select_db($database)) {
      if (@osc_db_query('create database ' . $database)) {
        osc_db_select_db($database);
      } else {
        $db_error = mysqli_error($$link);
      }
    }

    if (!$db_error) {
      if (file_exists($sql_file)) {
        $fd = fopen($sql_file, 'rb');
        $restore_query = fread($fd, filesize($sql_file));
        fclose($fd);
      } else {
        $db_error = 'SQL file does not exist: ' . $sql_file;
        return false;
      }

      $sql_array = array();
      $sql_length = strlen($restore_query);
      $pos = strpos($restore_query, ';');
      for ($i=$pos; $i<$sql_length; $i++) {
        if (substr($restore_query, 0, 1) == '#') {
          $restore_query = ltrim(substr($restore_query, strpos($restore_query, "\n")));
          $sql_length = strlen($restore_query);
          $i = strpos($restore_query, ';')-1;
          continue;
        }
        if (substr($restore_query, $i+1, 1) == "\n") {
          for ($j=($i+2); $j<$sql_length; $j++) {
            if (trim(substr($restore_query, $j, 1)) != '') {
              $next = substr($restore_query, $j, 6);
              if (substr($next, 0, 1) == '#') {
// find out where the break position is so we can remove this line (#comment line)
                for ($k=$j; $k<$sql_length; $k++) {
                  if (substr($restore_query, $k, 1) == "\n") break;
                }
                $query = substr($restore_query, 0, $i+1);
                $restore_query = substr($restore_query, $k);
// join the query before the comment appeared, with the rest of the dump
                $restore_query = $query . $restore_query;
                $sql_length = strlen($restore_query);
                $i = strpos($restore_query, ';')-1;
                continue 2;
              }
              break;
            }
          }
          if ($next == '') { // get the last insert query
            $next = 'insert';
          }
          if ( (preg_match('/create/i', $next)) || (preg_match('/insert/i', $next)) || (preg_match('/drop t/i', $next)) ) {
            $next = '';
            $sql_array[] = substr($restore_query, 0, $i);
            $restore_query = ltrim(substr($restore_query, $i+1));
            $sql_length = strlen($restore_query);
            $i = strpos($restore_query, ';')-1;
          }
        }
      }

      for ($i=0; $i<sizeof($sql_array); $i++) {
        $sql_query = $sql_array[$i];

        if (isset($table_prefix) && !empty($table_prefix)) {
          if (strtoupper(substr($sql_query, 0, 20)) == 'DROP TABLE IF EXISTS') {
            $sql_query = 'DROP TABLE IF EXISTS ' . $table_prefix . substr($sql_query, 21);
          } elseif (strtoupper(substr($sql_query, 0, 12)) == 'CREATE TABLE') {
            $sql_query = 'CREATE TABLE ' . $table_prefix . substr($sql_query, 13);
          } elseif (strtoupper(substr($sql_query, 0, 11)) == 'INSERT INTO') {
            $sql_query = 'INSERT INTO ' . $table_prefix . substr($sql_query, 12);
          }
        }

        if (!osc_db_query($sql_query)) {
          $db_error = mysqli_error($$link);

          return false;
        }
      }
    } else {
      return false;
    }
  }

  if ( !function_exists('mysqli_connect') ) {
    function mysqli_connect_errno($link = null) {
      if ( is_null($link) ) {
        return mysql_errno();
      }

      return mysql_errno($link);
    }

    function mysqli_connect_error($link = null) {
      if ( is_null($link) ) {
        return mysql_error();
      }

      return mysql_error($link);
    }

    function mysqli_connect($server, $username, $password) {
      if ( substr($server, 0, 2) == 'p:' ) {
        $link = mysql_pconnect(substr($server, 2), $username, $password);
      } else {
        $link = mysql_connect($server, $username, $password);
      }

      return $link;
    }

    function mysqli_set_charset($link, $charset) {
      if ( function_exists('mysql_set_charset') ) {
        return mysql_set_charset($charset, $link);
      }
    }

    function mysqli_select_db($link, $database) {
      return mysql_select_db($database, $link);
    }

    function mysqli_query($link, $query) {
      return mysql_query($query, $link);
    }

    function mysqli_error($link = null) {
      if ( is_null($link) ) {
        return mysql_error();
      }

      return mysql_error($link);
    }

    function mysqli_num_rows($query) {
      return mysql_num_rows($query);
    }
  }
?>
