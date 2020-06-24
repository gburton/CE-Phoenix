<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class ht_category_title extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_CATEGORY_TITLE_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $PHP_SELF, $oscTemplate, $current_category_id, $OSCOM_category;

      if ( (basename($PHP_SELF) == 'index.php') && ($current_category_id > 0) ) {
        $category_seo_title = $OSCOM_category->getData($current_category_id, 'seo_title');
        $category_name      = $OSCOM_category->getData($current_category_id, 'name');
      
        if ( tep_not_null($category_seo_title) && (MODULE_HEADER_TAGS_CATEGORY_TITLE_SEO_TITLE_OVERRIDE == 'True') ) {
          $oscTemplate->setTitle($category_seo_title . MODULE_HEADER_TAGS_CATEGORY_SEO_SEPARATOR . $oscTemplate->getTitle());
        } else {
          $oscTemplate->setTitle($category_name . MODULE_HEADER_TAGS_CATEGORY_SEO_SEPARATOR . $oscTemplate->getTitle());
        }
      }
    }

    protected function get_parameters() {
      return [
        $this->config_key_base . 'STATUS' => [
          'title' => 'Enable Category Title Module',
          'value' => 'True',
          'desc' => 'Do you want to allow category titles to be added to the page title?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        $this->config_key_base . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
        $this->config_key_base . 'SEO_TITLE_OVERRIDE' => [
          'title' => 'SEO Title Override?',
          'value' => 'True',
          'desc' => 'Do you want to allow category titles to be over-ridden by your SEO Titles (if set)?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
      ];
    }

  }
  