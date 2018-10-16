<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/languages/' . $language . '/ssl_check.php');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('ssl_check.php'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">
  <div class="card">
    <div class="card-header"><?php echo BOX_INFORMATION_HEADING; ?></div>
    <div class="card-body">
      <?php echo BOX_INFORMATION; ?>
    </div>
  </div>

  <div class="card text-white bg-danger">
    <div class="card-body">
      <?php echo TEXT_INFORMATION; ?>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', tep_href_link('login.php'), null, null, 'btn-light btn-block btn-lg'); ?></div>
  </div>
</div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
