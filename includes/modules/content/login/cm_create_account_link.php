<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_create_account_link extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_CREATE_ACCOUNT_LINK_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      $content_width = (int)MODULE_CONTENT_CREATE_ACCOUNT_LINK_CONTENT_WIDTH;

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_CREATE_ACCOUNT_LINK_STATUS' => [
          'title' => 'Enable New User Module',
          'value' => 'True',
          'desc' => 'Do you want to enable the new user module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_CREATE_ACCOUNT_LINK_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_CREATE_ACCOUNT_LINK_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '2000',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
