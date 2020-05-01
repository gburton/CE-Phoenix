<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

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
