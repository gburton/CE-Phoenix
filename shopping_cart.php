<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require("includes/application_top.php");
  
  $page_content = $oscTemplate->getContent('shopping_cart');

  require('includes/languages/' . $language . '/shopping_cart.php');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('shopping_cart.php'));

  require('includes/template_top.php');

  if ($messageStack->size('product_action') > 0) {
    echo $messageStack->output('product_action');
  }
?>

<div class="row">
  <?php echo $page_content; ?>
</div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
