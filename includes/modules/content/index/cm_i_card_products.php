<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_i_card_products extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_CARD_PRODUCTS_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      global $new_products_category_id, $currencies, $PHP_SELF;

      $content_width = MODULE_CONTENT_CARD_PRODUCTS_CONTENT_WIDTH;
      $card_layout = IS_PRODUCT_PRODUCTS_DISPLAY_ROW;

      if ( empty($new_products_category_id) ) {
        $card_products_query = tep_db_query(<<<'EOSQL'
SELECT p.*, pd.*,
   IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
   IF(s.status, s.specials_new_products_price, p.products_price) AS final_price,
   p.products_quantity AS in_stock,
   IF(s.status, 1, 0) AS is_special
 FROM products p LEFT JOIN specials s ON p.products_id = s.products_id, products_description pd
 WHERE p.products_status = 1 AND p.products_id = pd.products_id AND pd.language_id = 
EOSQL
          . (int)$_SESSION['languages_id']
          . " ORDER BY p.products_id DESC LIMIT " . (int)MODULE_CONTENT_CARD_PRODUCTS_MAX_DISPLAY);
      } else {
        $card_products_query = tep_db_query(<<<'EOSQL'
SELECT DISTINCT p.*, pd.*,
   IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
   if(s.status, s.specials_new_products_price,
   p.products_price) AS final_price,
   p.products_quantity AS in_stock,
   if(s.status, 1, 0) AS is_special
 FROM products p LEFT JOIN specials s ON p.products_id = s.products_id
   INNER JOIN products_description pd ON p.products_id = pd.products_id
   INNER JOIN products_to_categories p2c ON p.products_id = p2c.products_id
   INNER JOIN categories c ON p2c.categories_id = c.categories_id
 WHERE p.products_status = 1 AND c.parent_id = 
EOSQL
          . (int)$new_products_category_id
          . " AND pd.language_id = ". (int)$_SESSION['languages_id']
          . " ORDER BY p.products_id DESC LIMIT " . (int)MODULE_CONTENT_CARD_PRODUCTS_MAX_DISPLAY);
      }

      $num_card_products = tep_db_num_rows($card_products_query);

      if ($num_card_products > 0) {
        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_CARD_PRODUCTS_STATUS' => [
          'title' => 'Enable New Products Module',
          'value' => 'True',
          'desc' => 'Do you want to enable this module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_CARD_PRODUCTS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_CARD_PRODUCTS_MAX_DISPLAY' => [
          'title' => 'Maximum Display',
          'value' => '6',
          'desc' => 'Maximum Number of products that should show in this module?',
        ],
        'MODULE_CONTENT_CARD_PRODUCTS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '300',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
