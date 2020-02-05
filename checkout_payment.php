<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  $OSCOM_Hooks->register_pipeline('progress');

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
      $check_address_query = tep_db_query("select count(*) as total from address_book where customers_id = '" . (int)$customer_id . "' and address_book_id = '" . (int)$billto . "'");
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
    echo '<div class="alert alert-danger">';
      echo '<p class="lead"><b>' . tep_output_string_protected($error['title']) . '</b></p>';
      echo '<p>' . tep_output_string_protected($error['error']) . '</p>';
    echo '</div>';
  }
  
  $selection = $payment_modules->selection();
?>

  <div class="row">
    <div class="col-sm-7">
      <h5 class="mb-1"><?php echo TABLE_HEADING_PAYMENT_METHOD; ?></h5>
      <div>
        <table class="table border-right border-left border-bottom table-hover m-0">
        <?php
        for ($i=0, $n=sizeof($selection); $i<$n; $i++) {
          ?>
          <tr class="table-selection">
            <td><?php echo $selection[$i]['module']; ?></td>
            <td class="text-right">
              <?php
              if (sizeof($selection) > 1) {
                echo '<div class="custom-control custom-radio custom-control-inline">';
                  echo tep_draw_radio_field('payment', $selection[$i]['id'], ($selection[$i]['id'] == $payment), 'id="p_' . $selection[$i]['id'] . '" required aria-required="true" aria-describedby="p_' . $selection[$i]['id'] . '" class="custom-control-input"');
                  echo '<label class="custom-control-label" for="p_' . $selection[$i]['id'] . '">&nbsp;</label>';
                echo '</div>';
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
            for ($j=0, $n2=sizeof($selection[$i]['fields']); $j<$n2; $j++) {
              ?>
              <tr>
                <td><?php echo $selection[$i]['fields'][$j]['title']; ?></td>
                <td><?php echo $selection[$i]['fields'][$j]['field']; ?></td>
              </tr>
              <?php
            }
          }
        }
        ?>
        </table>
        <?php
        if (sizeof($selection) == 1) {
          echo '<p class="m-2 font-weight-lighter">' . TEXT_ENTER_PAYMENT_INFORMATION . '</p>';
        }
        ?>
      </div>
    </div>
    <div class="col-sm-5">
      <h5 class="mb-1">
        <?php 
        echo TABLE_HEADING_BILLING_ADDRESS;
        echo sprintf(LINK_TEXT_EDIT, 'font-weight-lighter ml-3', 'checkout_payment_address.php');      
        ?>
      </h5>
      <div class="border">
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <?php
            echo PAYMENT_FA_ICON;            
            echo tep_address_label($customer_id, $billto, true, ' ', '<br />'); 
            ?>
          </li>
        </ul>
      </div>
    </div>
  </div>
  
  <hr>


  <div class="form-group row">
    <label for="inputComments" class="col-form-label col-sm-4 text-sm-right"><?php echo ENTRY_COMMENTS; ?></label>
    <div class="col-sm-8">
      <?php
      echo tep_draw_textarea_field('comments', 'soft', 60, 5, $comments, 'id="inputComments" placeholder="' . ENTRY_COMMENTS_PLACEHOLDER . '"');
      ?>
    </div>
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
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
