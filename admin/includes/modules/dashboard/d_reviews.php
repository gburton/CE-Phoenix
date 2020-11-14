<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class d_reviews extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_ADMIN_DASHBOARD_REVIEWS_';

    public $content_width = 6;

    public function __construct() {
      parent::__construct();

      if ($this->enabled) {
        $this->content_width = (int)($this->base_constant('CONTENT_WIDTH') ?? 6);
      }
    }

    function getOutput() {
      $reviews_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT r.reviews_id, r.date_added, pd.products_name, r.customers_name, r.reviews_rating, r.reviews_status
 FROM reviews r, products_description pd
 WHERE pd.products_id = r.products_id and pd.language_id = %d
 ORDER BY r.date_added DESC
 LIMIT %d
EOSQL
        , (int)$_SESSION['languages_id'], (int)MODULE_ADMIN_DASHBOARD_REVIEWS_DISPLAY));


      $output = '<div class="table-responsive">';
        $output .= '<table class="table table-striped table-hover mb-2">';
          $output .= '<thead class="thead-dark">';
            $output .= '<tr>';
              $output .= '<th>' . MODULE_ADMIN_DASHBOARD_REVIEWS_TITLE . '</th>';
              $output .= '<th>' . MODULE_ADMIN_DASHBOARD_REVIEWS_DATE . '</th>';
              $output .= '<th>' . MODULE_ADMIN_DASHBOARD_REVIEWS_REVIEWER . '</th>';
              $output .= '<th>' . MODULE_ADMIN_DASHBOARD_REVIEWS_RATING . '</th>';
              $output .= '<th class="text-right">' . MODULE_ADMIN_DASHBOARD_REVIEWS_REVIEW_STATUS . '</th>';
            $output .= '</tr>';
          $output .= '</thead>';
          $output .= '<tbody>';

          while ($reviews = tep_db_fetch_array($reviews_query)) {
            $status_icon = ($reviews['reviews_status'] == '1') ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>';
            $output .= '<tr>';
              $output .= '<td><a href="' . tep_href_link('reviews.php', 'rID=' . (int)$reviews['reviews_id'] . '&action=edit') . '">' . $reviews['products_name'] . '</a></td>';
              $output .= '<td>' . tep_date_short($reviews['date_added']) . '</td>';
              $output .= '<td>' . htmlspecialchars($reviews['customers_name']) . '</td>';
              $output .= '<td>' . tep_draw_stars($reviews['reviews_rating']) . '</td>';
              $output .= '<td class="text-right">' . $status_icon . '</td>';
            $output .= '</tr>';
          }

          $output .= '</tbody>';
        $output .= '</table>';
      $output .= '</div>';

      return $output;
    }

    protected function get_parameters() {
      return [
        $this->config_key_base . 'STATUS' => [
          'title' => 'Enable Reviews Module',
          'value' => 'True',
          'desc' => 'Do you want to show the latest reviews on the dashboard?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        $this->config_key_base . 'DISPLAY' => [
          'title' => 'Reviews to display',
          'value' => '5',
          'desc' => 'This number of Reviews will display, ordered by latest added.',
        ],
        $this->config_key_base . 'CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        $this->config_key_base . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '800',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }
  }
