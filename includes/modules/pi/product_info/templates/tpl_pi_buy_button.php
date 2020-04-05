<div class="col-sm-<?php echo $content_width; ?> pi-buy-button mt-2">
  <?php 
  echo tep_draw_button(PI_BUY_BUTTON_TEXT, 'fas fa-shopping-cart', null, 'primary', array('params' => 'data-has-attributes="' . (($products_attributes['total'] > 0) ? '1' : '0') . '" data-in-stock="' . (int)$product_info['products_quantity'] . '" data-product-id="' . (int)$product_info['products_id'] . '"'), 'btn-success btn-block btn-lg btn-product-info btn-buy'); 
  echo tep_draw_hidden_field('products_id', (int)$product_info['products_id']);
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
