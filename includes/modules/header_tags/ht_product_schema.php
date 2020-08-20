<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ht_product_schema extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_PRODUCT_SCHEMA_';

    public function __construct() {
      parent::__construct(__FILE__);

      if (static::get_constant('MODULE_HEADER_TAGS_PRODUCT_SCHEMA_PLACEMENT') !== 'Header') {
        $this->group = 'footer_scripts';
      }
    }

    function execute() {
      global $product_info, $currencies;

      if (isset($product_info['products_name'])) {
        $products_image = $product_info['products_image'];
        $pi_query = tep_db_query("SELECT image FROM products_images WHERE products_id = " . (int)$product_info['products_id'] . " ORDER BY sort_order LIMIT 1");
        if ( $pi = tep_db_fetch_array($pi_query) ) {
          $products_image = $pi['image'];
        }

        $schema_product = [
          '@context'    => 'https://schema.org',
          '@type'       => 'Product',
          'name'        => tep_db_output($product_info['products_name']),
          'image'       => tep_href_link('images/' . $products_image, '', 'NONSSL', false, false),
          'url'         => tep_href_link('product_info.php', 'products_id=' . $product_info['products_id'], 'NONSSL', false, false),
          'description' => substr(trim(preg_replace('/\s\s+/', ' ', strip_tags($product_info['products_description']))), 0, 197) . '...',
        ];

        if (tep_not_null($product_info['products_model'] ?? null)) {
          $schema_product['mpn'] = tep_db_output($product_info['products_model']);
        }

        if (tep_not_null($product_info['products_gtin'] ?? null) && defined('MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH')) {
          $schema_product['gtin' .  MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH] = tep_db_output(substr($product_info['products_gtin'], 0-MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH));
        }

        $schema_product['offers'] = [
          '@type'         => 'Offer',
          'priceCurrency' => $_SESSION['currency'],
        ];

        if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
          $products_price = $currencies->display_raw($new_price, tep_get_tax_rate($product_info['products_tax_class_id']));
        } else {
          $products_price = $currencies->display_raw($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
        }

        $schema_product['offers']['price'] = $products_price;

        $specials_expiry_query = tep_db_query("SELECT expires_date FROM specials WHERE status = 1 AND products_id = " . (int)$product_info['products_id']);
        if ($specials_expiry = tep_db_fetch_array($specials_expiry_query)) {
          $schema_product['offers']['priceValidUntil'] = $specials_expiry['expires_date'];
        }

        $availability = ( $product_info['products_quantity'] > 0 ) ? MODULE_HEADER_TAGS_PRODUCT_SCHEMA_TEXT_IN_STOCK : MODULE_HEADER_TAGS_PRODUCT_SCHEMA_TEXT_OUT_OF_STOCK;
        $schema_product['offers']['availability'] = $availability;

        $schema_product['offers']['seller'] = [
          '@type' => 'Organization',
          'name'  => STORE_NAME,
        ];

        if (($product_info['manufacturers_id'] ?? 0) > 0) {
          // manufacturer class
          $ht_brand = new manufacturer((int)$product_info['manufacturers_id']);

          $schema_product['manufacturer'] = [
            '@type' => 'Organization',
            'name'  => tep_db_output($ht_brand->getData('manufacturers_name')),
          ];
        }

        $average_query = tep_db_query(<<<'EOSQL'
SELECT AVG(r.reviews_rating) AS average, COUNT(r.reviews_rating) AS count
 FROM reviews r
 where r.reviews_status = 1 AND r.products_id = 
EOSQL
          . (int)$product_info['products_id']);
        $average = tep_db_fetch_array($average_query);
        if ($average['count'] > 0) {
          $star_rating = round($average['average'], 0, PHP_ROUND_HALF_UP);
          $schema_product['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => number_format($star_rating, 2),
            'reviewCount' => (int)$average['count'],
          ];

          $reviews_query = tep_db_query(<<<'EOSQL'
SELECT rd.reviews_text, r.reviews_rating, r.reviews_id, r.customers_name, r.date_added, r.reviews_read
 FROM reviews r INNER JOIN reviews_description rd ON r.reviews_id = rd.reviews_id
 WHERE r.reviews_status = 1 AND r.products_id = 
EOSQL
            . (int)$_GET['products_id'] . " AND rd.languages_id = " . (int)$_SESSION['languages_id'] . " ORDER BY r.reviews_rating DESC");

          if (tep_db_num_rows($reviews_query) > 0) {
            $schema_product['review'] = [];
            while ($reviews = tep_db_fetch_array($reviews_query)) {
              $schema_product['review'][] = [
                '@type'         => 'Review',
                'author'        => tep_db_output($reviews['customers_name']),
                'datePublished' => tep_db_output($reviews['date_added']),
                'description'   => tep_db_output($reviews['reviews_text']),
                'name'          => tep_db_output($product_info['products_name']),
                'reviewRating'  => [
                  '@type'       => 'Rating',
                  'bestRating'  => '5',
                  'ratingValue' => (int)$reviews['reviews_rating'],
                  'worstRating' => '1',
                ],
              ];
            }
          }
        }

        $data = json_encode($schema_product);

        $GLOBALS['oscTemplate']->addBlock('<script type="application/ld+json">' . $data . '</script>', $this->group);
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_PRODUCT_SCHEMA_STATUS' => [
          'title' => 'Enable Product Schema Module',
          'value' => 'True',
          'desc' => 'Do you want to allow product schema to be added to your product page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_PRODUCT_SCHEMA_PLACEMENT' => [
          'title' => 'Placement',
          'value' => 'Header',
          'desc' => 'Where should the code be placed?',
          'set_func' => "tep_cfg_select_option(['Header', 'Footer'], ",
        ],
        'MODULE_HEADER_TAGS_PRODUCT_SCHEMA_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '950',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

