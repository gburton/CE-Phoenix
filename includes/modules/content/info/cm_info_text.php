<?php
/*
  Copyright (c) 2020, G Burton

  This work is licensed under a
  Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License.

  You should have received a copy of the license along with this work.
  If not, see <http://creativecommons.org/licenses/by-nc-nd/4.0/>.
*/

  class cm_info_text extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_INFO_TEXT_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $page;

      $content_width = (int)MODULE_CONTENT_INFO_TEXT_CONTENT_WIDTH;

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
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_INFO_TEXT_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '20',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
