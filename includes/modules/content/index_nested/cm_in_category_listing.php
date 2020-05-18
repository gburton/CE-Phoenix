<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_in_category_listing extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_IN_CATEGORY_LISTING_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      global $current_category_id, $OSCOM_category;

      $content_width  = MODULE_CONTENT_IN_CATEGORY_LISTING_CONTENT_WIDTH;
      $category_card_layout = MODULE_CONTENT_IN_CATEGORY_LISTING_LAYOUT;

      $category_name  = $OSCOM_category->getData($current_category_id, 'name');
      $category_level = $OSCOM_category->setMaximumLevel(1);
      $category_array = $OSCOM_category->buildBranchArray($current_category_id, $category_level);

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_IN_CATEGORY_LISTING_STATUS' => [
          'title' => 'Enable Sub-Category Listing Module',
          'value' => 'True',
          'desc' => 'Should this module be enabled?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_IN_CATEGORY_LISTING_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_IN_CATEGORY_LISTING_LAYOUT' => [
          'title' => 'Category Card Layout',
          'value' => 'card-deck',
          'desc' => <<<'EOD'
What Layout suits your shop?  See <a target="_blank" href="https://getbootstrap.com/docs/4.5/components/card/#card-layout"><u>card/#card-layout</u></a><div class="alert alert-warning">card-columns is a special use case that will not suit most shops as card-columns is very difficult to lay out and sort by...</div>
EOD
          ,
          'set_func' => "tep_cfg_select_option(['card-group', 'card-deck', 'card-columns'], ",
        ],
        'MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_SM' => [
          'title' => 'Items In Each Row (SM)',
          'value' => '2',
          'desc' => 'How many products should display per Row in SM (Small) viewport?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_MD' => [
          'title' => 'Items In Each Row (MD)',
          'value' => '3',
          'desc' => 'How many products should display per Row in MD (Medium) viewport?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_LG' => [
          'title' => 'Items In Each Row (LG)',
          'value' => '4',
          'desc' => 'How many products should display per Row in LG (Large) viewport?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_XL' => [
          'title' => 'Items In Each Row (XL)',
          'value' => '6',
          'desc' => 'How many products should display per Row in XL (Extra Large) viewport?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_IN_CATEGORY_LISTING_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '200',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
