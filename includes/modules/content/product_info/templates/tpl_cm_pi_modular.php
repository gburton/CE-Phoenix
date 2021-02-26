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

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
?>
