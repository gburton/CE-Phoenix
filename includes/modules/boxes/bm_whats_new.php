<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class bm_whats_new extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_BOXES_WHATS_NEW_';

    protected $group = 'boxes';

    function execute() {
      $random_query = tep_db_query("SELECT products_id FROM products WHERE products_status = 1 ORDER BY products_id DESC LIMIT " . (int)MODULE_BOXES_WHATS_NEW_MAX_RANDOM_SELECT_NEW);
      $num_rows = tep_db_num_rows($random_query);
      if (!$num_rows) {
        return;
      }

      tep_db_data_seek($random_query, tep_rand(0, ($num_rows - 1)));
      $random_selection = tep_db_fetch_array($random_query);

      $product = product_by_id::build((int)$random_selection['products_id']);

      $box = [
        'parameters' => ['product_card.php', 'component'],
        'classes' => 'is-product bm-whats-new',
        'title' => sprintf(MODULE_BOXES_WHATS_NEW_BOX_TITLE, tep_href_link('products_new.php')),
        'attributes' => $product->get('data_attributes'),
      ];

      $tpl_data = [
        'group' => $this->group,
        'file' => 'box.php',
        'type' => 'component',
      ];
      include 'includes/modules/block_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_BOXES_WHATS_NEW_STATUS' => [
          'title' => 'Enable Best Sellers Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_BOXES_WHATS_NEW_MAX_RANDOM_SELECT_NEW' => [
          'title' => 'Selection of Random New Products',
          'value' => '4',
          'desc' => 'Select one random product from the last X (the number you insert here) added products.',
        ],
        'MODULE_BOXES_WHATS_NEW_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement',
          'value' => 'Right Column',
          'desc' => 'Should the module be loaded in the left or right column?',
          'set_func' => "tep_cfg_select_option(['Left Column', 'Right Column'], ",
        ],
        'MODULE_BOXES_WHATS_NEW_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '5015',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
