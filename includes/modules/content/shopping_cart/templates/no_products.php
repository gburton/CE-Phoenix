<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/
?>

<div id="cm_sc_no_products" class="col-sm-<?php echo $content_width; ?>">
	<div class="alert alert-danger"><?php echo TEXT_CART_EMPTY; ?></div>
  <p class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', tep_href_link(FILENAME_DEFAULT), 'primary', NULL, 'btn-danger'); ?></p>
</div>