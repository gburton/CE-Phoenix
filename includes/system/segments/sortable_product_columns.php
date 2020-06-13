<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  // create column list
  $define_list = [
    'PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
    'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
    'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
    'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
    'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
    'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
    'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
    'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW,
    'PRODUCT_LIST_ID' => PRODUCT_LIST_ID,
    'PRODUCT_LIST_ORDERED' => PRODUCT_LIST_ORDERED,
  ];

  asort($define_list);

  $column_list = [];
  foreach ($define_list as $key => $value) {
    if ($value > 0) {
      $column_list[] = $key;
    }
  }

  if ( (!isset($_GET['sort'])) || (!preg_match('/^[1-9][ad]$/', $_GET['sort'])) || (substr($_GET['sort'], 0, -1) > count($column_list)) ) {
    $i = array_search(($default_column ?? 'PRODUCT_LIST_NAME'), $column_list, true);
    if (false !== $i) {
      $sort_col = $i+1;
      $_GET['sort'] = $sort_col . ($sort_order ?? 'a');
    }
  } else {
    $sort_col = substr($_GET['sort'], 0 , -1);
  }

  $sort_order = substr($_GET['sort'], -1);

  switch ($column_list[$sort_col-1]) {
    case 'PRODUCT_LIST_MODEL':
      $listing_sql .= " ORDER BY p.products_model " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
      break;
    case 'PRODUCT_LIST_NAME':
      $listing_sql .= " ORDER BY pd.products_name " . ($sort_order == 'd' ? 'DESC' : '');
      break;
    case 'PRODUCT_LIST_MANUFACTURER':
      $listing_sql .= " ORDER BY m.manufacturers_name " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
      break;
    case 'PRODUCT_LIST_QUANTITY':
      $listing_sql .= " ORDER BY p.products_quantity " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
      break;
    case 'PRODUCT_LIST_IMAGE':
      $listing_sql .= " ORDER BY pd.products_name";
      break;
    case 'PRODUCT_LIST_WEIGHT':
      $listing_sql .= " ORDER BY p.products_weight " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
      break;
    case 'PRODUCT_LIST_PRICE':
      $listing_sql .= " ORDER BY final_price " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
      break;
    case 'PRODUCT_LIST_ID':
      $listing_sql .= " ORDER BY p.products_id " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
      break;
    case 'PRODUCT_LIST_ORDERED':
      $listing_sql .= " ORDER BY p.products_ordered " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
      break;
  }
