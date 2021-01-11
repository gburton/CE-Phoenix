<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  class random_review {

    public static function build() {
      $random_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT RAND() * COUNT(*) AS `offset`
  FROM reviews r
   INNER JOIN reviews_description rd ON r.reviews_id = rd.reviews_id
   INNER JOIN products p ON p.products_id = r.products_id
   INNER JOIN products_description pd ON p.products_id = pd.products_id AND rd.languages_id = pd.language_id
  WHERE p.products_status = 1 AND r.reviews_status = 1 AND rd.languages_id = %d
  ORDER BY r.reviews_id DESC
EOSQL
        , (int)$_SESSION['languages_id']));

      $random_selection = $random_query->fetch_assoc();
      if (!$random_selection) {
        return false;
      }

      $product_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT %s,
    SUBSTRING(rd.reviews_text, 1, 60) AS reviews_text,
    r.reviews_rating
  FROM reviews r
    INNER JOIN reviews_description rd ON r.reviews_id = rd.reviews_id
    INNER JOIN products p ON p.products_id = r.products_id
    INNER JOIN products_description pd ON p.products_id = pd.products_id AND rd.languages_id = pd.language_id
    LEFT JOIN specials s ON p.products_id = s.products_id
    LEFT JOIN (SELECT products_id, COUNT(*) AS attribute_count FROM products_attributes GROUP BY products_id) a ON p.products_id = a.products_id
  WHERE p.products_status = 1 AND pd.language_id = %d
  ORDER BY r.reviews_id DESC LIMIT 1 OFFSET %d
EOSQL
        , Product::COLUMNS, (int)$_SESSION['languages_id'], (int)$random_selection['offset']));

      if ($product = $product_query->fetch_assoc()) {
        return new Product($product);
      }

      return false;
    }

  }
