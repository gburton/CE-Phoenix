<div class="col-sm-<?php echo $content_width; ?> pi-options-attributes mt-2">
  <h6><?php echo PI_OA_HEADING_TITLE; ?></h6>

  <?php
  foreach ($options as $option) {
    echo '<div class="form-group row">' . PHP_EOL;
    echo '  <label for="input_' . $option['id'] . '" class="col-form-label col-sm-3 text-left text-sm-right">' . $option['name'] . '</label>' . PHP_EOL;
    echo '  <div class="col-sm-9">' . PHP_EOL;
    echo $option['menu'] . PHP_EOL;
    echo $fr_input;
    echo '  </div>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
  }
  ?>
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
