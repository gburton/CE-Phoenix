<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require("includes/application_top.php");

  if ($cart->count_contents() > 0) {
    include('includes/classes/payment.php');
    $payment_modules = new payment;
  }

  require('includes/languages/' . $language . '/shopping_cart.php');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('shopping_cart.php'));

  require('includes/template_top.php');
?>

<div class="page-header">
  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<?php
  if ($messageStack->size('product_action') > 0) {
    echo $messageStack->output('product_action');
  }
?>

<?php
  if ($cart->count_contents() > 0) {
?>

<?php echo tep_draw_form('cart_quantity', tep_href_link('shopping_cart.php', 'action=update_product')); ?>

<div class="contentContainer">
  <div class="contentText">

<?php
    $any_out_of_stock = 0;
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
// Push all attributes information in an array
      if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
        while (list($option, $value) = each($products[$i]['attributes'])) {
          echo tep_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
          $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix
                                      from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                      where pa.products_id = '" . (int)$products[$i]['id'] . "'
                                       and pa.options_id = '" . (int)$option . "'
                                       and pa.options_id = popt.products_options_id
                                       and pa.options_values_id = '" . (int)$value . "'
                                       and pa.options_values_id = poval.products_options_values_id
                                       and popt.language_id = '" . (int)$languages_id . "'
                                       and poval.language_id = '" . (int)$languages_id . "'");
          $attributes_values = tep_db_fetch_array($attributes);

          $products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
          $products[$i][$option]['options_values_id'] = $value;
          $products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
          $products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
          $products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
        }
      }
    }
?>

    <table class="table table-striped table-condensed">
      <tbody>
<?php
    $products_name = NULL;
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      $products_name .= '<tr>';

      $products_name .= '  <td valign="top" align="center"><a href="' . tep_href_link('product_info.php', 'products_id=' . $products[$i]['id']) . '">' . tep_image('images/' . $products[$i]['image'], $products[$i]['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td>' .
                        '  <td valign="top"><a href="' . tep_href_link('product_info.php', 'products_id=' . $products[$i]['id']) . '"><strong>' . $products[$i]['name'] . '</strong></a>';

      if (STOCK_CHECK == 'true') {
        $stock_check = tep_check_stock($products[$i]['id'], $products[$i]['quantity']);
        if (tep_not_null($stock_check)) {
          $any_out_of_stock = 1;

          $products_name .= $stock_check;
        }
      }

      if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
        reset($products[$i]['attributes']);
        while (list($option, $value) = each($products[$i]['attributes'])) {
          $products_name .= '<br /><small><i> - ' . $products[$i][$option]['products_options_name'] . ' ' . $products[$i][$option]['products_options_values_name'] . '</i></small>';
        }
      }

      $products_name .= '<br>' . tep_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'style="width: 65px;" min="0"', 'number') . tep_draw_hidden_field('products_id[]', $products[$i]['id']) . ' ' . tep_draw_button(CART_BUTTON_UPDATE, 'fa fa-refresh', NULL, NULL, NULL, 'btn-info btn-xs') . ' ' . tep_draw_button(CART_BUTTON_REMOVE, 'fa fa-remove', tep_href_link('shopping_cart.php', 'products_id=' . $products[$i]['id'] . '&action=remove_product'), NULL, NULL, 'btn-danger btn-xs');

      $products_name .= '</td>';

      $products_name .= '  <td align="right" valign="top"><strong>' . $currencies->display_price($products[$i]['final_price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . '</strong></td>' .
                        '</tr>';
    }
    echo $products_name;
?>

      </tbody>
    </table>

    <p class="text-right"><strong><?php echo SUB_TITLE_SUB_TOTAL; ?> <?php echo $currencies->format($cart->show_total()); ?></strong></p>

<?php
    if ($any_out_of_stock == 1) {
      if (STOCK_ALLOW_CHECKOUT == 'true') {
?>

    <div class="alert alert-warning"><?php echo OUT_OF_STOCK_CAN_CHECKOUT; ?></div>

<?php
      } else {
?>

    <div class="alert alert-danger"><?php echo OUT_OF_STOCK_CANT_CHECKOUT; ?></div>

<?php
      }
    }
?>

  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CHECKOUT, 'fa fa-angle-right', tep_href_link('checkout_shipping.php', '', 'SSL'), 'primary', NULL, 'btn-success'); ?></div>
  </div>

<?php
    $initialize_checkout_methods = $payment_modules->checkout_initialization_method();

    if (!empty($initialize_checkout_methods)) {
?>
  <div class="clearfix"></div>
  <p class="text-right"><?php echo TEXT_ALTERNATIVE_CHECKOUT_METHODS; ?></p>

<?php
      reset($initialize_checkout_methods);
      while (list(, $value) = each($initialize_checkout_methods)) {
?>

  <p class="text-right"><?php echo $value; ?></p>

<?php
      }
    }
?>

</div>

</form>

<?php
  } else {
?>

<div class="alert alert-danger">
  <?php echo TEXT_CART_EMPTY; ?>
</div>

<p class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', tep_href_link('index.php'), 'primary', NULL, 'btn-danger'); ?></p>

<?php
  }

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
