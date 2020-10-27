<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class Web {

    public static function load_xml($url) {
      if (empty($url)) {
        return;
      }

      if (ini_get('allow_url_fopen')) {
        return simplexml_load_file($url);
      }

      if (function_exists('curl_init')) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);

        return simplexml_load_string(curl_exec($ch));
      }
    }

  }
