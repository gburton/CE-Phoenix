<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  class d_latest_addons {
    var $code = 'd_latest_addons';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;
    var $content_width = 6;

    function __construct() {
      $this->title = MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_TITLE;
      $this->description = MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_DESCRIPTION;

      if ( defined('MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_STATUS == 'True');
        $this->content_width = (int)MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      $feed = simplexml_load_file('http://feeds.feedburner.com/osCommerce_Contributions');
      
      $output = null; $count = 0;
      
      $output .= '<table class="table table-striped table-hover mb-0">';
        $output .= '<thead class="thead-dark">';
          $output .= '<tr>';
            $output .= '<th>' . MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_TITLE . '</th>';
            $output .= '<th class="text-right">'. MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_DATE . '</th>';
          $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';

        foreach ($feed->channel->item as $item) {
          $output .= '<tr>';
            $output .= '<td><a href="' . $item->link . '" target="_blank">' . $item->title . '</a></td>';
            $output .= '<td class="text-right">' . date("F j, Y", strtotime($item->pubDate)) . '</td>';
          $output .= '</tr>';
          
          $count++;  if ($count == (int)MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_DISPLAY) break;
        }

        $output .= '</tbody>';        
      $output .= '</table>';

      $output .= '<div class="text-right my-0 mb-2 p-1">';
        $output .= '<a class="float-left" href="https://forums.oscommerce.com/clubs/1-phoenix/" target="_blank">' . tep_image('images/icon_phoenix.png', 'Phoenix') . '</a> ';
        $output .= '<a class="float-left ml-1" href="http://addons.oscommerce.com" target="_blank">' . tep_image('images/icon_oscommerce.png', MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_ICON_SITE) . '</a> ';
        $output .= '<a title="' . MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_ICON_RSS . '" href="http://feeds.feedburner.com/osCommerce_Contributions" target="_blank"><i class="fas fa-rss-square fa-2x text-warning"></i></a> ';
      $output .= '</div>';

      return $output;
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Latest Add-Ons Module', 'MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_STATUS', 'True', 'Do you want to show the latest osCommerce Add-Ons on the dashboard?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())"); 
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Addons to display', 'MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_DISPLAY', '5', 'This number of Addons will display, ordered by latest published.', '6', '2', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_CONTENT_WIDTH', '6', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_SORT_ORDER', '1200', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_STATUS', 'MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_DISPLAY', 'MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_CONTENT_WIDTH', 'MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_SORT_ORDER');
    }
  }
  