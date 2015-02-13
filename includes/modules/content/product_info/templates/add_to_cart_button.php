<div class="col-sm-<?php echo $content_width; ?> addtocartbutton">
    <div class="text-right">
        <?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) . tep_draw_button(IMAGE_BUTTON_IN_CART, 'glyphicon glyphicon-shopping-cart', null, 'primary', null, 'btn-success'); ?>
    </div>
</div>