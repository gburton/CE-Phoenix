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
    $navigation->set_snapshot(array('mode' => 'SSL', 'page' => 'checkout_payment.php'));
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
    if ($cart->cartID != $cartID) {
      tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
    }
  }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
  if (!tep_session_is_registered('shipping')) {
    tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
  }

  if (!tep_session_is_registered('payment')) tep_session_register('payment');
  if (isset($_POST['payment'])) $payment = $_POST['payment'];

  if (!tep_session_is_registered('comments')) tep_session_register('comments');
  if (isset($_POST['comments']) && tep_not_null($_POST['comments'])) {
    $comments = tep_db_prepare_input($_POST['comments']);
  }

// load the selected payment module
  require('includes/classes/payment.php');
  $payment_modules = new payment($payment);

  require('includes/classes/order.php');
  $order = new order;

  $payment_modules->update_status();

  if ( ($payment_modules->selected_module != $payment) || ( is_array($payment_modules->modules) && (sizeof($payment_modules->modules) > 1) && !is_object($$payment) ) || (is_object($$payment) && ($$payment->enabled == false)) ) {
    tep_redirect(tep_href_link('checkout_payment.php', 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
  }

  if (is_array($payment_modules->modules)) {
    $payment_modules->pre_confirmation_check();
  }

// load the selected shipping module
  require('includes/classes/shipping.php');
  $shipping_modules = new shipping($shipping);

  require('includes/classes/order_total.php');
  $order_total_modules = new order_total;
  $order_total_modules->process();

// Stock Check
  $any_out_of_stock = false;
  if (STOCK_CHECK == 'true') {
    for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
      if (tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty'])) {
        $any_out_of_stock = true;
      }
    }
    // Out of Stock
    if ( (STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true) ) {
      tep_redirect(tep_href_link('shopping_cart.php'));
    }
  }

  require('includes/languages/' . $language . '/checkout_confirmation.php');

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_shipping.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2);

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size('checkout_confirmation') > 0) {
    echo $messageStack->output('checkout_confirmation');
  }

  if (isset($$payment->form_action_url)) {
    $form_action_url = $$payment->form_action_url;
  } else {
    $form_action_url = tep_href_link('checkout_process.php', '', 'SSL');
  }
  
  echo tep_draw_form('checkout_confirmation', $form_action_url, 'post');
?>

