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
  
  <!-- bs -->
  <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <?php echo $oscTemplate->getBlocks('footer_scripts'); ?>
  
  <script>
  var filter = $('.filter-list');
  $('div.alert-filters > ul.nav').append($('<ul>').attr('class','nav ml-auto').append($('<li>').append(filter)));  
  </script>

</body>
</html>
