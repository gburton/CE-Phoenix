<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  require $oscTemplate->map_to_template('template_top.php', 'component');

  if ($category_depth == 'nested') {

    if ($messageStack->size('product_action') > 0) {
      echo $messageStack->output('product_action');
    }
?>

  <div class="row">
    <?php echo $oscTemplate->getContent('index_nested'); ?>
  </div>

<?php
  } elseif ($category_depth == 'products' || !empty($_GET['manufacturers_id'])) {

?>

  <div class="row">
    <?php echo $oscTemplate->getContent('index_products'); ?>
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
