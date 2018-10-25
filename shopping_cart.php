<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

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

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

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

<?php
  $any_out_of_stock = 0;
  $products = $cart->get_products();
  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
// Push all attributes information in an array
    if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
      foreach($products[$i]['attributes'] as $option => $value) {
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

  <div class="table-responsive">  
    <table class="table">
      <thead>
        <tr>
          <th class="d-none d-md-block">&nbsp;</th>
          <th><?php echo TABLE_HEADING_PRODUCT; ?></th>
          <th><?php echo TABLE_HEADING_AVAILABILITY; ?></th>
          <th><?php echo TABLE_HEADING_QUANTITY; ?></th>          
          <th><?php echo TABLE_HEADING_REMOVE; ?></th>
          <th class="text-right"><?php echo TABLE_HEADING_PRICE; ?></th>
        </tr>
      </thead>
      <tbody>
<?php
    $products_name = NULL;
    
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      $products_name .= '<tr>';
        $products_name .= '<td class="d-none d-md-block"><a href="' . tep_href_link('product_info.php', 'products_id=' . $products[$i]['id']) . '">' . tep_image('images/' . $products[$i]['image'], htmlspecialchars($products[$i]['name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td>';
        $products_name .= '<th><a href="' . tep_href_link('product_info.php', 'products_id=' . $products[$i]['id']) . '">' . $products[$i]['name'] . '</a>';
        if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
          foreach($products[$i]['attributes'] as $option => $value) {
            $products_name .= '<small><br><i> - ' . $products[$i][$option]['products_options_name'] . ' ' . $products[$i][$option]['products_options_values_name'] . '</i></small>';
          }
        }
        $products_name .= '</th>';
        
        if (STOCK_CHECK == 'true') {
          $stock_check = tep_check_stock($products[$i]['id'], $products[$i]['quantity']);
          if (tep_not_null($stock_check)) {
            $any_out_of_stock = 1;

            $products_name .= '<td>' . $stock_check . '</td>';
          }
          else {
            goto in_stock;
          }
        }
        else {
          in_stock:
          $products_name .= '<td>' . TEXT_IN_STOCK . '</td>';
        }

        $products_name .= '<td><div class="input-group">' . tep_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'style="width: 65px;" min="0"', 'number') . tep_draw_hidden_field('products_id[]', $products[$i]['id']) . '<div class="input-group-append">' . tep_draw_button(CART_BUTTON_UPDATE, null, NULL, NULL, NULL, 'btn-info') . '</div></div></td>';

        $products_name .= '<td>' . tep_draw_button(CART_BUTTON_REMOVE, null, tep_href_link('shopping_cart.php', 'products_id=' . $products[$i]['id'] . '&action=remove_product'), NULL, NULL, 'btn-danger btn-xs') .'  </td>'  ;
        $products_name .= '<td class="text-right">' . $currencies->display_price($products[$i]['final_price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . '</td>';

        $products_name .='</tr>';
      }
      echo $products_name;
?>
        <tr>
          <td colspan="6"><h4 class="text-sm-right"><?php echo SUB_TITLE_SUB_TOTAL; ?> <?php echo $currencies->format($cart->show_total()); ?></h4></td>
        </tr>
      </tbody>
    </table>
  </div>

    

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

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CHECKOUT, 'fa fa-angle-right', tep_href_link('checkout_shipping.php', '', 'SSL'), 'primary', NULL, 'btn-success btn-lg btn-block'); ?></div>
  </div>

<?php
    $initialize_checkout_methods = $payment_modules->checkout_initialization_method();

    if (!empty($initialize_checkout_methods)) {
?>
  <div class="clearfix"></div>
  <p class="text-right"><?php echo TEXT_ALTERNATIVE_CHECKOUT_METHODS; ?></p>

<?php
      foreach($initialize_checkout_methods as $value) {
?>

  <p class="text-right"><?php echo $value; ?></p>

<?php
      }
    }
?>

</div>

</form>

<div class="row">
  <?php echo $oscTemplate->getContent('shopping_cart'); ?>
</div>

<?php
  } else {
?>

<div class="alert alert-danger">
  <?php echo TEXT_CART_EMPTY; ?>
</div>

<p class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', tep_href_link('index.php'), 'primary', NULL, 'btn-danger btn-lg btn-block'); ?></p>

<?php
  }

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
