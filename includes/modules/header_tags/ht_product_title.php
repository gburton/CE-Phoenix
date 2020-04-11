<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ht_product_title extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_PRODUCT_TITLE_';

    protected $group = 'header_tags';

    function execute() {
      global $PHP_SELF, $oscTemplate, $product_check;

      if (basename($PHP_SELF) == 'product_info.php') {
        if (isset($_GET['products_id']) && ($product_check['total'] > 0)) {
          $product_info_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT pd.products_name, pd.products_seo_title
 FROM products p INNER JOIN products_description pd ON pd.products_id = p.products_id
 WHERE p.products_status = 1 AND p.products_id = %d AND pd.language_id = %d
EOSQL
            , (int)$_GET['products_id'], (int)$_SESSION['languages_id']));
          $product_info = tep_db_fetch_array($product_info_query);

          if ( tep_not_null($product_info['products_seo_title']) && (MODULE_HEADER_TAGS_PRODUCT_TITLE_SEO_TITLE_OVERRIDE == 'True') ) {
            $oscTemplate->setTitle($product_info['products_seo_title'] . MODULE_HEADER_TAGS_PRODUCT_SEO_SEPARATOR . $oscTemplate->getTitle());
          } else {
            $oscTemplate->setTitle($product_info['products_name'] . MODULE_HEADER_TAGS_PRODUCT_SEO_SEPARATOR . $oscTemplate->getTitle());
          }
        }
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_PRODUCT_TITLE_STATUS' => [
          'title' => 'Enable Product Title Module',
          'value' => 'True',
          'desc' => 'Do you want to allow product titles to be added to the page title?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_PRODUCT_TITLE_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
        'MODULE_HEADER_TAGS_PRODUCT_TITLE_SEO_TITLE_OVERRIDE' => [
          'title' => 'SEO Title Override?',
          'value' => 'True',
          'desc' => 'Do you want to allow product titles to be over-ridden by your SEO Titles (if set)?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_PRODUCT_TITLE_SEO_BREADCRUMB_OVERRIDE' => [
          'title' => 'SEO Breadcrumb Override?',
          'value' => 'True',
          'desc' => 'Do you want to allow product names in the breadcrumb to be over-ridden by your SEO Titles (if set)?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
      ];
    }

  }
