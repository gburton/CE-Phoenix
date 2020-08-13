<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class info_pages {
    /* 
    Example getContainer
    $pages = info_pages::getContainer(['pd.languages_id' => '1',                                       
                                       'p.pages_status' => '1']);

    Makes array of pages in english (1) where the page status is active (1)
    */
    public static function getContainer($container = []) {
      global $languages_id; $pages_arr = [];

      $pages_query_raw = "select * from pages p left join pages_description pd on p.pages_id = pd.pages_id where 1=1 ";
      if ( sizeof($container) > 0 ) {
        foreach ($container as $k => $v) {
          $pages_query_raw .= "AND $k = '$v' ";
        }
      }
      $pages_query_raw .= "order by p.sort_order";

      $pages_query = tep_db_query($pages_query_raw);

      while($pages = tep_db_fetch_array($pages_query)) {
        $pages_arr[] = $pages;
      }

      return $pages_arr;
    }

    /*
    Example getElement
    $pages = info_pages::getElement(['p.slug' => 'privacy',
                                     'pd.languages_id' => '1'], 'pages_text');

    Get the Text of the privacy page in the english language (1)
    */
    public static function getElement($container = [], $element = null) {
      if ( (sizeof($container) > 0) && (tep_not_null($element)) ) {
        $page_query_raw = "select $element from pages p left join pages_description pd on p.pages_id = pd.pages_id where 1=1 ";
        foreach ($container as $k => $v) {
          $page_query_raw .= "AND $k = '$v' ";
        }

        $page_query = tep_db_query($page_query_raw);

        $page = tep_db_fetch_array($page_query);

        return $page[$element];
      }
    }
    
    public static function get_page($arr) {
      $page_arr = info_pages::getContainer($arr);

      // will always be the first and only item in the returned array
      return $page_arr[0];
    }

    public static function get_pages($order_by = null) {
      global $languages_id; $pages_arr = [];
      
      $sort_order = $order_by ?? 'p.sort_order';

      $pages_query = tep_db_query("select * from pages p left join pages_description pd on p.pages_id = pd.pages_id where pd.languages_id = '" . (int)$languages_id . "' order by $sort_order");
      while($pages = tep_db_fetch_array($pages_query)) {
        $pages_arr[] = $pages;
      }

      // may be 1 or more pages
      return $pages_arr;
    }

    public static function split_page_results() {
      global $languages_id, $pages_query_numrows;

      $pages_query_raw = "select * from pages p left join pages_description pd on p.pages_id = pd.pages_id where pd.languages_id = '" . (int)$languages_id . "' order by p.last_modified DESC, p.pages_id DESC";
      $pages_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $pages_query_raw, $pages_query_numrows);

      return $pages_split;
    }
    
    public static function requirements() {
      $required_slugs = ['conditions', 'privacy', 'shipping']; $db_slugs = [];
      
      $slugs_query = tep_db_query("select slug from pages order by slug");
      while ($slugs = tep_db_fetch_array($slugs_query)) {
        $db_slugs[] = $slugs['slug'];
      }
      
      $missing_requirements = array_diff($required_slugs, $db_slugs);
      
      return $missing_requirements;
    }

  }
