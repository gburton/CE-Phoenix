<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class Loader {

    public function __call($name, $arguments) {
      $GLOBALS[$name] = new $name();
    }

  }
