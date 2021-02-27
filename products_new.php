<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  require language::map_to_translation('products_new.php');

  $listing_sql = sprintf(<<<'EOSQL'
SELECT p.*, pd.*, m.*,
  IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
  IF(s.status, s.specials_new_products_price, p.products_price) AS final_price,
  p.products_quantity AS in_stock,
  IF(s.status, 1, 0) AS is_special
 FROM
  products_description pd,
  products p
    LEFT JOIN manufacturers m ON p.manufacturers_id = m.manufacturers_id
    LEFT JOIN specials s ON p.products_id = s.products_id
 WHERE p.products_status = 1 AND p.products_id = pd.products_id AND pd.language_id = %d
EOSQL
    , (int)$_SESSION['languages_id']);

  $default_column = 'PRODUCT_LIST_ID';
  $sort_order = 'd';

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
