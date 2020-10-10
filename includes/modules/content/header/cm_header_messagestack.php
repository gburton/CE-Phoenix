<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_header_messagestack extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_HEADER_MESSAGESTACK_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      global $messageStack;
      
      $content_width = (int)MODULE_CONTENT_HEADER_MESSAGESTACK_CONTENT_WIDTH;

      if ($messageStack->size('header') > 0) {
        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_HEADER_MESSAGESTACK_STATUS' => [
          'title' => 'Enable Message Stack Notifications Module',
          'value' => 'True',
          'desc' => 'Should the Message Stack Notifications be shown in the header when needed? ',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_HEADER_MESSAGESTACK_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_HEADER_MESSAGESTACK_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

