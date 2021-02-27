<div class="col-sm-<?php echo $content_width; ?> cm-pi-also-purchased">
  <h4><?php echo MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_PUBLIC_TITLE; ?></h4>

  <div class="<?php echo $card_layout; ?>">
    <?php
    while ($orders = tep_db_fetch_array($orders_query)) {      
      ?>
      <div class="col mb-2">
        <div class="card h-100 is-product" data-is-special="<?php echo (int)$orders['is_special']; ?>" data-product-price="<?php echo $currencies->display_raw($orders['final_price'], tep_get_tax_rate($orders['products_tax_class_id'])); ?>" data-product-manufacturer="<?php echo max(0, (int)$orders['manufacturers_id']); ?>">
          <a href="<?php echo tep_href_link('product_info.php', 'products_id=' . (int)$orders['products_id']); ?>"><?php echo tep_image('images/' . $orders['products_image'], htmlspecialchars($orders['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, '', true, 'card-img-top'); ?></a>
          <div class="card-body">         
            <h5 class="card-title">
              <a href="<?php echo tep_href_link('product_info.php', 'products_id=' . (int)$orders['products_id']); ?>"><?php echo $orders['products_name']; ?></a>
            </h5>
            <h6 class="card-subtitle mb-2 text-muted">
              <?php
              if ($orders['is_special'] == 1) {
                echo sprintf(IS_PRODUCT_SHOW_PRICE_SPECIAL, $currencies->display_price($orders['products_price'], tep_get_tax_rate($orders['products_tax_class_id'])), $currencies->display_price($orders['specials_new_products_price'], tep_get_tax_rate($orders['products_tax_class_id'])));
              }
              else {
                echo sprintf(IS_PRODUCT_SHOW_PRICE, $currencies->display_price($orders['products_price'], tep_get_tax_rate($orders['products_tax_class_id'])));
              }
              ?>
            </h6>          
          </div>
          <div class="card-footer bg-white pt-0 border-0">
            <div class="btn-group" role="group">
              <?php
              echo tep_draw_button(IS_PRODUCT_BUTTON_VIEW, '', tep_href_link('product_info.php', tep_get_all_get_params(array('action', 'products_id', 'sort', 'cPath')) . 'products_id=' . (int)$orders['products_id']), NULL, NULL, 'btn-info btn-product-listing btn-view') . PHP_EOL;
              $has_attributes = (tep_has_product_attributes((int)$orders['products_id']) === true) ? '1' : '0';
              if ($has_attributes == 0) echo tep_draw_button(IS_PRODUCT_BUTTON_BUY, '', tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action', 'products_id', 'sort', 'cPath')) . 'action=buy_now&products_id=' . (int)$orders['products_id']), NULL, array('params' => 'data-has-attributes="' . $has_attributes . '" data-in-stock="' . (int)$orders['in_stock'] . '" data-product-id="' . (int)$orders['products_id'] . '"'), 'btn-light btn-product-listing btn-buy') . PHP_EOL;
              ?>
            </div>
          </div>
        </div>
      </div>
      <?php
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
