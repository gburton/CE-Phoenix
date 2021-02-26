<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_pi_gtin extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PRODUCT_INFO_GTIN_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $product_info;

      $content_width = (int)MODULE_CONTENT_PRODUCT_INFO_GTIN_CONTENT_WIDTH;

      if (tep_not_null($product_info['products_gtin'])) {
        $gtin = substr($product_info['products_gtin'], -MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH);

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PRODUCT_INFO_GTIN_STATUS' => [
          'title' => 'Enable GTIN Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PRODUCT_INFO_GTIN_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH' => [
          'title' => 'Length of GTIN',
          'value' => '13',
          'desc' => 'Length of GTIN. 14 (Industry Standard), 13 (eg ISBN codes and EAN UCC-13), 12 (UPC), 8 (EAN UCC-8)',
          'set_func' => "tep_cfg_select_option(['14', '13', '12', '8'], ",
        ],
        'MODULE_CONTENT_PRODUCT_INFO_GTIN_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }
  }
