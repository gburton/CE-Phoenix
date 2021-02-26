<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_pi_description extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_DESCRIPTION_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $product_info;

      $content_width = (int)MODULE_CONTENT_PI_DESCRIPTION_CONTENT_WIDTH;

      $product_description = stripslashes($product_info['products_description']);

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_DESCRIPTION_STATUS' => [
          'title' => 'Enable Description Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_DESCRIPTION_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '8',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_DESCRIPTION_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '60',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
