<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ht_product_opengraph extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_';

    public $group = 'header_tags';

    function execute() {
      global $product_info, $currencies;

      if (isset($product_info['products_name'])) {
        $data = [
          'og:type' => 'product',
          'og:title' => $product_info['products_name'],
          'og:site_name' => STORE_NAME,
        ];

        $product_description = substr(trim(preg_replace('/\s\s+/', ' ', strip_tags($product_info['products_description']))), 0, 197) . '...';
        $data['og:description'] = $product_description;

        $pi_query = tep_db_query("SELECT image FROM products_images WHERE products_id = " . (int)$product_info['products_id'] . " ORDER BY sort_order LIMIT 1");
        $pi = tep_db_fetch_array($pi_query);
        $products_image = $pi['image'] ?? $product_info['products_image'];
        $data['og:image'] = tep_href_link("images/$products_image", '', 'NONSSL', false, false);

        if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
          $products_price = $currencies->display_raw($new_price, tep_get_tax_rate($product_info['products_tax_class_id']));
        } else {
          $products_price = $currencies->display_raw($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
        }
        $data['product:price:amount'] = $products_price;
        $data['product:price:currency'] = $_SESSION['currency'];

        $data['og:url'] = tep_href_link('product_info.php', 'products_id=' . $product_info['products_id'], 'NONSSL', false);

        $data['product:availability'] = ( $product_info['products_quantity'] > 0 ) ? MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_TEXT_IN_STOCK : MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_TEXT_OUT_OF_STOCK;

        $result = '';
        foreach ( $data as $property => $content ) {
          $result .= '<meta property="' . tep_output_string_protected($property) . '" content="' . tep_output_string_protected($content) . '" />' . PHP_EOL;
        }

        $GLOBALS['oscTemplate']->addBlock($result, $this->group);
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_STATUS' => [
          'title' => 'Enable Product OpenGraph Module',
          'value' => 'True',
          'desc' => 'Do you want to allow Open Graph Meta Tags (good for Facebook and Pinterest and other sites) to be added to your product page?  Note that your product thumbnails MUST be at least 200px by 200px.',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '900',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }