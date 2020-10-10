<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
  
  $page_content = $oscTemplate->getContent('shopping_cart');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('shopping_cart.php'));

  require $oscTemplate->map_to_template('template_top.php', 'component');

  if ($messageStack->size('product_action') > 0) {
    echo $messageStack->output('product_action');
  }
?>

<div class="row">
  <?php echo $page_content; ?>
</div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
