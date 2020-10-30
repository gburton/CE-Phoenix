<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $column_orderings = array_filter([
    'PRODUCT_LIST_MODEL' => " ORDER BY p.products_model%s, pd.products_name",
    'PRODUCT_LIST_NAME' => " ORDER BY pd.products_name%s",
    'PRODUCT_LIST_MANUFACTURER' => " ORDER BY m.manufacturers_name%s, pd.products_name",
    'PRODUCT_LIST_QUANTITY' => " ORDER BY p.products_quantity%s, pd.products_name",
    'PRODUCT_LIST_IMAGE' => " ORDER BY pd.products_name",
    'PRODUCT_LIST_WEIGHT' => " ORDER BY p.products_weight%s, pd.products_name",
    'PRODUCT_LIST_PRICE' => " ORDER BY final_price%s, pd.products_name",
    'PRODUCT_LIST_ID' => " ORDER BY p.products_id%s, pd.products_name",
    'PRODUCT_LIST_ORDERED' => " ORDER BY p.products_ordered%s, pd.products_name",
  ], function ($k) {
    return (constant($k) > 0);
  }, ARRAY_FILTER_USE_KEY);

  uksort($column_orderings, function ($a, $b) {
    return (constant($a) <=> constant($b));
  });

  $column_list = array_keys($column_orderings);

  if ( (isset($_GET['sort'])) && (preg_match('/^[1-9][ad]$/', $_GET['sort'])) && (substr($_GET['sort'], 0, -1) <= count($column_list)) ) {
    $sort_column = intval(substr($_GET['sort'], 0 , -1)) - 1;
  } else {
    $i = array_search(($default_column ?? 'PRODUCT_LIST_NAME'), $column_list, true);
    if (false !== $i) {
      $sort_column = $i;
      $_GET['sort'] = ($sort_column + 1) . 'a';
    }
  }

  $direction = ('d' === substr($_GET['sort'], -1)) ? ' DESC' : '';

  $parameters = [
    'column_list' => &$column_list,
    'column_orderings' => &$column_orderings,
    'default_column' => $default_column ?? null,
    'direction' => &$direction,
    'listing_sql' => &$listing_sql,
    'sort_column' => &$sort_column,
  ];
  $GLOBALS['hooks']->register_pipeline('filter', $parameters);

  if (isset($column_orderings[$column_list[$sort_column]])) {
    $listing_sql .= sprintf($column_orderings[$column_list[$sort_column]], $direction);
  }
