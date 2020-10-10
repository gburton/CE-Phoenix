<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class session_eraser {

    public function __call($name, $arguments) {
      unset($_SESSION[$name]);
    }

  }
