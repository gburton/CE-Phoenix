<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

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
