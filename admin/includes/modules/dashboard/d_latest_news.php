<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class d_latest_news extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_ADMIN_DASHBOARD_LATEST_NEWS_';

    public $content_width = 6;

    function __construct() {
      parent::__construct();

      if ( $this->enabled ) {
        $this->content_width = (int)MODULE_ADMIN_DASHBOARD_LATEST_NEWS_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      $feed = Web::load_xml('https://feeds.feedburner.com/osCommerceNewsAndBlogs');

      $output = '<table class="table table-striped table-hover mb-0">';
        $output .= '<thead class="thead-dark">';
          $output .= '<tr>';
            $output .= '<th>' . MODULE_ADMIN_DASHBOARD_LATEST_NEWS_TITLE . '</th>';
            $output .= '<th class="text-right">'. MODULE_ADMIN_DASHBOARD_LATEST_NEWS_DATE . '</th>';
          $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';
        
        $count = 0;
        foreach ($feed->channel->item as $item) {
          $output .= '<tr>';
            $output .= '<td><a href="' . $item->link . '" target="_blank">' . $item->title . '</a></td>';
            $output .= '<td class="text-right">' . date("F j, Y", strtotime($item->pubDate)) . '</td>';
          $output .= '</tr>';

          $count++;
          if ($count == (int)MODULE_ADMIN_DASHBOARD_LATEST_NEWS_DISPLAY) {
            break;
          }
        }

        $output .= '</tbody>';
      $output .= '</table>';

      $output .= '<div class="text-right my-0 mb-2 p-1">';
        $output .= '<a class="float-left" href="https://forums.oscommerce.com/clubs/1-phoenix/" target="_blank">' . tep_image('images/icon_phoenix.png', 'Phoenix') . '</a> ';
        $output .= '<a class="float-left ml-1" href="http://www.oscommerce.com/Us&News" target="_blank">' . tep_image('images/icon_oscommerce.png', MODULE_ADMIN_DASHBOARD_LATEST_NEWS_ICON_NEWS) . '</a> ';
        $output .= '<a title="' . MODULE_ADMIN_DASHBOARD_LATEST_NEWS_ICON_NEWSLETTER . '" href="http://www.oscommerce.com/newsletter/subscribe" target="_blank"><i class="fas fa-envelope-square fa-2x text-muted"></i></a> ';
        $output .= '<a title="' . MODULE_ADMIN_DASHBOARD_LATEST_NEWS_ICON_FACEBOOK . '" href="http://www.facebook.com/pages/osCommerce/33387373079" target="_blank"><i class="fab fa-facebook-square fa-2x text-info"></i></a> ';
        $output .= '<a title="' . MODULE_ADMIN_DASHBOARD_LATEST_NEWS_ICON_TWITTER . '" href="http://twitter.com/osCommerce" target="_blank"><i class="fab fa-twitter-square fa-2x text-primary"></i></a> ';
        $output .= '<a title="' . MODULE_ADMIN_DASHBOARD_LATEST_NEWS_ICON_RSS . '" href="http://feeds.feedburner.com/osCommerceNewsAndBlogs" target="_blank"><i class="fas fa-rss-square fa-2x text-warning"></i></a> ';
      $output .= '</div>';

      return $output;
    }

    protected function get_parameters() {
      return [
        'MODULE_ADMIN_DASHBOARD_LATEST_NEWS_STATUS' => [
          'title' => 'Enable Latest News Module',
          'value' => 'True',
          'desc' => 'Do you want to show the latest osCommerce News on the dashboard?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_LATEST_NEWS_DISPLAY' => [
          'title' => 'Items to display',
          'value' => '5',
          'desc' => 'This number of items will display, ordered by latest published.',
        ],
        'MODULE_ADMIN_DASHBOARD_LATEST_NEWS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_LATEST_NEWS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '700',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
