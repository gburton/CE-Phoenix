<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ht_twitter_product_card extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_';

    public $group = 'header_tags';

    function execute() {
      global $PHP_SELF, $oscTemplate, $currencies;

      if ( ($PHP_SELF == 'product_info.php') && isset($_GET['products_id']) ) {
        $product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, p.products_image from products p, products_description pd where p.products_id = " . (int)$_GET['products_id'] . " and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = " . (int)$_SESSION['languages_id']);

        if ( tep_db_num_rows($product_info_query) === 1 ) {
          $product_info = tep_db_fetch_array($product_info_query);

          $data = [
            'card' => MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_TYPE,
            'title' => $product_info['products_name'],
          ];

          if ( tep_not_null(MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SITE_ID) ) {
            $data['site'] = MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SITE_ID;
          }

          if ( tep_not_null(MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_USER_ID) ) {
            $data['creator'] = MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_USER_ID;
          }

          $product_description = substr(trim(preg_replace('/\s\s+/', ' ', strip_tags($product_info['products_description']))), 0, 197);

          if ( strlen($product_description) == 197 ) {
            $product_description .= ' ..';
          }

          $data['description'] = $product_description;

          $products_image = $product_info['products_image'];

          $pi_query = tep_db_query("select image from products_images where products_id = '" . (int)$product_info['products_id'] . "' order by sort_order limit 1");

          if ( tep_db_num_rows($pi_query) === 1 ) {
            $pi = tep_db_fetch_array($pi_query);

            $products_image = $pi['image'];
          }

          $data['image'] = tep_href_link('images/' . $products_image, '', 'NONSSL', false, false);

          $result = '';

          foreach ( $data as $key => $value ) {
            $result .= '<meta name="twitter:' . tep_output_string_protected($key) . '" content="' . tep_output_string_protected($value) . '" />' . "\n";
          }

          $oscTemplate->addBlock($result, $this->group);
        }
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
