<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

class hook_admin_siteWide_styleSheets {
  
  function listen_injectSiteStart() {
    $admin_css = '<!-- stylesheets hooked -->' . PHP_EOL;
    $admin_css .= '<style>* {min-height: 0.01px;} .form-control-feedback { position: absolute; width: auto; top: 7px; right: 45px; margin-top: 0; } @media (min-width: 768px) { .w-md-25 { width: 25% !important; } .w-md-50 { width: 50% !important; } .w-md-75 { width: 75% !important; } .w-md-100 { width: 100% !important; } .w-md-auto { width: auto !important; } }</style>' . PHP_EOL;
    $admin_css .= '<link href="includes/stylesheet.css" rel="stylesheet">' . PHP_EOL;

    return $admin_css;
  }

}
