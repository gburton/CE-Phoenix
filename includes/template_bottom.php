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
  
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

  <?php echo $oscTemplate->getBlocks('footer_scripts'); ?>
  
  <script>
  var filter = $('.filter-list');
  $('ul.nav').append($('<ul>').attr('class','nav ml-auto').append($('<li>').append(filter)));  
  </script>

</body>
</html>