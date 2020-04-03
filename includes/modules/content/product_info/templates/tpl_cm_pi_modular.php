<div class="col-sm-<?php echo $content_width; ?> cm-pi-modular">
  <div class="row">
    <?php
    foreach ($slot_array as $k => $v) {
      if ($oscTemplate->hasBlocks('pi_modules_' . $k)) {
        echo '<div class="col-sm-' . $v . '">';
          echo '<div class="row">';
            echo $oscTemplate->getBlocks('pi_modules_' . $k);
          echo '</div>';
        echo '</div>' . PHP_EOL;
      }
    }
    ?>
  </div>
</div>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
