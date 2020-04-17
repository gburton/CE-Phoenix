<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

class hook_shop_siteWide_jQuery {
  public $version = '3.5.0';

  public $afterfooter = null;

  public function listen_injectAfterFooter() {
    $this->afterfooter .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js" integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script>' . PHP_EOL;

    return $this->afterfooter;
  }

}
