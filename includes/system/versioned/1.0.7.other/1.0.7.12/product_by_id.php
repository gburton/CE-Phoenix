<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class product_by_id {

    protected static function _build($product_id, $get_parameters, $active_only) {
      if ( empty($product_id) ) {
        return new Product();
      }

      $sql = sprintf(<<<'EOSQL'
SELECT %s
  FROM products_description pd
    INNER JOIN products p ON pd.products_id = p.products_id
    LEFT JOIN specials s ON p.products_id = s.products_id
    LEFT JOIN
      (SELECT products_id, COUNT(*) AS attribute_count
        FROM products_attributes
        GROUP BY products_id) a ON p.products_id = a.products_id
  WHERE p.products_id = %d AND pd.language_id = %d
EOSQL
        , Product::COLUMNS, (int)$product_id, (int)$_SESSION['languages_id']);

      if ($active_only) {
        $sql .= ' AND p.products_status = 1';
      }

      $product_query = tep_db_query($sql);

      if ($product = $product_query->fetch_assoc()) {
        if (!empty($get_parameters)) {
          $product['link'] = Product::build_link($product_id, $get_parameters);
        }

        return new Product($product);
      }

      return new Product(['status' => 0, 'id' => (int)$product_id]);
    }

    public static function build($product_id, $get_parameters = null) {
      return static::_build($product_id, $get_parameters, true);
    }

    public static function administer($product_id) {
      return static::_build($product_id, null, false);
    }

  }
