<div class="col-sm-<?php echo $content_width; ?> cm-cs-product-notifications">
  <h5 class="mb-1"><?php echo MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_TEXT_NOTIFY_PRODUCTS; ?></h5>
  
  <div class="border">
    <ul class="list-group list-group-flush">
      <?php
  foreach ($products_displayed as $id => $name) {
    echo '<li class="list-group-item">';
    echo '<div class="custom-control custom-switch">';
    echo tep_draw_checkbox_field('notify[]', $id, false, 'class="custom-control-input" id="notify_' . $id . '"');
    echo '<label class="custom-control-label" for="notify_' . $id . '">' . $name . '</label></div>';
    echo '</li>' . PHP_EOL;
  }
?>
    </ul>
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
