<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class application_surface {

    public function __call($name, $arguments) {
      return DIR_FS_CATALOG . "includes/system/segments/application/$name.php";
    }

  }
