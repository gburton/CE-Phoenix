<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>


<?php echo tep_draw_form('cart_quantity', tep_href_link('product_info.php', tep_get_all_get_params(['action']). 'action=add_product', 'NONSSL'), 'post', 'role="form"'); ?>

<?php
  if ($messageStack->size('product_action') > 0) {
    echo $messageStack->output('product_action');
  }
?>

  <div class="row is-product">
    <?php echo $oscTemplate->getContent('product_info'); ?>
  </div>

</form>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
