<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class nb_brand extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_NAVBAR_BRAND_';

    public $group = 'navbar_modules_left';

    function getOutput() {
      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/block_template.php';
    }

    public function get_parameters() {
      return [
        'MODULE_NAVBAR_BRAND_STATUS' => [
          'title' => 'Enable Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your Navbar?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_NAVBAR_BRAND_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement Group',
          'value' => 'Home',
          'desc' => 'This is a special module that must be placed in the Home Group.  Lowest is loaded first, per Group.',
          'set_func' => "tep_cfg_select_option(['Home'], ",
        ],
        'MODULE_NAVBAR_BRAND_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '505',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
