<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_reviews extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PRODUCT_INFO_REVIEWS_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      $content_width = (int)MODULE_CONTENT_PRODUCT_INFO_REVIEWS_CONTENT_WIDTH;
      $item_width = (int)MODULE_CONTENT_PRODUCT_INFO_REVIEWS_CONTENT_WIDTH_EACH;

      $review_query = tep_db_query(<<<'EOSQL'
SELECT rd.*, r.*, p.*, pd.*
 FROM reviews r
   INNER JOIN reviews_description rd ON r.reviews_id = rd.reviews_id
   INNER JOIN products p ON r.products_id = p.products_id
   INNER JOIN products_description pd ON p.products_id = pd.products_id AND rd.languages_id = pd.language_id
 WHERE p.products_status = 1 AND r.reviews_status = 1 AND r.products_id = 
EOSQL
        . (int)$_GET['products_id'] . " AND rd.languages_id = " . (int)$_SESSION['languages_id']
        . " ORDER BY r." . MODULE_CONTENT_PRODUCT_INFO_REVIEWS_ORDER . " DESC LIMIT "
        . (int)MODULE_CONTENT_PRODUCT_INFO_REVIEWS_CONTENT_LIMIT);

      if (tep_db_num_rows($review_query) > 0) {
        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PRODUCT_INFO_REVIEWS_STATUS' => [
          'title' => 'Enable Reviews Module',
          'value' => 'True',
          'desc' => 'Should the reviews block be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PRODUCT_INFO_REVIEWS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PRODUCT_INFO_REVIEWS_CONTENT_WIDTH_EACH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should each Review be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PRODUCT_INFO_REVIEWS_CONTENT_LIMIT' => [
          'title' => 'Number of Reviews',
          'value' => '99',
          'desc' => 'How many reviews should be shown?',
        ],
        'MODULE_CONTENT_PRODUCT_INFO_REVIEWS_ORDER' => [
          'title' => 'Sort Order',
          'value' => 'reviews_rating',
          'desc' => 'Display Reviews by Rating (High to Low) or Date Added (New to Old)',
          'set_func' => "tep_cfg_select_option(['reviews_rating', 'date_added'], ",
        ],
        'MODULE_CONTENT_PRODUCT_INFO_REVIEWS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

