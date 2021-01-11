<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  class random_special {

    public static function build() {
      $random_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT RAND() * COUNT(*) AS `offset`
  FROM products p
   INNER JOIN products_description pd ON p.products_id = pd.products_id
   INNER JOIN specials s ON pd.products_id = s.products_id
  WHERE p.products_status = 1 AND s.status = 1 AND pd.language_id = %d
  ORDER BY s.specials_id DESC
EOSQL
        , (int)$_SESSION['languages_id']));

      $random_selection = $random_query->fetch_assoc();
      if (!$random_selection) {
        return false;
      }

      $product_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT pd.*, p.*, s.*,
    s.specials_new_products_price AS base_price,
    p.products_quantity AS in_stock,
    1 AS is_special,
    IF(COALESCE(a.attribute_count, 0) > 0, 1, 0) AS has_attributes
  FROM products p
    INNER JOIN products_description pd ON p.products_id = pd.products_id
    INNER JOIN specials s ON pd.products_id = s.products_id
    LEFT JOIN (SELECT products_id, COUNT(*) AS attribute_count FROM products_attributes GROUP BY products_id) a ON p.products_id = a.products_id
  WHERE p.products_status = 1 AND s.status = 1 AND pd.language_id = %d
  ORDER BY s.specials_id DESC LIMIT 1 OFFSET %d
EOSQL
        , (int)$_SESSION['languages_id'], (int)$random_selection['offset']));

      if ($product = $product_query->fetch_assoc()) {
        return new Product($product);
      }

      return false;
    }

  }
