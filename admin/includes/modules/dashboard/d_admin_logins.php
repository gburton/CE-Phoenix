<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class d_admin_logins extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_';

    public $content_width = 6;

    public function __construct() {
      parent::__construct();

      if ($this->enabled) {
        $this->content_width = (int)($this->base_constant('CONTENT_WIDTH') ?? 6);
      }
    }

    function getOutput() {
      $output = '<table class="table table-striped table-hover mb-2">';
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
            $output .= '<td>' . (($logins['success'] == '1') ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>') . ' <a href="' . tep_href_link('action_recorder.php', 'module=ar_admin_login&aID=' . (int)$logins['id']) . '">' . htmlspecialchars($logins['user_name']) . '</a></td>';
            $output .= '<td class="text-right">' . tep_date_short($logins['date_added']) . '</td>';
          $output .= '</tr>';
        }

        $output .= '</tbody>';
      $output .= '</table>';

      return $output;
    }

    protected function get_parameters() {
      return [
        'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_STATUS' => [
          'title' => 'Enable Administrator Logins Module',
          'value' => 'True',
          'desc' => 'Do you want to show the latest administrator logins on the dashboard?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_DISPLAY' => [
          'title' => 'Logins to display',
          'value' => '5',
          'desc' => 'This number of Logins will display, ordered by latest access.',
        ],
        'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_ADMIN_LOGINS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '1000',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
