<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_info_text extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_INFO_TEXT_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $page;

      $content_width = MODULE_CONTENT_INFO_TEXT_CONTENT_WIDTH;

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_INFO_TEXT_STATUS' => [
          'title' => 'Enable Text Module',
          'value' => 'True',
          'desc' => 'Should this module be shown?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_INFO_TEXT_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => 'col-12',
          'desc' => 'What width container should the content be shown in?',
        ],
        'MODULE_CONTENT_INFO_TEXT_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '20',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
