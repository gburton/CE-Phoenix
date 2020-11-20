<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class d_phoenix_addons extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_';

    public $content_width = 6;

    function __construct() {
      parent::__construct();

      if ( $this->enabled ) {
        $this->content_width = (int)$this->base_constant('CONTENT_WIDTH');
      }
    }

    function getOutput() {
      $feed = Web::load_xml('https://feeds.feedburner.com/PhoenixAddons');

      $output = '<div class="table-responsive">';
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
                $output .= '<td><a href="' . $item->link . '" target="_blank" rel="noreferrer">' . $item->title . '</a></td>';
                $output .= '<td>' . $item->owner . '</td>';
                $output .= '<td class="text-right">' . tep_draw_stars($item->rating) . '</td>';
              $output .= '</tr>';
            }
          }

          $output .= '</tbody>';
        $output .= '</table>';
      $output .= '</div>';

      $output .= '<div class="text-right my-0 p-1">';
        $output .= '<a class="float-left" href="https://forums.oscommerce.com/clubs/1-phoenix/" target="_blank" rel="noreferrer">' . tep_image('images/icon_phoenix.png', 'Phoenix') . '</a> ';
        $output .= '<a href="https://forums.oscommerce.com/clubs/1-phoenix/" target="_blank" rel="noreferrer">' . MODULE_ADMIN_DASHBOARD_PHOENIX_JOIN_CLUB . '</a>';
      $output .= '</div>';

      $output .= tep_draw_bootstrap_button(MODULE_ADMIN_DASHBOARD_PHOENIX_VIEW_ALL, 'far fa-list-alt', tep_href_link('certified_addons.php'), null, null, 'btn btn-success btn-block mb-2');

      return $output;
    }

    protected function get_parameters() {
      return [
        'MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_STATUS' => [
          'title' => 'Enable Latest Add-Ons Module',
          'value' => 'True',
          'desc' => 'Do you want to show the latest Phoenix Club Add-Ons on the dashboard?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_PHOENIX_ADDONS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '500',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
