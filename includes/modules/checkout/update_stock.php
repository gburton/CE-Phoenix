<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  if (STOCK_LIMITED == 'true') {
    foreach ($GLOBALS['order']->products as $product) {
// Stock Update - Joao Correia
      if (DOWNLOAD_ENABLED == 'true') {
        $stock_query_raw = <<<'EOSQL'
SELECT products_quantity, pad.products_attributes_filename
 FROM products p
   LEFT JOIN products_attributes pa ON p.products_id=pa.products_id
   LEFT JOIN products_attributes_download pad ON pa.products_attributes_id=pad.products_attributes_id
 WHERE p.products_id = '
EOSQL
        . tep_get_prid($product['id']) . "'";

// Will work with only one option for downloadable products
// otherwise, we have to build the query dynamically with a loop
        $products_attributes = $product['attributes'] ?? '';
        if (is_array($products_attributes)) {
          $stock_query_raw .= " AND pa.options_id = " . (int)$products_attributes[0]['option_id'] . " AND pa.options_values_id = " . (int)$products_attributes[0]['value_id'];
        }
        $stock_query = tep_db_query($stock_query_raw);
      } else {
        $stock_query = tep_db_query("SELECT products_quantity FROM products WHERE products_id = '" . tep_get_prid($product['id']) . "'");
      }

      if ($stock_values = tep_db_fetch_array($stock_query)) {
        // do not decrement quantities if products_attributes_filename exists
        if ((DOWNLOAD_ENABLED != 'true') || (!$stock_values['products_attributes_filename'])) {
          $stock_left = $stock_values['products_quantity'] - $product['qty'];
          tep_db_query("UPDATE products SET products_quantity = " . (int)$stock_left . " WHERE products_id = '" . tep_get_prid($product['id']) . "'");
          if ( ($stock_left < 1) && (STOCK_ALLOW_CHECKOUT == 'false') ) {
            tep_db_query("UPDATE products SET products_status = '0' WHERE products_id = '" . tep_get_prid($product['id']) . "'");
          }
        }
      }
    }
  }
