<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

?>

</div>

<footer class="pt-2">
  <div class="bg-light m-0 pt-2 pb-2">
    <div class="<?php echo BOOTSTRAP_CONTAINER; ?>">
      <div class="footer">
        <div class="row">
          <?php echo $oscTemplate->getContent('footer'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="bg-dark text-white pt-3">
    <div class="<?php echo BOOTSTRAP_CONTAINER; ?>">
      <div class="footer-extra">
        <div class="row">
          <?php echo $oscTemplate->getContent('footer_suffix'); ?>
        </div>
      </div>
    </div>
  </div>
</footer>

