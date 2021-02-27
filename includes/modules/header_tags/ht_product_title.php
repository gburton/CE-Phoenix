<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class ht_product_title extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_PRODUCT_TITLE_';

    protected $group = 'header_tags';

    function execute() {
      global $oscTemplate, $product_info;

      if (isset($_GET['products_id'], $product_info['products_name']) && (basename($GLOBALS['PHP_SELF']) == 'product_info.php')) {
        if ( tep_not_null($product_info['products_seo_title']) && (MODULE_HEADER_TAGS_PRODUCT_TITLE_SEO_TITLE_OVERRIDE === 'True') ) {
          $oscTemplate->setTitle($product_info['products_seo_title'] . MODULE_HEADER_TAGS_PRODUCT_SEO_SEPARATOR . $oscTemplate->getTitle());
        } else {
          $oscTemplate->setTitle($product_info['products_name'] . MODULE_HEADER_TAGS_PRODUCT_SEO_SEPARATOR . $oscTemplate->getTitle());
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
      ];
    }

  }
