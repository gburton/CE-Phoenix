<div class="col-sm-<?php echo $content_width; ?> cm-pi-price">
  <h2 class="display-4 text-left text-sm-right"><?php echo (tep_not_null($specials_price)) ? sprintf(MODULE_CONTENT_PI_PRICE_DISPLAY_SPECIAL, $specials_price, $products_price) : sprintf(MODULE_CONTENT_PI_PRICE_DISPLAY, $products_price); ?></h2>
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
