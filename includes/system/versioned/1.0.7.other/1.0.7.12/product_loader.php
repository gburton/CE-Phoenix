<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class product_loader extends capabilities_manager {

    protected static $capabilities;

    const LISTENER_NAME = 'productCapabilities';
    const CAPABILITIES = [
      'attributes' => 'Product::load_attributes',
      'brand' => 'Product::load_brand',
      'categories' => 'Product::load_categories',
      'data_attributes' => 'Product::build_data_attributes',
      'images' => 'Product::load_images',
      'link' => 'Product::build_link',
      'notify' => 'Product::load_notify',
      'review_percentile' => 'Product::load_reviews',
      'review_rating' => 'Product::load_reviews',
      'reviews' => 'Product::load_reviews',
      'tax_rate' => 'Product::load_tax_rate',
      'translations' => 'Product::load_translations',
    ];

    public static function load_attributes($product, $language_id = null) {
      $attributes_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT po.products_options_name, pov.products_options_values_name,
   pa.options_id, pa.options_values_id, pa.price_prefix, pa.options_values_price,
   pad.products_attributes_filename, pad.products_attributes_maxdays, pad.products_attributes_maxcount
 FROM products_options po
  INNER JOIN products_attributes pa ON po.products_options_id = pa.options_id
  LEFT JOIN products_options_values pov ON pa.options_values_id = pov.products_options_values_id AND po.language_id = pov.language_id
  LEFT JOIN products_attributes_download pad ON pa.products_attributes_id = pad.products_attributes_id
 WHERE po.language_id = %d AND pa.products_id = %d
 ORDER BY po.sort_order, po.products_options_name, pov.sort_order, pov.products_options_values_name
EOSQL
      , (int)($language_id ?? $_SESSION['languages_id']), (int)$product->get('id')));

      $attributes = [];
      while ($attribute = $attributes_query->fetch_assoc()) {
        if (!isset($attributes[$attribute['options_id']])) {
          $attributes[$attribute['options_id']] = [
            'name' => $attribute['products_options_name'],
            'values' => [],
          ];
        }

        $attributes[$attribute['options_id']]['values'][$attribute['options_values_id']] = [
          'name' => $attribute['products_options_values_name'],
          'prefix' => $attribute['price_prefix'],
          'price' => $attribute['options_values_price'],
          'filename' => $attribute['products_attributes_filename'],
          'maxdays' => $attribute['products_attributes_maxdays'],
          'maxcount' => $attribute['products_attributes_maxcount'],
        ];
      }

      $product->set('has_attributes', (count($attributes) > 0) ? '1' : '0');
      $product->set('attributes', $attributes);
      return $attributes;
    }

    public static function load_brand($product) {
      if (isset($GLOBALS['brand']) && ($GLOBALS['brand']->getData('manufacturers_id') == $product->get('manufacturers_id'))) {
        $product->_data['brand'] =& $GLOBALS['brand'];
      } else {
        $product->set('brand', new manufacturer($product->get('manufacturers_id')));
      }

      return $product->get('brand');
    }

    public static function load_categories($product) {
      $categories_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT categories_id
 FROM products_to_categories
 WHERE products_id = %d
EOSQL
        , (int)$product->get('id')));

      $categories = [];
      while ($category = $categories_query->fetch_assoc()) {
        $categories[] = $category['categories_id'];
      }

      $product->set('categories', $categories);
      return $categories;
    }

    public static function load_images($product) {
      $images_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT *
 FROM products_images
 WHERE products_id = %d
 ORDER BY sort_order
EOSQL
        , (int)$product->get('id')));

      $images = [];
      while ($image = $images_query->fetch_assoc()) {
        $images[] = $image;
      }

      $product->set('images', $images);
      return $images;
    }

    public static function load_notifications($product) {
      $notifications_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT date_added FROM product_notifications WHERE products_id = %d AND customers_id = %d
EOSQL
        , (int)$product->get('id'), (int)$_SESSION['customer_id']));

      $product->set('notify', mysqli_num_rows($notifications_query));
      return $product->get('notify');
    }

    public static function load_reviews($product) {
      $reviews_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT r.*, rd.*
 FROM reviews r INNER JOIN reviews_description rd ON r.reviews_id = rd.reviews_id
 WHERE r.reviews_status = 1 AND r.products_id = %d AND rd.languages_id = %d
EOSQL
        , $product->get('id'), $_SESSION['languages_id']));

      $sum = 0;
      $reviews = [];
      while ($review_data = $reviews_query->fetch_assoc()) {
        $review = [];
        foreach ($review_data as $key => $value) {
          $trimmed_key = Text::ltrim_once($key, 'reviews_');

          $review[isset($review_data[$trimmed_key]) ? $key : $trimmed_key] = $value;
        }

        $sum += $review['rating'];
        $reviews[] = $review;
      }

      $product->set('review_percentile',
        number_format(count($reviews) ? (20 * $sum / count($reviews)) : 0, 2));
      $product->set('review_rating',
        number_format(count($reviews) ? ($sum / count($reviews)) : 0, 2));
      $product->set('reviews', $reviews);

      return $reviews;
    }

    public static function load_tax_rate($product) {
      if (isset($GLOBALS['customer'])) {
        $tax_rate = tep_get_tax_rate(
          $product->get('tax_class_id'),
          $GLOBALS['customer']->get('country_id'),
          $GLOBALS['customer']->get('zone_id'));
      } else {
        $tax_rate = tep_get_tax_rate($product->get('tax_class_id'));
      }

      $product->set('tax_rate', $tax_rate);
      return $tax_rate;
    }

    public static function load_translations($product) {
      if (!$product->get('id')) {
        return [];
      }

      $translations_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT *
 FROM products_description
 WHERE products_id = %d
EOSQL
        , $product->get('id')));
      $translations = [];
      while ($data = $translations_query->fetch_assoc()) {
        $translation = [];
        foreach ($data as $key => $value) {
          $trimmed_key = Text::ltrim_once($key, 'products_');

          $translation[isset($data[$trimmed_key]) ? $key : $trimmed_key] = $value;
        }

        $translations[$translation['language_id']] = $translation;
      }

      $product->set('translations', $translations);
      return $translations;
    }

  }
