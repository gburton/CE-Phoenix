<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';
  
  $OSCOM_Hooks->register_pipeline('progress');

// if the customer is not logged on, redirect them to the login page
  if (!isset($_SESSION['customer_id'])) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
  if (!isset($_SESSION['shipping'])) {
    tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
  }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($cart->cartID) && isset($_SESSION['cartID'])) {
    if ($cart->cartID != $cartID) {
      tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
    }
  }

// Stock Check
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    foreach ($cart->get_products() as $product) {
      if (tep_check_stock($product['id'], $product['quantity'])) {
        tep_redirect(tep_href_link('shopping_cart.php'));
        break;
      }
    }
  }

  if (isset($_SESSION['billto'])) {
// verify the selected billing address
    if ( is_numeric($_SESSION['billto']) || ([] === $_SESSION['billto']) ) {
      $check_address_query = tep_db_query("SELECT COUNT(*) AS total FROM address_book WHERE customers_id = " . (int)$_SESSION['customer_id'] . " and address_book_id = " . (int)$_SESSION['billto']);
      $check_address = tep_db_fetch_array($check_address_query);

      if ($check_address['total'] != '1') {
        $_SESSION['billto'] = $customer->get_default_address_id();
        unset($_SESSION['payment']);
      }
    }
  } else {
    // if no billing destination address was selected, use the customers own address as default
    $_SESSION['billto'] = $customer->get_default_address_id();
  }

  $order = new order();

  if (!tep_session_is_registered('comments')) tep_session_register('comments');
  if (isset($_POST['comments']) && tep_not_null($_POST['comments'])) {
    $comments = tep_db_prepare_input($_POST['comments']);
  }

  $total_weight = $cart->show_weight();
  $total_count = $cart->count_contents();

// load all enabled payment modules
  $payment_modules = new payment();

  require "includes/languages/$language/checkout_payment.php";

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_shipping.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('checkout_payment.php', '', 'SSL'));

  require 'includes/template_top.php';

  echo $payment_modules->javascript_validation();
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('checkout_payment', tep_href_link('checkout_confirmation.php', '', 'SSL'), 'post', 'onsubmit="return check_form();"', true); ?>

<div class="contentContainer">

<?php
  if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error())) {
    echo '  <div class="alert alert-danger">' . "\n";
    echo '    <p class="lead"><b>' . tep_output_string_protected($error['title']) . "</b></p>\n";
    echo '    <p>' . tep_output_string_protected($error['error']) . "</p>\n";
    echo "  </div>\n";
  }

  $selection = $payment_modules->selection();
?>

  <div class="row">
    <div class="col-sm-7">
      <h2 class="h5 mb-1"><?php echo TABLE_HEADING_PAYMENT_METHOD; ?></h2>
      <div>
        <table class="table border-right border-left border-bottom table-hover m-0">
<?php
  foreach ($selection as $choice) {
?>
          <tr class="table-selection">
            <td><label for="p_<?php echo $choice['id']; ?>"><?php echo $choice['module']; ?></label></td>
            <td class="text-right">
<?php
    if (count($selection) > 1) {
      echo '              <div class="custom-control custom-radio custom-control-inline">';
      echo tep_draw_radio_field('payment', $choice['id'], ($choice['id'] == $payment), 'id="p_' . $choice['id'] . '" required="required" aria-required="true" class="custom-control-input"');
      echo '<label class="custom-control-label" for="p_' . $choice['id'] . '">&nbsp;</label>';
      echo '</div>';
    } else {
      echo tep_draw_hidden_field('payment', $choice['id']);
    }
?>
            </td>
          </tr>
<?php
    if (isset($choice['error'])) {
?>
          <tr>
            <td colspan="2"><?php echo $choice['error']; ?></td>
          </tr>
<?php
    } elseif (isset($choice['fields']) && is_array($choice['fields'])) {
      foreach ($choice['fields'] as $field) {
?>
          <tr>
            <td><?php echo $field['title']; ?></td>
            <td><?php echo $field['field']; ?></td>
          </tr>
<?php
      }
    }
  }
?>
        </table>
<?php
  if (count($selection) == 1) {
    echo '        <p class="m-2 font-weight-lighter">' . TEXT_ENTER_PAYMENT_INFORMATION . "</p>\n";
  }
?>
      </div>
    </div>
    <div class="col-sm-5">
      <h2 class="h5 mb-1">
<?php
  echo TABLE_HEADING_BILLING_ADDRESS;
  echo sprintf(LINK_TEXT_EDIT, 'font-weight-lighter ml-3', tep_href_link('checkout_payment_address.php', '', 'SSL'));
?>
      </h2>
      <div class="border">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><?php echo PAYMENT_FA_ICON . $customer->make_address_label($_SESSION['billto'], true, ' ', '<br />'); ?>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <hr>


  <div class="form-group row">
    <label for="inputComments" class="col-form-label col-sm-4 text-sm-right"><?php echo ENTRY_COMMENTS; ?></label>
    <div class="col-sm-8"><?php echo tep_draw_textarea_field('comments', 'soft', 60, 5, $comments, 'id="inputComments" placeholder="' . ENTRY_COMMENTS_PLACEHOLDER . '"'); ?></div>
  </div>

<?php
  echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(BUTTON_CONTINUE_CHECKOUT_PROCEDURE, 'fas fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
  </div>

  <div class="progressBarHook">

<?php
  $parameters = [
    'style' => 'progress-bar progress-bar-striped progress-bar-animated bg-info',
    'markers' => ['position' => 2, 'min' => 0, 'max' => 100, 'now' => 67],
  ];
  echo $OSCOM_Hooks->call('progress', 'progressBar', $parameters);
?>  

  </div>

</div>

</form>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
