<div class="buttonSet col-sm-<?php echo $content_width . ' ' . MODULE_CONTENT_PRODUCT_INFO_BUTTON_LINE_CONTENT_ALIGN . ' ' . MODULE_CONTENT_PRODUCT_INFO_BUTTON_LINE_CONTENT_VERT_MARGIN . ' ' . MODULE_CONTENT_PRODUCT_INFO_BUTTON_LINE_CONTENT_HORIZ_MARGIN; ?> ">
  <div class="col-xs-6 text-left reviewbutton">
    <?php echo $review_button; ?>
  </div>
  <div class="col-xs-6 text-right addcartbutton">
    <?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) . tep_draw_button(IMAGE_BUTTON_IN_CART, 'glyphicon glyphicon-shopping-cart', null, 'primary', null, 'btn-success'); ?>
  </div>
</div>