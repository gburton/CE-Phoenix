<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ht_product_meta extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_PRODUCT_META_';

    protected $group = 'header_tags';

    function execute() {
      global $PHP_SELF, $oscTemplate, $product_check;

      if (isset($_GET['products_id'])) {
        if ($product_check['total'] > 0) {
          $meta_info_query = tep_db_query("select pd.products_seo_description, pd.products_seo_keywords from products p, products_description pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
          $meta_info = tep_db_fetch_array($meta_info_query);

          if (tep_not_null($meta_info['products_seo_description'])) {
            $oscTemplate->addBlock('<meta name="description" content="' . tep_output_string($meta_info['products_seo_description']) . '" />' . PHP_EOL, $this->group);
          }
          if ((tep_not_null($meta_info['products_seo_keywords'])) && (MODULE_HEADER_TAGS_PRODUCT_META_KEYWORDS_STATUS != 'Search') ) {
            $oscTemplate->addBlock('<meta name="keywords" content="' . tep_output_string($meta_info['products_seo_keywords']) . '" />' . PHP_EOL, $this->group);
          }
        }
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_PRODUCT_META_STATUS' => [
          'title' => 'Enable Product Meta Module',
          'value' => 'True',
          'desc' => 'Do you want to allow product meta tags to be added to the page header?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_PRODUCT_META_KEYWORDS_STATUS' => [
          'title' => 'Enable Product Meta Module - Keywords',
          'value' => 'Search',
          'desc' => 'Keywords can be used for META, for SEARCH, or for BOTH.  If you are into the Chinese Market select Both (for Baidu Search Engine) otherwise select Search.',
          'set_func' => "tep_cfg_select_option(['Meta', 'Search', 'Both'], ",
        ],
        'MODULE_HEADER_TAGS_PRODUCT_META_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
