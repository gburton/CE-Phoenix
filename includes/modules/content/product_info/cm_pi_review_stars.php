<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_pi_review_stars extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_REVIEW_STARS_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $product_info;

      $pid = (int)$product_info['products_id'];
      $review_link = tep_href_link('ext/modules/content/reviews/write.php', "products_id=$pid");

      $review_average_query = tep_db_query("SELECT AVG(reviews_rating) AS average, COUNT(reviews_rating) AS count FROM reviews WHERE products_id = " . $pid . " and reviews_status = 1");
      $review_average = tep_db_fetch_array($review_average_query);

      $review_stars_array = [];
      if ($review_average['count'] > 0) {
        $review_stars_array[] = tep_draw_stars((int)$review_average['average']);

        if ((int)$review_average['count'] == 1) {
          $review_stars_array[] = sprintf(MODULE_CONTENT_PI_REVIEW_STARS_COUNT_ONE, (int)$review_average['count']);
        } else {
          $review_stars_array[] = sprintf(MODULE_CONTENT_PI_REVIEW_STARS_COUNT, (int)$review_average['count']);
        }

        $do_review = MODULE_CONTENT_PI_REVIEW_STARS_DO_REVIEW;
      } else {
        $review_stars_array[] = sprintf(MODULE_CONTENT_PI_REVIEW_STARS_COUNT, 0);

        $do_review = MODULE_CONTENT_PI_REVIEW_STARS_DO_FIRST_REVIEW;
      }

      $content_width = (int)MODULE_CONTENT_PI_REVIEW_STARS_CONTENT_WIDTH;

      $tpl_data = ['group' => $this->group, 'file' => __FILE__];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_REVIEW_STARS_STATUS' => [
          'title' => 'Enable Review Stars/Link Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_REVIEW_STARS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_REVIEW_STARS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '55',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
