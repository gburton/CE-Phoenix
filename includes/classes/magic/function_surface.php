<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class function_surface {

    public function __call($name, $arguments) {
      return DIR_FS_CATALOG . "includes/functions/$name.php";
    }

  }
