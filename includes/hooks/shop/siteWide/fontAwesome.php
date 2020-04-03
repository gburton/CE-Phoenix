<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

class hook_shop_siteWide_fontAwesome {
  public $version = '5.13.0';

  public $sitestart = null;

  public function listen_injectSiteStart() {
    $this->sitestart .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" integrity="sha256-h20CPZ0QyXlBuAw7A+KluUYx/3pK+c7lYEpqLTlxjYQ=" crossorigin="anonymous" />' . PHP_EOL;

    return $this->sitestart;
  }

}
