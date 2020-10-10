<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_cas_continue_button extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_CAS_CONTINUE_BUTTON_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      $content_width = (int)MODULE_CONTENT_CAS_CONTINUE_BUTTON_CONTENT_WIDTH;

      $origin_href = $_SESSION['navigation']->pop_snapshot_as_link();

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_CAS_CONTINUE_BUTTON_STATUS' => [
          'title' => 'Enable Button Module',
          'value' => 'True',
          'desc' => 'Do you want to enable this module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_CAS_CONTINUE_BUTTON_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_CAS_CONTINUE_BUTTON_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '30',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
