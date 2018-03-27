<div class="col-sm-<?php echo $content_width; ?> cm-pi-price">
  <div class="page-header">
    <h2 class="h3 text-right-not-xs"><?php echo (tep_not_null($specials_price)) ? sprintf(MODULE_CONTENT_PI_PRICE_DISPLAY_SPECIAL, $specials_price, $products_price) : sprintf(MODULE_CONTENT_PI_PRICE_DISPLAY, $products_price); ?></h2>
  </div>
</div>
