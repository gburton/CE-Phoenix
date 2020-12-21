<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  class product_by_id {

    public static function build($product_id, $get_parameters = null) {
      if ( empty($product_id) ) {
        return new Product();
      }

      $product_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT %s
  FROM products_description pd
    INNER JOIN products p ON pd.products_id = p.products_id
    LEFT JOIN specials s ON p.products_id = s.products_id
    LEFT JOIN (SELECT products_id, COUNT(*) AS attribute_count FROM products_attributes GROUP BY products_id) a ON p.products_id = a.products_id
  WHERE p.products_status = 1 AND p.products_id = %d AND pd.language_id = %d
EOSQL
        , Product::COLUMNS, (int)$product_id, (int)$_SESSION['languages_id']));

      if ($product = tep_db_fetch_array($product_query)) {
        if (!empty($get_parameters)) {
          $product['link'] = Product::build_link($product_id, $get_parameters);
        }

        return new Product($product);
      }

      return new Product(['status' => 0, 'id' => (int)$product_id]);
    }

  }
