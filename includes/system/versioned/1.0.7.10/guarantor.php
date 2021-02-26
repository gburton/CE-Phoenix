<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class Guarantor {

    public static function &ensure_global($class, ...$parameters) {
      if (!(($GLOBALS[$class] ?? null) instanceof $class)) {
        $GLOBALS[$class] = new $class(...$parameters);
      }

      return $GLOBALS[$class];
    }

  }
