<div class="col-sm-<?php echo $content_width ?>">
    <table class="table table-striped table-condensed">
	<tbody>
	    <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_SHOPPING_CART, 'action=update_product')); ?>
      	<?php echo $products_name; ?>
		</form>
      </tbody>
    </table>
</div>	