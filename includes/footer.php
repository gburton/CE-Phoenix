<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

?>

<footer>
  <?php
  if ($oscTemplate->hasBlocks('boxes_footer')) {
    ?>
    <div class="footer">
      <div class="container-fluid row-fluid">
        <?php echo $oscTemplate->getBlocks('boxes_footer'); ?>
      </div>
    </div>
    <?php
  }
  ?>
  <div class="footer-extra">
    <div class="container-fluid row-fluid">
      <div class="col-sm-6 text-center-xs"><?php echo FOOTER_TEXT_BODY; ?></div>
      <div class="col-sm-6 text-right text-center-xs"><?php echo FOOTER_TEXT_PAYMENT_ICONS; ?></div>
    </div>
  </div>
</footer>

