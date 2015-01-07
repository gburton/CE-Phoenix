<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/
?>

      </div> <!-- bodyContent //-->

<?php
  if ($oscTemplate->hasBlocks('boxes_column_left')) {
?>

      <aside id="columnLeft" class="col-md-<?php echo $oscTemplate->getGridColumnWidth(); ?>  col-md-pull-<?php echo $oscTemplate->getGridContentWidth(); ?>">
        <?php echo $oscTemplate->getBlocks('boxes_column_left'); ?>
      </aside>

<?php
  }

  if ($oscTemplate->hasBlocks('boxes_column_right')) {
?>

      <aside id="columnRight" class="col-md-<?php echo $oscTemplate->getGridColumnWidth(); ?>">
        <?php echo $oscTemplate->getBlocks('boxes_column_right'); ?>
      </aside>

<?php
  }
?>

    </div> <!-- row -->

  </section> <!-- bodyWrapper //-->

  <footer id="modular-footer" class="<?php echo BOOTSTRAP_CONTAINER; ?>">
    <div id="footer" class="row">
      <?php echo $oscTemplate->getContent('footer'); ?>
    </div>
      
    <div id="footer-extra" class="row">
      <?php echo $oscTemplate->getContent('footer_suffix'); ?>
    </div>
  </footer>

  <script src="ext/bootstrap/js/bootstrap.min.js"></script>
  <?php echo $oscTemplate->getBlocks('footer_scripts'); ?>

</body>
</html>