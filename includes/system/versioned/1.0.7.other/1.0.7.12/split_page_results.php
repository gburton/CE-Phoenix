<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class splitPageResults {

    public $sql_query;
    public $number_of_rows;

    protected $current_page_number;
    protected $number_of_pages;
    protected $number_of_rows_per_page;
    protected $page_name;

/* class constructor */
    public function __construct($query, $max_rows, $count_key = '*', $page_holder = 'page') {
      $this->sql_query = $query;
      $this->page_name = $page_holder;

      $page = $_GET[$this->page_name] ?? $_POST[$this->page_name] ?? '';

      if (empty($page) || !is_numeric($page)) {
        $page = 1;
      }
      $this->current_page_number = $page;

      $this->number_of_rows_per_page = $max_rows;

      $pos_to = strlen($this->sql_query);
      $pos_from = stripos($this->sql_query, ' FROM');
      $pos_where = strripos($this->sql_query, ' WHERE') ?: $pos_from;

      $pos_group_by = stripos($this->sql_query, ' GROUP BY', $pos_where);
      if ($pos_group_by && ($pos_group_by < $pos_to)) {
        $pos_to = $pos_group_by;
      }

      $pos_having = stripos($this->sql_query, ' HAVING', $pos_where);
      if ($pos_having && ($pos_having < $pos_to)) {
        $pos_to = $pos_having;
      }

      $pos_order_by = stripos($this->sql_query, ' ORDER BY', $pos_where);
      if ($pos_order_by && ($pos_order_by < $pos_to)) {
        $pos_to = $pos_order_by;
      }

      if (stripos($this->sql_query, 'DISTINCT') || stripos($this->sql_query, 'GROUP BY', $pos_where)) {
        $count_string = 'DISTINCT ' . tep_db_input($count_key);
      } else {
        $count_string = tep_db_input($count_key);
      }

      $count_query = tep_db_query("SELECT COUNT(" . $count_string . ") AS total " . substr($this->sql_query, $pos_from, ($pos_to - $pos_from)));
      $count = $count_query->fetch_assoc();

      $this->number_of_rows = $count['total'];

      $this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);

      if ($this->current_page_number > $this->number_of_pages) {
        $this->current_page_number = $this->number_of_pages;
      }

      $offset = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

      $this->sql_query .= " LIMIT " . max($offset, 0) . ", " . $this->number_of_rows_per_page;
    }

/* class functions */

// display split-page-number-links
    public function display_links($max_page_links, $parameters = '') {
      global $PHP_SELF;

      $display_links_string = '<nav aria-label="...">';
        $display_links_string .= '<ul class="pagination pagination-lg justify-content-end">';

        if (tep_not_null($parameters) && (substr($parameters, -1) != '&')) {
          $parameters .= '&';
        }

// previous button - not displayed on first page
        if ($this->current_page_number > 1) {
          $display_links_string .= '<li class="page-item">';
            $display_links_string .= '<a class="page-link" href="' . tep_href_link($PHP_SELF, $parameters . $this->page_name . '=' . ($this->current_page_number - 1)) . '" title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><i class="fas fa-angle-left"></i></a>';
          $display_links_string .= '</li>';
        } else {
          $display_links_string .= '<li class="page-item disabled">';
            $display_links_string .= '<a class="page-link" href="#" tabindex="-1"><i class="fas fa-angle-left"></i></a>';
          $display_links_string .= '</li>';
        }

// check if number_of_pages > $max_page_links
        $cur_window_num = (int)($this->current_page_number / $max_page_links);
        if ($this->current_page_number % $max_page_links) {
          $cur_window_num++;
        }

        $max_window_num = (int)($this->number_of_pages / $max_page_links);
        if ($this->number_of_pages % $max_page_links) {
          $max_window_num++;
        }

// previous window of pages
        if ($cur_window_num > 1) {
          $display_links_string .= '<li class="page-item">';
            $display_links_string .= '<a class="page-link" href="' . tep_href_link($PHP_SELF, $parameters . $this->page_name . '=' . (($cur_window_num - 1) * $max_page_links)) . '" title=" ' . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>';
          $display_links_string .= '</li>';
        }

// page nn button
        for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++) {
          if ($jump_to_page == $this->current_page_number) {
            $display_links_string .= '<li class="page-item active">';
              $display_links_string .= '<a class="page-link" href="' . tep_href_link($PHP_SELF, $parameters . $this->page_name . '=' . $jump_to_page) . '" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' ">' . $jump_to_page . '<span class="sr-only">(current)</span></a>';
            $display_links_string .= '</li>';
          } else {
            $display_links_string .= '<li class="page-item">';
              $display_links_string .= '<a class="page-link" href="' . tep_href_link($PHP_SELF, $parameters . $this->page_name . '=' . $jump_to_page) . '" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' ">' . $jump_to_page . '</a>';
            $display_links_string .= '</li>';
          }
        }

// next window of pages
        if ($cur_window_num < $max_window_num) {
          $display_links_string .= '<li class="page-item">';
            $display_links_string .= '<a class="page-link" href="' . tep_href_link($PHP_SELF, $parameters . $this->page_name . '=' . (($cur_window_num) * $max_page_links + 1)) . '" title=" ' . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>';
          $display_links_string .= '</li>';
        }

// next button
        if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) {
          $display_links_string .= '<li class="page-item">';
            $display_links_string .= '<a class="page-link" href="' . tep_href_link($PHP_SELF, $parameters . 'page=' . ($this->current_page_number + 1)) . '" aria-label=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' "><span aria-hidden="true"><i class="fas fa-angle-right"></i></span></a>';
            $display_links_string .= '<span class="sr-only">' . PREVNEXT_TITLE_NEXT_PAGE . '</span>';
          $display_links_string .= '</li>';
        } else {
          $display_links_string .= '<li class="page-item disabled">';
            $display_links_string .= '<a class="page-link" href="#" tabindex="-1"><i class="fas fa-angle-right"></i></a>';
          $display_links_string .= '</li>';
        }

        $display_links_string .= '</ul>';
      $display_links_string .= '</nav>';

      return $display_links_string;
    }

// display number of total products found
    function display_count($text_output) {
      $to_num = ($this->number_of_rows_per_page * $this->current_page_number);
      if ($to_num > $this->number_of_rows) {
        $to_num = $this->number_of_rows;
      }

      if ($to_num == 0) {
        $from_num = 0;
      } else {
        $from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

        $from_num++;
      }

      return sprintf($text_output, $from_num, $to_num, $this->number_of_rows);
    }

  }