<div class="contentContainer">
  <div class="row">
    <div class="col-sm-7">
      <h5 class="mb-1"><?php echo LIST_PRODUCTS; ?><small><a class="font-weight-lighter ml-2" href="<?php echo tep_href_link('shopping_cart.php', '', 'SSL'); ?>"><?php echo TEXT_EDIT; ?></a></small>
      </h5>
      <div class="border">
        <ul class="list-group list-group-flush">
          <?php
          for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
            echo '<li class="list-group-item">';
              echo '<span class="float-right">' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . '</span>';
              echo '<h5 class="mb-1">' . $order->products[$i]['name'] . '<small> x ' . $order->products[$i]['qty'] . '</small></h5>';
              if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
                echo '<p class="w-100 mb-1">';
                for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
                  echo '- ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '<br>';
                }
                echo '</p>';
              }              
            echo '</li>';
          }
          ?>
        </ul>
        <table class="table mb-0">
          <?php
          if (MODULE_ORDER_TOTAL_INSTALLED) {
            echo $order_total_modules->output();
          }
          ?>
        </table>
      </div>      
    </div>
    <div class="col-sm-5">
      <h5 class="mb-1"><?php echo ORDER_DETAILS; ?></h5>
      <div class="border">        
        <ul class="list-group list-group-flush">
          <?php
          if ($sendto != false) {
            echo '<li class="list-group-item">';
              echo '<i class="fas fa-shipping-fast fa-fw fa-3x float-right text-black-50"></i>';
              echo '<h5 class="mb-0">' . HEADING_DELIVERY_ADDRESS . '<small><a class="font-weight-lighter ml-2" href="' . tep_href_link('checkout_shipping_address.php', '', 'SSL') . '">' . TEXT_EDIT . '</a></small></h5>';
              echo '<p class="w-100 mb-1">' . tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />') . '</p>';
            echo '</li>';
          }
          echo '<li class="list-group-item">';
            echo '<i class="fas fa-file-invoice-dollar fa-fw fa-3x float-right text-black-50"></i>';
            echo '<h5 class="mb-0">' . HEADING_BILLING_ADDRESS . '<small><a class="font-weight-lighter ml-2" href="' . tep_href_link('checkout_payment_address.php', '', 'SSL') . '">' . TEXT_EDIT . '</a></small></h5>';
            echo '<p class="w-100 mb-1">' . tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />') . '</p>';
          echo '</li>';
          if ($order->info['shipping_method']) {
            echo '<li class="list-group-item">';
              echo '<h5 class="mb-1">' . HEADING_SHIPPING_METHOD . '<small><a class="font-weight-lighter ml-2" href="' . tep_href_link('checkout_shipping.php', '', 'SSL') . '">' . TEXT_EDIT . '</a></small></h5>';
              echo '<p class="w-100 mb-1">' . $order->info['shipping_method'] . '</p>';
            echo '</li>';
          }
          echo '<li class="list-group-item">';
            echo '<h5 class="mb-1">' . HEADING_PAYMENT_METHOD . '<small><a class="font-weight-lighter ml-2" href="' . tep_href_link('checkout_payment.php', '', 'SSL') . '">' . TEXT_EDIT . '</a></small></h5>';
            echo '<p class="w-100 mb-1">' . $order->info['payment_method'] . '</p>';
          echo '</li>';
          ?>
        </ul>
        
      </div>
    </div>
  </div>
  
  <?php
  if (tep_not_null($order->info['comments'])) {
    ?>
    <h5 class="mb-1"><?php echo HEADING_ORDER_COMMENTS . '<small><a class="font-weight-lighter ml-2" href="' . tep_href_link('checkout_payment.php', '', 'SSL') . '">' .TEXT_EDIT . '</a></small>'; ?></h5>
    
    <div class="border mb-3">
      <ul class="list-group list-group-flush">
        <li class="list-group-item">
          <?php 
          echo '<i class="fas fa-comments fa-fw fa-3x float-right text-black-50"></i>';
          echo nl2br(tep_output_string_protected($order->info['comments'])) . tep_draw_hidden_field('comments', $order->info['comments']);
          ?>
        </li>
      </ul>
    </div>
   
    <?php
  }

  if (is_array($payment_modules->modules)) {
    if ($confirmation = $payment_modules->confirmation()) {
?>
  <hr>

  <h5 class="mb-1"><?php echo HEADING_PAYMENT_INFORMATION; ?></h5>

  <div class="row">
<?php
    if (tep_not_null($confirmation['title'])) {
      echo '<div class="col">';
        echo '<div class="bg-light border p-3">';
          echo $confirmation['title'];
        echo '</div>';
      echo '</div>';
    }
    if (isset($confirmation['fields'])) {
      echo '<div class="col">';
        echo '<div class="alert alert-info" role="alert">';
        $fields = '';
        for ($i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++) {
          $fields .= $confirmation['fields'][$i]['title'] . ' ' . $confirmation['fields'][$i]['field'] . '<br>';
        }
        if (strlen($fields) > 4) echo substr($fields,0,-4);
        echo '</div>';
      echo '</div>';
    }
?>
  </div>
  <div class="w-100"></div>

<?php
    }
  }
  
  echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
  ?>
  
  <div class="buttonSet mt-3">
    <div class="text-right">
      <?php
      if (is_array($payment_modules->modules)) {
        echo $payment_modules->process_button();
      }
      echo tep_draw_button(IMAGE_BUTTON_FINALISE_ORDER, 'fas fa-check-circle', null, 'primary', null, 'btn-success btn-block btn-lg');
      ?>
    </div>
  </div>

  <div class="progressBarHook">
    <?php
    $parameters = [
      'style' => 'progress-bar progress-bar-striped progress-bar-animated bg-info',
      'markers' => ['position' => 3, 'min' => 0, 'max' => 100, 'now' => 100],
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
