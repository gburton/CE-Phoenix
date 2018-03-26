<div class="col-xs-<?php echo $content_width; ?> text-right cm-pi-buy-button">
  <?php 
  echo tep_draw_button(MODULE_CONTENT_PI_BUY_BUTTON_TEXT, 'fa fa-shopping-cart', null, 'primary', array('params' => 'data-has-attributes="' . (($products_attributes['total'] > 0) ? '1' : '0') . '" data-in-stock="' . (int)$product_info['products_quantity'] . '" data-product-id="' . (int)$product_info['products_id'] . '"'), 'btn-success btn-product-info btn-buy'); 
  echo tep_draw_hidden_field('products_id', (int)$product_info['products_id']);
  ?>
</div>
