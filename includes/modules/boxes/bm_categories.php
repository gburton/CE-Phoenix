<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class bm_categories extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_BOXES_CATEGORIES_';

    function execute() {
      $display = new tree_display(Guarantor::ensure_global('category_tree'));

      $display->setPath($GLOBALS['cPath'], '<strong>', '</strong>');
      $display->setMaximumLevel((int)MODULE_BOXES_CATEGORIES_MAX_LEVEL);

      $display->setChildString('', '');

      $tpl_data = ['group' => $this->group, 'file' => __FILE__];
      include 'includes/modules/block_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_BOXES_CATEGORIES_STATUS' => [
          'title' => 'Enable Categories Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_BOXES_CATEGORIES_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement',
          'value' => 'Left Column',
          'desc' => 'Should the module be loaded in the left or right column?',
          'set_func' => "tep_cfg_select_option(['Left Column', 'Right Column'], ",
        ],
        'MODULE_BOXES_CATEGORIES_MAX_LEVEL' => [
          'title' => 'Maximum Level of Nesting',
          'value' => '1',
          'desc' => 'If you increase this number, subcategories will show in the module output.',
        ],
        'MODULE_BOXES_CATEGORIES_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

