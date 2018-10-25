<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/languages/' . $language . '/shipping.php');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('shipping.php'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">
  <?php echo TEXT_INFORMATION; ?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', tep_href_link('index.php'), null, null, 'btn-light btn-block btn-lg'); ?></div>
  </div>
</div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
