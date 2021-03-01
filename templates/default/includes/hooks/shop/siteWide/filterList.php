<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

class hook_shop_siteWide_filterList {
  var $siteend = null;

  function listen_injectSiteEnd() {
    $filterListScript = <<<eod
<script>
var filter = $('.filter-list');
$('div.alert-filters > ul.nav').append($('<ul>').attr('class','nav ml-auto').append($('<li>').append(filter)));
</script>
eod;

    $this->siteend .= '<!-- filterlist hooked -->' . PHP_EOL;
    $this->siteend .= $filterListScript . PHP_EOL;

    return $this->siteend;
  }
  
}
