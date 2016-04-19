<?php
/*
  $Id$
 
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
 
  Copyright (c) 2016 osCommerce
 
  Released under the GNU General Public License
*/
?>

<div class="col-sm-<?php echo $content_width ?>">
	<?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_SHOPPING_CART, 'action=update_product')); ?>
      	<?php echo $products_name; ?>
	</form>
</div>	
