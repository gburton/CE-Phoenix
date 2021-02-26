<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class ht_product_opengraph extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_';

    public $group = 'header_tags';

    function execute() {
      global $product;

      if (isset($product) && is_callable([$product, 'get']) && $product->get('status')) {
        $data = [
          'og:type' => 'product',
          'og:title' => $product->get('name'),
          'og:site_name' => STORE_NAME,
        ];

        $product_description = substr(trim(preg_replace('/\s\s+/', ' ', strip_tags($product->get('description')))), 0, 197) . '...';
        $data['og:description'] = $product_description;

        $images = $product->get('images');
        $products_image = $images[0]['image'] ?? $product->get('image');
        $data['og:image'] = tep_href_link("images/$products_image", '', 'NONSSL', false, false);

        $data['product:price:amount'] = $product->format_raw();
        $data['product:price:currency'] = $_SESSION['currency'];

        $data['og:url'] = tep_href_link('product_info.php', 'products_id=' . $product->get('id'), 'NONSSL', false);

        $data['product:availability'] = ( $product->get('in_stock') > 0 ) ? MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_TEXT_IN_STOCK : MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_TEXT_OUT_OF_STOCK;

        $result = '';
        foreach ( $data as $property => $content ) {
          $result .= '<meta property="' . htmlspecialchars($property) . '" content="' . htmlspecialchars($content) . '" />' . PHP_EOL;
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