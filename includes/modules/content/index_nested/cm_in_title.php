<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_in_title extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_IN_TITLE_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      global $current_category_id, $OSCOM_category;

      $content_width = MODULE_CONTENT_IN_TITLE_CONTENT_WIDTH;

      $category_name  = $OSCOM_category->getData($current_category_id, 'name');
      $category_image = $OSCOM_category->getData($current_category_id, 'image');

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_IN_TITLE_STATUS' => [
          'title' => 'Enable Title Module',
          'value' => 'True',
          'desc' => 'Do you want to enable this module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_IN_TITLE_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_IN_TITLE_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '50',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
