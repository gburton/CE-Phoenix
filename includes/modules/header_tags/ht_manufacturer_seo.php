<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ht_manufacturer_seo extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_MANUFACTURERS_SEO_';

    protected $group = 'header_tags';

    function execute() {
      global $PHP_SELF, $oscTemplate, $brand;

      if (basename($PHP_SELF) == 'index.php') {
        if (isset($_GET['manufacturers_id']) && is_numeric($_GET['manufacturers_id'])) {
          $brand_seo_description = $brand->getData('manufacturers_seo_description');

          if (tep_not_null($brand_seo_description)) {
            $oscTemplate->addBlock('<meta name="description" content="' . tep_output_string($brand_seo_description) . '" />' . PHP_EOL, $this->group);
          }
        }
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_MANUFACTURERS_SEO_STATUS' => [
          'title' => 'Enable Manufacturer Meta Module',
          'value' => 'True',
          'desc' => 'Do you want to allow Manufacturer meta tags to be added to the page header?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_MANUFACTURERS_SEO_DESCRIPTION_STATUS' => [
          'title' => 'Display Manufacturer Meta Description',
          'value' => 'True',
          'desc' => 'Manufacturer Descriptions help your site and your sites visitors.',
          'set_func' => "tep_cfg_select_option(['True'], ",
        ],
        'MODULE_HEADER_TAGS_MANUFACTURERS_SEO_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '110',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
