<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

class hook_shop_siteWide_styleSheets {

  public $sitestart = null;
  
  function listen_injectSiteStart() {
    $this->sitestart .= '<!-- stylesheets hooked -->' . PHP_EOL;
    $this->sitestart .= '<style>* {min-height: 0.01px;}.form-control-feedback { position: absolute; width: auto; top: 7px; right: 45px; margin-top: 0; }@media (max-width: 575.98px) {.display-1 {font-size: 3rem;font-weight: 300;line-height: 1.0;}.display-2 {font-size: 2.75rem;font-weight: 300;line-height: 1.0;}.display-3 {font-size: 2.25rem;font-weight: 300;line-height: 1.0;}.display-4 {font-size: 1.75rem;font-weight: 300;line-height: 1.0;}h4 {font-size: 1rem;}}</style>' . PHP_EOL;

    $css_file = 'templates/' . TEMPLATE_SELECTION . '/static/user.css';
    if (file_exists($css_file)) {
      $this->sitestart .= '<link href="' . $css_file . '" rel="stylesheet">' . PHP_EOL;
    }

    return $this->sitestart;
  }

}
