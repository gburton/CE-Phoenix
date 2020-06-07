<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class osC_Actions {

    public static function parse($action) {
      $action = basename($action);

      if ( $action && class_exists($class = 'osC_Actions_' . $action) ) {
        call_user_func([$class, 'execute']);
      }
    }

  }
