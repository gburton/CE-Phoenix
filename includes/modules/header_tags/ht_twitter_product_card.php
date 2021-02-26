<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class ht_twitter_product_card extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_';

    public $group = 'header_tags';

    function execute() {
      global $product;

      if ( ('product_info.php' === $GLOBALS['PHP_SELF']) && isset($_GET['products_id'], $product) ) {
          $data = [
            'card' => MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_TYPE,
            'title' => $product->get('name'),
          ];

          if ( tep_not_null(MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SITE_ID) ) {
            $data['site'] = MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SITE_ID;
          }

          if ( tep_not_null(MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_USER_ID) ) {
            $data['creator'] = MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_USER_ID;
          }

          $product_description = substr(trim(preg_replace('/\s\s+/', ' ', strip_tags($product->get('description')))), 0, 197);
          if ( strlen($product_description) == 197 ) {
            $product_description .= ' ..';
          }

          $data['description'] = $product_description;

          $products_image = $product->get('images')[0]['image'] ?? $product->get('image');
          $data['image'] = tep_href_link('images/' . $products_image, '', 'NONSSL', false, false);

          $result = '';
          foreach ( $data as $key => $value ) {
            $result .= '<meta name="twitter:' . htmlspecialchars($key) . '" content="' . htmlspecialchars($value) . '" />' . "\n";
          }

          $GLOBALS['oscTemplate']->addBlock($result, $this->group);
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_STATUS' => [
          'title' => 'Enable Twitter Card Module',
          'value' => 'True',
          'desc' => 'Do you want to allow Twitter Card tags to be added to your product information pages?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_TYPE' => [
          'title' => 'Choose Twitter Card Type',
          'value' => 'summary_large_image',
          'desc' => 'Choose Summary or Summary Large Image.  Note that your product images MUST be at least h120px by w120px (Summary) or h150px x w280px (Summary Large Image).',
          'set_func' => "tep_cfg_select_option(['summary', 'summary_large_image'], ",
        ],
        'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_USER_ID' => [
          'title' => 'Twitter Author @username',
          'value' => '',
          'desc' => 'Your @username at Twitter',
        ],
        'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SITE_ID' => [
          'title' => 'Twitter Shop @username',
          'value' => '',
          'desc' => "Your shop's @username at Twitter (or leave blank if it is the same as your @username above).",
        ],
        'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
