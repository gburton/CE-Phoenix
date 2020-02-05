<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class bm_best_sellers extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_BOXES_BEST_SELLERS_';

    protected $group = 'boxes';

    function execute() {
      global $current_category_id, $languages_id;

      $sql = 'SELECT DISTINCT p.products_id, pd.products_name FROM products p, products_description pd';
      if (isset($current_category_id) && ($current_category_id > 0)) {
        $sql .= ", products_to_categories p2c, categories c WHERE p.products_id = p2c.products_id AND p2c.categories_id = c.categories_id AND "
              . (int)$current_category_id . " IN (c.categories_id, c.parent_id) AND";
      } else {
        $sql .= ' WHERE';
      }
      $sql .= " p.products_status = 1 AND p.products_ordered > 0 AND p.products_id = pd.products_id AND pd.language_id = " . (int)$languages_id . " ORDER BY p.products_ordered DESC, pd.products_name LIMIT " . MODULE_BOXES_BEST_SELLERS_MAX_DISPLAY;

      $best_sellers_query = tep_db_query($sql);
      if (tep_db_num_rows($best_sellers_query) >= MODULE_BOXES_BEST_SELLERS_MIN_DISPLAY) {
        $best_sellers = [];

        while ($best_seller = tep_db_fetch_array($best_sellers_query)) {
          $best_sellers[] = [
            'link' => tep_href_link('product_info.php', 'products_id=' . (int)$best_seller['products_id']),
            'text' => $best_seller['products_name'],
          ];
        }

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/block_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_BOXES_BEST_SELLERS_STATUS' => [
          'title' => 'Enable Best Sellers Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_BOXES_BEST_SELLERS_MIN_DISPLAY' => [
          'title' => 'Minimum to Display',
          'value' => '1',
          'desc' => 'Minimum number of best sellers to make the box display',
        ],
        'MODULE_BOXES_BEST_SELLERS_MAX_DISPLAY' => [
          'title' => 'Maximum Display',
          'value' => '10',
          'desc' => 'Maximum number of best sellers to display in the box',
        ],
        'MODULE_BOXES_BEST_SELLERS_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement',
          'value' => 'Right Column',
          'desc' => 'Should the module be loaded in the left or right column?',
          'set_func' => "tep_cfg_select_option(['Left Column', 'Right Column'], ",
        ],
        'MODULE_BOXES_BEST_SELLERS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
