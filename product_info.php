<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  if (!isset($_GET['products_id'])) {
    tep_redirect(tep_href_link('index.php'));
  }

  require "includes/languages/$language/product_info.php";

  $product_info_query = tep_db_query("SELECT p.*, pd.* FROM products p, products_description pd where p.products_status = 1 and p.products_id = " . (int)$_GET['products_id'] . " and pd.products_id = p.products_id and pd.language_id = " . (int)$languages_id);
  if ($product_info = tep_db_fetch_array($product_info_query)) {
    tep_db_query("UPDATE products_description SET products_viewed = products_viewed+1 WHERE products_id = " . (int)$_GET['products_id'] . " and language_id = " . (int)$languages_id);

    require $oscTemplate->map_to_template(__FILE__, 'page');
  } else {
    require $oscTemplate->map_to_template('product_info_not_found.php', 'page');
  }

  require 'includes/application_bottom.php';
