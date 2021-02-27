<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class bm_product_notifications extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_BOXES_PRODUCT_NOTIFICATIONS_';

    function execute() {
      global $PHP_SELF, $request_type;

      if (isset($_GET['products_id'])) {
        if (isset($_SESSION['customer_id'])) {
          $check_query = tep_db_query("SELECT COUNT(*) AS count FROM products_notifications WHERE products_id = " . (int)$_GET['products_id'] . " AND customers_id = " . (int)$_SESSION['customer_id']);
          $check = tep_db_fetch_array($check_query);

          $notification_exists = ($check['count'] > 0);
        } else {
          $notification_exists = false;
        }

        $tpl_data = ['group' => $this->group, 'file' => __FILE__];
        include 'includes/modules/block_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_BOXES_PRODUCT_NOTIFICATIONS_STATUS' => [
          'title' => 'Enable Product Notifications Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_BOXES_PRODUCT_NOTIFICATIONS_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement',
          'value' => 'Right Column',
          'desc' => 'Should the module be loaded in the left or right column?',
          'set_func' => "tep_cfg_select_option(['Left Column', 'Right Column'], ",
        ],
        'MODULE_BOXES_PRODUCT_NOTIFICATIONS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

