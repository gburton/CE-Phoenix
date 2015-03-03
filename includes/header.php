<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  if ($messageStack->size('header') > 0) { 
     echo $messageStack->output('header'); 
  }
?>

  <header id="modular-header" class="<?php echo BOOTSTRAP_CONTAINER; ?>">
    <div id="header" class="row">
      <?php echo $oscTemplate->getContent('header'); ?>
    </div>
  </header>
