<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class splitPageResults {

    private $current_page_number;

    function __construct(&$current_page_number, $max_rows_per_page, &$sql_query, &$query_num_rows) {
      $this->current_page_number = empty($current_page_number) ? 1 : (int)$current_page_number;

      $pos_to = strlen($sql_query);
      $pos_from = stripos($sql_query, ' from');

      $pos_group_by = strripos($sql_query, ' group by', $pos_from);
      if (($pos_group_by < $pos_to) && ($pos_group_by != false)) $pos_to = $pos_group_by;

      $pos_having = strripos($sql_query, ' having', $pos_from);
      if (($pos_having < $pos_to) && ($pos_having != false)) $pos_to = $pos_having;

      $pos_order_by = strripos($sql_query, ' order by', $pos_from);
      if (($pos_order_by < $pos_to) && ($pos_order_by != false)) $pos_to = $pos_order_by;

      $reviews_count_query = tep_db_query("SELECT COUNT(*) AS total " . substr($sql_query, $pos_from, ($pos_to - $pos_from)));
      $reviews_count = tep_db_fetch_array($reviews_count_query);
      $query_num_rows = $reviews_count['total'];

      $num_pages = (int)ceil($query_num_rows / $max_rows_per_page);
      if ($this->current_page_number > $num_pages) {
        $current_page_number = $this->current_page_number = $num_pages;
      }
      $offset = ($max_rows_per_page * ($this->current_page_number - 1));
      $sql_query .= " LIMIT " . max($offset, 0) . ", " . $max_rows_per_page;
    }

    function display_links($query_numrows, $max_rows_per_page, $max_page_links, $current_page_number, $parameters = '', $page_name = 'page') {
      global $PHP_SELF;

      if ( tep_not_null($parameters) && (substr($parameters, -1) != '&') ) $parameters .= '&';

// calculate number of pages needing links
      $num_pages = ceil($query_numrows / $max_rows_per_page);

      $pages_array = [];
      for ($i = 1; $i <= $num_pages; $i++) {
        $pages_array[] = ['id' => $i, 'text' => $i];
      }

      if ($num_pages > 1) {
        $display_links = tep_draw_form('pages', $PHP_SELF, '', 'get');
        $display_links .= '<div class="input-group">';
          $display_links .= '<div class="input-group-append">';
            $display_links .= '<span class="input-group-text" id="p">' . SPLIT_PAGES . '</span>';
          $display_links .= '</div>';

          $display_links .= tep_draw_pull_down_menu($page_name, $pages_array, $this->current_page_number, 'onchange="this.form.submit();"');

          if ($parameters != '') {
            if (substr($parameters, -1) == '&') $parameters = substr($parameters, 0, -1);
            $pairs = explode('&', $parameters);
            foreach ($pairs as $pair) {
              list($key,$value) = explode('=', $pair);
              $display_links .= tep_draw_hidden_field(rawurldecode($key), rawurldecode($value));
            }
          }

          $display_links .= tep_hide_session_id() . '</form>';
        $display_links .= '</div>';
      } else {
        $display_links = sprintf(TEXT_RESULT_PAGE, $num_pages, $num_pages);
      }

      return $display_links;
    }

    function display_count($query_numrows, $max_rows_per_page, $current_page_number, $text_output) {
      $to_num = ($max_rows_per_page * $this->current_page_number);
      if ($to_num > $query_numrows) $to_num = $query_numrows;
      $from_num = ($max_rows_per_page * ($this->current_page_number - 1));
      if ($to_num == 0) {
        $from_num = 0;
      } else {
        $from_num++;
      }

      return sprintf($text_output, $from_num, $to_num, $query_numrows);
    }

  }
