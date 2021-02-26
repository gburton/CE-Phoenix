<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_ip_category_manufacturer_description extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_IP_CATEGORY_DESCRIPTION_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $current_category_id, $category_tree, $brand;

      if (($brand ?? null) instanceof manufacturer) {
        $cm_description = $brand->getData('manufacturers_description');
      } else {
        $cm_description = $category_tree->get($current_category_id, 'description');
      }

      if (tep_not_null($cm_description)) {
        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_IP_CATEGORY_DESCRIPTION_STATUS' => [
          'title' => 'Enable Category/Manufacturer Description Module',
          'value' => 'True',
          'desc' => 'Should this module be enabled?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_IP_CATEGORY_DESCRIPTION_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_IP_CATEGORY_DESCRIPTION_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '100',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
