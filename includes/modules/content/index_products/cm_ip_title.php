<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_ip_title extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_IP_TITLE_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $current_category_id, $category_tree, $brand;

      $content_width = MODULE_CONTENT_IP_TITLE_CONTENT_WIDTH;

      if (($brand ?? null) instanceof manufacturer) {
        $cm_name  = $brand->getData('manufacturers_name');
        $cm_image = $brand->getData('manufacturers_image');
      } else {
        $cm_name  = $category_tree->get($current_category_id, 'name');
        $cm_image = $category_tree->get($current_category_id, 'image');
      }

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';

    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_IP_TITLE_STATUS' => [
          'title' => 'Enable Title Module',
          'value' => 'True',
          'desc' => 'Do you want to enable this module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_IP_TITLE_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_IP_TITLE_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '50',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
