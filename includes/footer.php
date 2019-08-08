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

<footer>
  <div class="jumbotron jumbotron-fluid jumbotron-footer m-0 pt-1 pb-1 border-top border-light">
    <div class="<?php echo BOOTSTRAP_CONTAINER; ?>">
      <div class="footer">
        <div class="row">
          <?php echo $oscTemplate->getContent('footer'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-extra">
    <div class="<?php echo BOOTSTRAP_CONTAINER; ?>">
      <div class="row">
        <?php echo $oscTemplate->getContent('footer_suffix'); ?>
      </div>
    </div>
  </div>
</footer>

