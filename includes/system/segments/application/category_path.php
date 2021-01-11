<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  if (isset($_GET['products_id'])) {
    $product = product_by_id::build((int)$_GET['products_id']);
  }

// calculate category path
  if (isset($_GET['cPath'])) {
    $cPath_array = Guarantor::ensure_global('category_tree')->parse_path($_GET['cPath']);
    $current_category_id = end($cPath_array);
  } elseif (isset($_GET['manufacturers_id'])) {
    $brand = new manufacturer((int)$_GET['manufacturers_id']);
  } elseif (isset($product) && $product->get('status')) {
    $current_category_id = $product->get('categories')[0] ?? 0;
    $cPath_array = array_reverse(
      Guarantor::ensure_global('category_tree')->get_ancestors($current_category_id));
    $cPath_array[] = $current_category_id;
  }

  if (!isset($current_category_id)) {
    $current_category_id = 0;
  }

  $cPath = isset($cPath_array) ? implode('_', $cPath_array) : '';

  if (isset($category_tree)) {
    $OSCOM_category = &$category_tree;
  }
