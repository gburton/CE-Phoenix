<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

class hook_admin_categories_focusRequiredTab {

  public function listen_injectSiteEnd() {
    $focusTab = <<<'ft'
<script>
$(function () {
  $('button[type="submit"]').click(function() {
    var id = $('.tab-pane').find(':required:invalid').closest('.tab-pane').attr('id');

    $('.nav a[href="#' + id + '"]').tab('show'); $('.tab-pane').find(':required:invalid').closest('.collapse').addClass('show');

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) { $("form[name='new_product']")[0].reportValidity(); })
  })
})
</script>
ft;

    return $focusTab;
  }

}
