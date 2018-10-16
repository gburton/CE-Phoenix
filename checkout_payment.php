<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  $OSCOM_Hooks->register('progress');

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
  if (!tep_session_is_registered('shipping')) {
    tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
  }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
    if ($cart->cartID != $cartID) {
      tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
    }
  }

// Stock Check
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if (tep_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
        tep_redirect(tep_href_link('shopping_cart.php'));
        break;
      }
    }
  }

// if no billing destination address was selected, use the customers own address as default
  if (!tep_session_is_registered('billto')) {
    tep_session_register('billto');
    $billto = $customer_default_address_id;
  } else {
// verify the selected billing address
    if ( (is_array($billto) && empty($billto)) || is_numeric($billto) ) {
      $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "' and address_book_id = '" . (int)$billto . "'");
      $check_address = tep_db_fetch_array($check_address_query);

      if ($check_address['total'] != '1') {
        $billto = $customer_default_address_id;
        if (tep_session_is_registered('payment')) tep_session_unregister('payment');
      }
    }
  }

  require('includes/classes/order.php');
  $order = new order;

  if (!tep_session_is_registered('comments')) tep_session_register('comments');
  if (isset($_POST['comments']) && tep_not_null($_POST['comments'])) {
    $comments = tep_db_prepare_input($_POST['comments']);
  }

  $total_weight = $cart->show_weight();
  $total_count = $cart->count_contents();

// load all enabled payment modules
  require('includes/classes/payment.php');
  $payment_modules = new payment;

  require('includes/languages/' . $language . '/checkout_payment.php');

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_shipping.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('checkout_payment.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<?php echo $payment_modules->javascript_validation(); ?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('checkout_payment', tep_href_link('checkout_confirmation.php', '', 'SSL'), 'post', 'onsubmit="return check_form();"', true); ?>

<div class="contentContainer">

<?php
  if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error())) {
?>
  
  <?php echo '<strong>' . tep_output_string_protected($error['title']) . '</strong>'; ?>

  <p class="messageStackError"><?php echo tep_output_string_protected($error['error']); ?></p>

<?php
  }
?>

  <h4><?php echo TABLE_HEADING_BILLING_ADDRESS; ?></h4>

  <div class="row">
    <div class="col-sm-8">
      <div class="alert alert-warning">
        <?php echo TEXT_SELECTED_BILLING_DESTINATION; ?>
        <p class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CHANGE_ADDRESS, 'fa fa-home', tep_href_link('checkout_payment_address.php', '', 'SSL'), null, null, 'btn-light btn-sm'); ?></p>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="card">
        <div class="card-header"><?php echo TITLE_BILLING_ADDRESS; ?></div>
        <div class="card-body">
          <?php echo tep_address_label($customer_id, $billto, true, ' ', '<br />'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>

  <h4><?php echo TABLE_HEADING_PAYMENT_METHOD; ?></h4>

<?php
  $selection = $payment_modules->selection();

  if (sizeof($selection) > 1) {
?>

  <div class="alert alert-warning">
    <div class="row">
      <div class="col-sm-8">
        <?php echo TEXT_SELECT_PAYMENT_METHOD; ?>
      </div>
      <div class="col-sm-4">
        <div class="text-right">
          <?php echo '<strong>' . TITLE_PLEASE_SELECT . '</strong>'; ?>
        </div>
      </div>
    </div>
  </div>

<?php
    } else {
?>

  <div class="alert alert-info"><?php echo TEXT_ENTER_PAYMENT_INFORMATION; ?></div>

<?php
    }
?>

  <table class="table table-striped table-sm table-hover">
    <tbody>
<?php
  $radio_buttons = 0;
  for ($i=0, $n=sizeof($selection); $i<$n; $i++) {
?>
    <tr class="table-selection">
      <td><strong><?php echo $selection[$i]['module']; ?></strong></td>
      <td align="right">

<?php
    if (sizeof($selection) > 1) {
      echo tep_draw_radio_field('payment', $selection[$i]['id'], ($selection[$i]['id'] == $payment), 'required aria-required="true"');
    } else {
      echo tep_draw_hidden_field('payment', $selection[$i]['id']);
    }
?>

      </td>
    </tr>

<?php
    if (isset($selection[$i]['error'])) {
?>

    <tr>
      <td colspan="2"><?php echo $selection[$i]['error']; ?></td>
    </tr>

<?php
    } elseif (isset($selection[$i]['fields']) && is_array($selection[$i]['fields'])) {
?>

    <tr>
      <td colspan="2"><table border="0" cellspacing="0" cellpadding="2">

<?php
      for ($j=0, $n2=sizeof($selection[$i]['fields']); $j<$n2; $j++) {
?>

        <tr>
          <td><?php echo $selection[$i]['fields'][$j]['title']; ?></td>
          <td><?php echo $selection[$i]['fields'][$j]['field']; ?></td>
        </tr>

<?php
      }
?>

      </table></td>
    </tr>

<?php
    }

    $radio_buttons++;
  }
?>
    </tbody>
  </table>

  <hr>

  <div class="form-group row">
    <label for="inputComments" class="col-form-label col-sm-4 text-sm-right"><?php echo TABLE_HEADING_COMMENTS; ?></label>
    <div class="col-sm-8">
      <?php
      echo tep_draw_textarea_field('comments', 'soft', 60, 5, $comments, 'id="inputComments" placeholder="' . TABLE_HEADING_COMMENTS . '"');
      ?>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
  </div>

  <div class="progressBarHook">
    <?php
    echo $OSCOM_Hooks->call('progress', 'progressBar', $arr = array('style' => 'progress-bar progress-bar-striped progress-bar-animated bg-info', 'markers' => array('position' => 2, 'min' => 0, 'max' => 100, 'now' => 67)));
    ?>  
  </div>

</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
