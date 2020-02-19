<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  require "includes/languages/$language/logoff.php";

  $breadcrumb->add(NAVBAR_TITLE);

  unset($_SESSION['customer_id']);

  unset($_SESSION['sendto']);
  unset($_SESSION['billto']);
  unset($_SESSION['shipping']);
  unset($_SESSION['payment']);
  unset($_SESSION['comments']);

  $cart->reset();

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">
  <div class="alert alert-danger" role="alert">
    <?php echo TEXT_MAIN; ?>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', tep_href_link('index.php'), null, null, 'btn-danger btn-lg btn-block'); ?></div>
  </div>
</div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
