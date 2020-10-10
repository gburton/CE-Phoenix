<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class d_phoenix_addons {
    var $code = 'd_phoenix_addons';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;
    var $content_width = 6;

    function __construct() {
      $this->title = MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_TITLE;
      $this->description = MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_DESCRIPTION;

      if ( defined('MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_STATUS == 'True');
        $this->content_width = (int)MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      $feed = simplexml_load_file('http://feeds.feedburner.com/PhoenixAddons');
      
      $output = null;
      
      $output .= '<div class="table-responsive">';
        $output .= '<table class="table table-striped table-hover mb-0">';
          $output .= '<thead class="thead-dark">';
            $output .= '<tr>';
              $output .= '<th>' . tep_image('images/icon_phoenix.png', 'Phoenix') . ' ' . MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_TITLE . '</th>';
              $output .= '<th>' . MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_OWNER . '</th>';
              $output .= '<th class="text-right">' . MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_RATING . '</th>';
            $output .= '</tr>';
          $output .= '</thead>';
          $output .= '<tbody>';

          foreach ($feed->channel->item as $item) {
            if ($item->highlight == 1) {
              $output .= '<tr>';
                $output .= '<td><a href="' . $item->link . '" target="_blank">' . $item->title . '</a></td>';
                $output .= '<td>' . $item->owner . '</td>';
                $output .= '<td class="text-right">' . tep_draw_stars($item->rating) . '</td>';
              $output .= '</tr>';
            }
          }

          $output .= '</tbody>';        
        $output .= '</table>';
      $output .= '</div>';
      
      $output .= '<div class="text-right my-0 p-1">';
        $output .= '<a class="float-left" href="https://forums.oscommerce.com/clubs/1-phoenix/" target="_blank">' . tep_image('images/icon_phoenix.png', 'Phoenix') . '</a> ';
        $output .= '<a href="https://forums.oscommerce.com/clubs/1-phoenix/" target="_blank">' . MODULE_ADMIN_DASHBOARD_PHOENIX_JOIN_CLUB . '</a>';
      $output .= '</div>';
      
      $output .= tep_draw_bootstrap_button(MODULE_ADMIN_DASHBOARD_PHOENIX_VIEW_ALL, 'far fa-list-alt', tep_href_link('certified_addons.php'), null, null, 'btn btn-success btn-block mb-2');

      return $output;
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Latest Add-Ons Module', 'MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_STATUS', 'True', 'Do you want to show the latest Phoenix Club Add-Ons on the dashboard?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_CONTENT_WIDTH', '6', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())"); 
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_SORT_ORDER', '500', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_STATUS', 'MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_CONTENT_WIDTH',  'MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_SORT_ORDER');
    }
  }
  