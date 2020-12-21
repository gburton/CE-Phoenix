<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  class product_builder extends product_loader {

    const COLUMNS = <<<'EOSQL'
pd.*, p.*,
    IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
    IF(s.status, s.specials_new_products_price, p.products_price) AS base_price,
    p.products_quantity AS in_stock,
    IF(s.status, 1, 0) AS is_special,
    IF(s.status, s.expires_date, NULL) AS special_expiration,
    IF(COALESCE(a.attribute_count, 0) > 0, 1, 0) AS has_attributes
EOSQL;

    public static function build_link($product, $parameters = '') {
      $product_id = is_numeric($product) ? $product : $product->get('id');
      return tep_href_link('product_info.php', "{$parameters}products_id=" . (int)$product_id);
    }

    public static function build_data_attributes($product, $data = []) {
      $data['data-is-special'] = $product->get('is_special');
      $data['data-product-price'] = $product->format_raw();
      $data['data-product-manufacturer'] = $product->get('manufacturers_id');

      $product->set('data_attributes', implode(array_map(function ($key, $value) {
        return ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
      }, array_keys($data), $data)));

      return $product->get('data_attributes');
    }

    public static function build_prid($uprid) {
      $pieces = explode('{', $uprid);
      return is_numeric($pieces[0]) ? (int)$pieces[0] : false;
    }

    public static function build_uprid($id, $params) {
      if (is_numeric($id)) {
        $uprid = (int)$id;

        if (is_array($params)) {
          foreach ($params as $option => $value) {
            if (!is_numeric($option) || !is_numeric($value)) {
              return (int)$id;
            }

            $uprid .= '{' . (int)$option . '}' . (int)$value;
          }
        }
      } else {
        $first_bracket = strpos($id, '{');
        if ((false === $first_bracket) || !is_numeric($prid = Product::build_prid($id))) {
          return false;
        }

        $uprid = $prid;

// strpos()+1 to remove up to and including the first { which would create an empty array element in explode()
        foreach (explode('{', substr($id, $first_bracket + 1)) as $attribute) {
          $pair = explode('}', $attribute, 2);

          if (!is_numeric($pair[0]) || !is_numeric($pair[1])) {
            return $prid;
          }

          $uprid .= '{' . (int)$pair[0] . '}' . (int)$pair[1];
        }
      }

      return $uprid;
    }

    public static function fetch_name($product_id, $language_id = null) {
      if (empty($language_id)) {
        $language_id = $_SESSION['languages_id'];
      }

      $product_query = tep_db_query("SELECT products_name FROM products_description WHERE products_id = " . (int)$product_id . " AND language_id = " . (int)$language_id);
      $product = tep_db_fetch_array($product_query);

      return $product['products_name'];
    }

  }
