<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

class hook_admin_siteWide_tooltip {

  public function listen_injectBodyEnd() {
    $tooltip = <<<tt
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
tt;

    return $tooltip;
  }

}
