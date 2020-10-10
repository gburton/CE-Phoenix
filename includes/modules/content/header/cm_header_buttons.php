<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_header_buttons extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_HEADER_BUTTONS_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      $content_width = (int)MODULE_CONTENT_HEADER_BUTTONS_CONTENT_WIDTH;

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_HEADER_BUTTONS_STATUS' => [
          'title' => 'Enable Header Buttons Module',
          'value' => 'True',
          'desc' => 'Do you want to enable the Buttons content module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_HEADER_BUTTONS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '4',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_HEADER_BUTTONS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

