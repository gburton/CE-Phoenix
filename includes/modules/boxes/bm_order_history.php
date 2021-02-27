<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class bm_order_history extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_BOXES_ORDER_HISTORY_';

    function execute() {
      global $PHP_SELF;

      if (isset($_SESSION['customer_id'])) {
// retrieve the last x products purchased
        $products_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT DISTINCT op.products_id, pd.products_name
 FROM orders o
   INNER JOIN orders_products op ON o.orders_id = op.orders_id
   INNER JOIN products p ON op.products_id = p.products_id
   INNER JOIN products_description pd ON p.products_id = pd.products_id
 WHERE p.products_status = 1 AND o.customers_id = %d AND pd.language_id = %d
 GROUP BY products_id
 ORDER BY o.date_purchased DESC
 LIMIT %d
EOSQL
          , (int)$_SESSION['customer_id'], (int)$_SESSION['languages_id'], (int)MODULE_BOXES_ORDER_HISTORY_MAX_DISPLAY_PRODUCTS));

        if (tep_db_num_rows($products_query)) {
          $tpl_data = ['group' => $this->group, 'file' => __FILE__];
          include 'includes/modules/block_template.php';
        }
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_BOXES_ORDER_HISTORY_STATUS' => [
          'title' => 'Enable Order History Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_BOXES_ORDER_HISTORY_MAX_DISPLAY_PRODUCTS' => [
          'title' => 'Maximum Products to show',
          'value' => '6',
          'desc' => 'Maximum number of products to display in the customer order history box',
        ],
        'MODULE_BOXES_ORDER_HISTORY_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement',
          'value' => 'Right Column',
          'desc' => 'Should the module be loaded in the left or right column?',
          'set_func' => "tep_cfg_select_option(['Left Column', 'Right Column'], ",
        ],
        'MODULE_BOXES_ORDER_HISTORY_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

