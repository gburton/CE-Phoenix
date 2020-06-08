<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE);

  require $oscTemplate->map_to_template('template_top.php', 'component');
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
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
