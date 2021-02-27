<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_forgot_password extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_FORGOT_PASSWORD_';
    const REQUIRES = [ 'password_reset_key', 'password_reset_date', 'email_address', 'password' ];

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      $content_width = (int)MODULE_CONTENT_FORGOT_PASSWORD_CONTENT_WIDTH;

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_FORGOT_PASSWORD_STATUS' => [
          'title' => 'Enable Forgot Password Module',
          'value' => 'True',
          'desc' => 'Do you want to enable this module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_FORGOT_PASSWORD_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_FORGOT_PASSWORD_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '3000',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
