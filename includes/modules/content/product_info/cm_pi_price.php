<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_pi_price extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_PRICE_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $product_info, $currencies;

      $content_width = (int)MODULE_CONTENT_PI_PRICE_CONTENT_WIDTH;

      $products_price = $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
      $specials_price = null;

      if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
        $specials_price = $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id']));
      }

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_PRICE_STATUS' => [
          'title' => 'Enable Price Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_PRICE_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '3',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_PRICE_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '50',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
