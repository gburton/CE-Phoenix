<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  class d_reviews {
    var $code = 'd_reviews';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;
    var $content_width = 6;

    function __construct() {
      $this->title = MODULE_ADMIN_DASHBOARD_REVIEWS_TITLE;
      $this->description = MODULE_ADMIN_DASHBOARD_REVIEWS_DESCRIPTION;

      if ( defined('MODULE_ADMIN_DASHBOARD_REVIEWS_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_DASHBOARD_REVIEWS_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_REVIEWS_STATUS == 'True');
        $this->content_width = (int)MODULE_ADMIN_DASHBOARD_REVIEWS_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      global $languages_id;
      
      $output = null;
      
      $output .= '<div class="table-responsive">';
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

          $reviews_query = tep_db_query("select r.reviews_id, r.date_added, pd.products_name, r.customers_name, r.reviews_rating, r.reviews_status from reviews r, products_description pd where pd.products_id = r.products_id and pd.language_id = '" . (int)$languages_id . "' order by r.date_added desc limit " . (int)MODULE_ADMIN_DASHBOARD_REVIEWS_DISPLAY);
          while ($reviews = tep_db_fetch_array($reviews_query)) {
            $status_icon = ($reviews['reviews_status'] == '1') ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>';
            $output .= '<tr>';
              $output .= '<td><a href="' . tep_href_link('reviews.php', 'rID=' . (int)$reviews['reviews_id'] . '&action=edit') . '">' . $reviews['products_name'] . '</a></td>';
              $output .= '<td>' . tep_date_short($reviews['date_added']) . '</td>';
              $output .= '<td>' . tep_output_string_protected($reviews['customers_name']) . '</td>';
              $output .= '<td>' . tep_draw_stars($reviews['reviews_rating']) . '</td>';
              $output .= '<td class="text-right">' . $status_icon . '</td>';
            $output .= '</tr>';
          }

          $output .= '</tbody>';
        $output .= '</table>';
      $output .= '</div>';

      return $output;
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_DASHBOARD_REVIEWS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Reviews Module', 'MODULE_ADMIN_DASHBOARD_REVIEWS_STATUS', 'True', 'Do you want to show the latest reviews on the dashboard?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Reviews to display', 'MODULE_ADMIN_DASHBOARD_REVIEWS_DISPLAY', '5', 'This number of Reviews will display, ordered by latest added.', '6', '2', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_ADMIN_DASHBOARD_REVIEWS_CONTENT_WIDTH', '6', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())"); 
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_DASHBOARD_REVIEWS_SORT_ORDER', '800', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_ADMIN_DASHBOARD_REVIEWS_STATUS', 'MODULE_ADMIN_DASHBOARD_REVIEWS_DISPLAY', 'MODULE_ADMIN_DASHBOARD_REVIEWS_CONTENT_WIDTH', 'MODULE_ADMIN_DASHBOARD_REVIEWS_SORT_ORDER');
    }
  }
  