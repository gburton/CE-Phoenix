<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require $oscTemplate->map_to_template('template_top.php', 'component');

  if ($category_depth == 'nested') {

    if ($messageStack->size('product_action') > 0) {
      echo $messageStack->output('product_action');
    }
?>

<div class="contentContainer">
  <div class="row">
    <?php echo $oscTemplate->getContent('index_nested'); ?>
  </div>
</div>

<?php
  } elseif ($category_depth == 'products' || !empty($_GET['manufacturers_id'])) {

?>

<div class="contentContainer">
  <div class="row">
    <?php echo $oscTemplate->getContent('index_products'); ?>
  </div>
</div>

<?php
  } else { // default page

    if ($messageStack->size('product_action') > 0) {
      echo $messageStack->output('product_action');
    }
?>

<div class="row">
  <?php echo $oscTemplate->getContent('index'); ?>
</div>

<?php
  }

  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
