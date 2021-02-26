<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $hooks->register_pipeline('checkout');
  foreach ($hooks->generate(null, 'startApplication') as $result) {
    if (is_string($result)) {
      $result = [ $result ];
    }
    
    if (is_array($result)) {
      foreach ($result as $path) {
        if (is_string($path ?? null) && file_exists($path)) {
          require $path;
        }
      }
    }
  }
