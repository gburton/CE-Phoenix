<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class objectInfo {

// class constructor
    function __construct($object_array) {
		  $this->objectInfo($object_array);
		}

    function objectInfo($object_array) {
      foreach($object_array as $key => $value) {
        $this->$key = tep_db_prepare_input($value);
      }
    }
  }
?>
