<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  class d_admin_logins {
    var $code = 'd_admin_logins';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;
    var $content_width = 6;

    function __construct() {
      $this->title = MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_TITLE;
      $this->description = MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_DESCRIPTION;

      if ( defined('MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_STATUS == 'True');
        $this->content_width = (int)MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      
      $output = null;
      
      $output .= '<table class="table table-striped table-hover mb-2">';
        $output .= '<thead class="thead-dark">';
          $output .= '<tr>';
            $output .= '<th>' . MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_TITLE . '</th>';
            $output .= '<th class="text-right">'. MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_DATE . '</th>';
          $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';
        
        $logins_query = tep_db_query("select id, user_name, success, date_added from action_recorder where module = 'ar_admin_login' order by date_added desc limit " . (int)MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_DISPLAY);
        while ($logins = tep_db_fetch_array($logins_query)) {
          $output .= '<tr>';
            $output .= '<td>' . (($logins['success'] == '1') ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>') . ' <a href="' . tep_href_link('action_recorder.php', 'module=ar_admin_login&aID=' . (int)$logins['id']) . '">' . tep_output_string_protected($logins['user_name']) . '</a></td>';
            $output .= '<td class="text-right">' . tep_date_short($logins['date_added']) . '</td>';
          $output .= '</tr>';
        }

        $output .= '</tbody>';
      $output .= '</table>';

      return $output;
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Administrator Logins Module', 'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_STATUS', 'True', 'Do you want to show the latest administrator logins on the dashboard?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Logins to display', 'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_DISPLAY', '5', 'This number of Logins will display, ordered by latest access.', '6', '2', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_CONTENT_WIDTH', '6', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_SORT_ORDER', '1000', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_STATUS', 'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_DISPLAY', 'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_CONTENT_WIDTH', 'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_SORT_ORDER');
    }
  }
  