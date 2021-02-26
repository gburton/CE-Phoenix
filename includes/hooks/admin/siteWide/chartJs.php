<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

class hook_admin_siteWide_chartJs {
  var $version = '2.8.0';

  var $sitestart = null;

  function listen_injectSiteStart() {
    global $PHP_SELF;
    
    if (basename($PHP_SELF == 'index.php')) {
      $this->sitestart .= '<!-- chartJs Hooked -->' . PHP_EOL;
      $this->sitestart .= '<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>' . PHP_EOL;

      return $this->sitestart;
    }
  }
  
}
