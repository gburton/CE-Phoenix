<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  foreach ($GLOBALS['order']->products as $product) {
// Update products_ordered (for bestsellers list)
    tep_db_query("UPDATE products SET products_ordered = products_ordered + " . sprintf('%d', $product['qty']) . " WHERE products_id = '" . tep_get_prid($product['id']) . "'");
  }
