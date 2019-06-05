<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
?>

      </div> <!-- bodyContent //-->

<?php
  if ( $oscTemplate->hasBlocks('boxes_column_left') && ($oscTemplate->getGridColumnWidth() > 0) ) {
?>

      <div id="columnLeft" class="col-md-<?php echo $oscTemplate->getGridColumnWidth(); ?> order-xs-6 order-md-1">
        <?php echo $oscTemplate->getBlocks('boxes_column_left'); ?>
      </div>

<?php
  }

  if ( $oscTemplate->hasBlocks('boxes_column_right') && ($oscTemplate->getGridColumnWidth() > 0) ) {
?>

      <div id="columnRight" class="col-md-<?php echo $oscTemplate->getGridColumnWidth(); ?> order-last">
        <?php echo $oscTemplate->getBlocks('boxes_column_right'); ?>
      </div>

<?php
  }
?>

    </div> <!-- row -->

  </div> <!-- bodyWrapper //-->

  <?php require('includes/footer.php'); ?> 
  
  <?php 
  echo $OSCOM_Hooks->call('siteWide', 'BS_footer');
  echo $OSCOM_Hooks->call('siteWide', 'FL');
  
  echo $oscTemplate->getBlocks('footer_scripts'); 
  ?>

</body>
</html>
