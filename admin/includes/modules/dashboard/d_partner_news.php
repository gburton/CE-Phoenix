<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class d_partner_news extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_ADMIN_DASHBOARD_PARTNER_NEWS_';

    public $content_width = 6;

    function __construct() {
      parent::__construct();

      if ( $this->enabled ) {
        $this->content_width = (int)($this->base_constant('CONTENT_WIDTH') ?? 6);
      }

      if ( !function_exists('json_decode') ) {
        $this->description .= '<p style="color: #ff0000; font-weight: bold;">' . MODULE_ADMIN_DASHBOARD_PARTNER_NEWS_ERROR_JSON_DECODE . '</p>';

        $this->enabled = false;
      }
    }

    function getOutput() {
      $result = $this->_getContent();

      $output = '';

      if (is_array($result) && !empty($result)) {
        $output .= '<table class="table table-striped table-hover mb-0">';
          $output .= '<thead class="thead-dark">';
            $output .= '<tr>';
              $output .= '<th>' . MODULE_ADMIN_DASHBOARD_PARTNER_NEWS_TITLE . '</th>';
            $output .= '</tr>';
          $output .= '</thead>';
          $output .= '<tbody>';

            foreach ($result as $p) {
              $output .= '<tr>';
                $output .= '<td><h6 class="m-0"><a href="' . $p['url'] . '" target="_blank" rel="noreferrer">' . $p['title'] . '</strong></a> <small>(' . $p['category_title'] . ')</small></h6>' . $p['status_update'] . '</td>';
              $output .= '</tr>';
            }

          $output .= '</tbody>';
        $output .= '</table>';

        $output .= '<div class="text-right my-0 mb-2 p-1">';
          $output .= '<a class="float-left" href="https://forums.oscommerce.com/clubs/1-phoenix/" target="_blank" rel="noreferrer">' . tep_image('images/icon_phoenix.png', 'Phoenix') . '</a> ';
          $output .= '<a href="http://www.oscommerce.com/Services" target="_blank" rel="noreferrer">' . MODULE_ADMIN_DASHBOARD_PARTNER_NEWS_MORE_TITLE . '</a>';
        $output .= '</div>';
      }

      return $output;
    }

    function _getContent() {
      $result = null;

      $filename = DIR_FS_CACHE . 'oscommerce_partners_news.cache';

      if ( file_exists($filename) ) {
        $difference = floor((time() - filemtime($filename)) / 60);

        if ( $difference < 60 ) {
          $result = unserialize(file_get_contents($filename));
        }
      }

      if ( !isset($result) ) {
        if (function_exists('curl_init')) {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, 'http://www.oscommerce.com/index.php?RPC&Website&Index&GetPartnerStatusUpdates');
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $response = trim(curl_exec($ch));
          curl_close($ch);

          if (!empty($response)) {
            $result = trim($response);
          }
        } else {
          if ($fp = @fsockopen('www.oscommerce.com', 80, $errno, $errstr, 30)) {
            $header = 'GET /index.php?RPC&Website&Index&GetPartnerStatusUpdates HTTP/1.0' . "\r\n" .
                      'Host: www.oscommerce.com' . "\r\n" .
                      'Connection: close' . "\r\n\r\n";

            fwrite($fp, $header);

            $response = '';
            while (!feof($fp)) {
              $response .= fgets($fp, 1024);
            }

            fclose($fp);

            $response = explode("\r\n\r\n", $response); // split header and content

            if (isset($response[1]) && !empty($response[1])) {
              $result = trim($response[1]);
            }
          }
        }

        if ( !empty($result) ) {
          $result = json_decode($result, true);

          if ( is_writable(DIR_FS_CACHE) ) {
            file_put_contents($filename, serialize($result), LOCK_EX);
          }
        }
      }

      return $result;
    }

    protected function get_parameters() {
      return [
        'MODULE_ADMIN_DASHBOARD_PARTNER_NEWS_STATUS' => [
          'title' => 'Enable Partner News Module',
          'value' => 'True',
          'desc' => 'Do you want to show the latest osCommerce Partner News on the dashboard?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_PARTNER_NEWS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_ADMIN_DASHBOARD_PARTNER_NEWS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '1100',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
