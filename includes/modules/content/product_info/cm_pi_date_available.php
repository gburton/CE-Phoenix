<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_date_available extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_DATE_AVAILABLE_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $product_info;

      $content_width = (int)MODULE_CONTENT_PI_DATE_AVAILABLE_CONTENT_WIDTH;

      if ($product_info['products_date_available'] > date('Y-m-d H:i:s')) {
        $date = (MODULE_CONTENT_PI_DATE_AVAILABLE_STYLE == 'Long') ? tep_date_long($product_info['products_date_available']) : tep_date_short($product_info['products_date_available']);

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_DATE_AVAILABLE_STATUS' => [
          'title' => 'Enable Date Available Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_DATE_AVAILABLE_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_DATE_AVAILABLE_STYLE' => [
          'title' => 'Date Style',
          'value' => 'Long',
          'desc' => 'How should the date look?',
          'set_func' => "tep_cfg_select_option(['Long', 'Short'], ",
        ],
        'MODULE_CONTENT_PI_DATE_AVAILABLE_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '70',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

