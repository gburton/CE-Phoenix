<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

class hook_admin_siteWide_popover {

  public function listen_injectBodyEnd() {
    $popover = <<<pp
<script>
$(function () {
  $('[data-toggle="popover"]').popover({
    container: 'body', 
    trigger: 'hover click'
  })
})
</script>
pp;

    return $popover;
  }

}
