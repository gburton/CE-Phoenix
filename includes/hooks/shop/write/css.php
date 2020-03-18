<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

class hook_shop_write_css {
  
  function listen_injectSiteEnd() {
    $reviews_css = '<style>.rating > input { display: none; } .rating > label:before { content: "\f005"; margin: 2px; font-size: 1.5em; font-family: "Font Awesome 5 Free"; display: inline-block; } .rating > label { color: #ccc; } .rating > input:checked ~ label { color: #ffca08; font-weight: 900; } .rating > input:hover ~ label { color: #ffca08; cursor: pointer; font-weight: 900; }</style>';

    return $reviews_css;
  }
  
}
