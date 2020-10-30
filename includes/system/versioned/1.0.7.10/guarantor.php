<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class Guarantor {

    public static function &ensure_global($class) {
      if (!(($GLOBALS[$class] ?? null) instanceof $class)) {
        $GLOBALS[$class] = new $class();
      }

      return $GLOBALS[$class];
    }

  }
