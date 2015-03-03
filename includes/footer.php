<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

?>

  <footer id="modular-footer" class="<?php echo BOOTSTRAP_CONTAINER; ?>">
    <div id="footer" class="row">
      <?php echo $oscTemplate->getContent('footer'); ?>
    </div>
      
    <div id="footer-extra" class="row">
      <?php echo $oscTemplate->getContent('footer_suffix'); ?>
    </div>
  </footer>
