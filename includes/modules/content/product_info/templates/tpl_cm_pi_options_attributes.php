<div class="col-sm-<?php echo $content_width; ?> cm-pi-options-attributes">
  <h4><?php echo MODULE_CONTENT_PI_OA_HEADING_TITLE; ?></h4>

  <?php
  foreach ($options as $option) {
    echo '<div class="form-group row">' . PHP_EOL;
    echo '<label for="input_' . $option['id'] . '" class="col-form-label col-sm-3 text-left text-sm-right">' . $option['name'] . '</label>' . PHP_EOL;
    echo '<div class="col-sm-9">' . PHP_EOL;
    echo tep_draw_pull_down_menu('id[' . $option['id'] . ']', $option['choices'], $option['selection'], $fr_required . 'id="input_' . $option['id'] . '"') . PHP_EOL;
    echo $fr_input;
    echo '</div>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
  }
?>
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
