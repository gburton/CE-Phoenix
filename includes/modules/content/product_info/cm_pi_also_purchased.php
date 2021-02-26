<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_pi_also_purchased extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $currencies, $PHP_SELF;

      $content_width = (int)MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_CONTENT_WIDTH;
      $card_layout = IS_PRODUCT_PRODUCTS_DISPLAY_ROW;

      $orders_query = tep_db_query(<<<'EOSQL'
SELECT
  p.*, pd.*,
  IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
  IF(s.status, s.specials_new_products_price, p.products_price) AS final_price,
  p.products_quantity AS in_stock,
  IF(s.status, 1, 0) AS is_special
 FROM orders_products opa
   INNER JOIN orders_products opb ON opa.orders_id = opb.orders_id
   INNER JOIN orders o ON opb.orders_id = o.orders_id
   INNER JOIN products p ON opb.products_id = p.products_id
   LEFT JOIN specials s ON p.products_id = s.products_id
   LEFT JOIN products_description pd ON p.products_id = pd.products_id
 WHERE p.products_status = 1 AND opa.products_id != opb.products_id AND opa.products_id = 
EOSQL
        . (int)$_GET['products_id']
        . " AND pd.language_id = " . (int)$_SESSION['languages_id']
        . " GROUP BY p.products_id ORDER BY o.date_purchased DESC LIMIT " . (int)MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_CONTENT_LIMIT);
      $num_products_ordered = tep_db_num_rows($orders_query);

      if ($num_products_ordered > 0) {
        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_STATUS' => [
          'title' => 'Enable Also Purchased Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_CONTENT_LIMIT' => [
          'title' => 'Number of Products',
          'value' => '4',
          'desc' => 'How many products (maximum) should be shown?',
        ],
        'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '120',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
