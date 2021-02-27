<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

class hook_shop_siteWide_noJs {
  var $bodywrapperstart = null;

  function listen_injectBodyWrapperStart() {
    $msg = TEXT_NOSCRIPT;

    $this->bodywrapperstart .= <<<eod
<!-- noJs hooked -->
<noscript>
  <div class="alert alert-danger text-center">{$msg}</div>
  <div class="w-100"></div>
</noscript>
eod;

    return $this->bodywrapperstart;
  }

}
