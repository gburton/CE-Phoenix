<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_cs_product_notifications extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      global $order_id;

      $content_width = MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_CONTENT_WIDTH;

      if ( isset($_SESSION['customer_id']) ) {
        $global_query = tep_db_query("SELECT global_product_notifications FROM customers_info WHERE customers_info_id = " . (int)$_SESSION['customer_id']);
        $global = tep_db_fetch_array($global_query);

        if ( $global['global_product_notifications'] != '1' ) {
          if ( isset($_GET['action']) && ($_GET['action'] == 'update') ) {
            if ( !empty($_POST['notify']) && is_array($_POST['notify']) ) {
              $notify = array_unique($_POST['notify']);

              foreach ( $notify as $n ) {
                if ( is_numeric($n) && ($n > 0) ) {
                  $check_query = tep_db_query("SELECT products_id FROM products_notifications WHERE products_id = " . (int)$n . " AND customers_id = " . (int)$_SESSION['customer_id'] . " LIMIT 1");

                  if ( !tep_db_num_rows($check_query) ) {
                    tep_db_query("INSERT INTO products_notifications (products_id, customers_id, date_added) VALUES ('" . (int)$n . "', '" . (int)$_SESSION['customer_id'] . "', NOW())");
                  }
                }
              }
            }
          }

          $products_displayed = [];

          $products_query = tep_db_query("SELECT DISTINCT products_id, products_name FROM orders_products WHERE orders_id = " . (int)$order_id . " ORDER BY products_name");
          while ($products = tep_db_fetch_array($products_query)) {
            if ( !isset($products_displayed[$products['products_id']]) ) {
              $products_displayed[$products['products_id']] = $products['products_name'];
            }
          }

          $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
          include 'includes/modules/content/cm_template.php';
        }
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS' => [
          'title' => 'Enable Product Notifications Module',
          'value' => 'True',
          'desc' => 'Should the product notifications block be shown on the checkout success page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '5',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '1000',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
