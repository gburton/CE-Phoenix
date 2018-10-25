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

    <table class="table table-bordered">
      <thead>
        <tr>
          <th><?php echo HEADING_QTY; ?></th>
          <th><?php echo HEADING_PRODUCTS; ?></th>
          <th colspan="2" class="text-right"><?php echo tep_draw_button(TEXT_EDIT, 'fa fa-edit', tep_href_link('shopping_cart.php'), NULL, NULL, 'btn btn-light btn-sm'); ?></th>
        </tr>
      </thead>
     <tbody>

<?php
  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    echo '          <tr>' . "\n" .
         '            <td align="right" valign="top" width="30">' . $order->products[$i]['qty'] . '&nbsp;x&nbsp;</td>' . "\n" .
         '            <td valign="top">' . $order->products[$i]['name'];

    if (STOCK_CHECK == 'true') {
      echo tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty']);
    }

    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br /><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>';
      }
    }

    echo '</td>' . "\n";

    if (sizeof($order->info['tax_groups']) > 1) echo '            <td valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";

    echo '            <td align="right" valign="top">' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>


        </tbody>
      </table>

      <table class="table">

<?php
  if (MODULE_ORDER_TOTAL_INSTALLED) {
    echo $order_total_modules->output();
  }
?>

        </table>
  <hr>
  
  <table class="table">
    <thead>
      <tr>
        <?php
        if ($sendto != false) {
          echo '<th>' . HEADING_DELIVERY_ADDRESS . '</th>';
        }
        echo '<th>' . HEADING_BILLING_ADDRESS . '</th>';
        ?>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php
        if ($sendto != false) {
          echo '<td>' . tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />') . '</td>';
        }
        echo '<td>' . tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />') . '</td>';
        ?>
      </tr>
    </tbody>
  </table>
  
  <table class="table">
    <thead>
      <tr>
        <?php
        if ($order->info['shipping_method']) {
          echo '<th scope="col">' . HEADING_SHIPPING_METHOD . '</th>';
        }
        echo '<th scope="col">' . HEADING_PAYMENT_METHOD . '</th>';
        ?>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php
        if ($order->info['shipping_method']) {
          echo '<td scope="row">' . $order->info['shipping_method'] . ' ' . tep_draw_button(TEXT_EDIT, 'fa fa-edit', tep_href_link('checkout_shipping.php', '', 'SSL'), NULL, NULL, 'btn-light btn-sm') . '</td>';
        }
        echo '<td scope="row">' . $order->info['payment_method'] . ' ' . tep_draw_button(TEXT_EDIT, 'fa fa-edit', tep_href_link('checkout_payment.php', '', 'SSL'), NULL, NULL, 'btn-light btn-sm') . '</td>';
        ?>
      </tr>
    </tbody>
  </table>
 

<?php
  if (is_array($payment_modules->modules)) {
    if ($confirmation = $payment_modules->confirmation()) {
?>
  <hr>

  <h4><?php echo HEADING_PAYMENT_INFORMATION; ?></h4>

  <div class="row">
<?php
    if (tep_not_null($confirmation['title'])) {
      echo '<div class="col-sm-6">';
      echo '  <div class="alert alert-danger">';
      echo $confirmation['title'];
      echo '  </div>';
      echo '</div>';
    }
?>
<?php
      if (isset($confirmation['fields'])) {
        echo '<div class="col-sm-6">';
        echo '  <div class="alert alert-info">';
        $fields = '';
        for ($i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++) {
          $fields .= $confirmation['fields'][$i]['title'] . ' ' . $confirmation['fields'][$i]['field'] . '<br>';
        }
        if (strlen($fields) > 4) echo substr($fields,0,-4);
        echo '  </div>';
        echo '</div>';
      }
?>
  </div>
  <div class="clearfix"></div>

<?php
    }
  }

  if (tep_not_null($order->info['comments'])) {
?>
  <hr>

  <h4><?php echo HEADING_ORDER_COMMENTS; ?></h4>
  <p><?php echo nl2br(tep_output_string_protected($order->info['comments'])) . tep_draw_hidden_field('comments', $order->info['comments']) . ' ' . tep_draw_button(TEXT_EDIT, 'fa fa-edit', tep_href_link('checkout_payment.php', '', 'SSL'), NULL, NULL, 'btn-light btn-sm'); ?></p>

<?php
  }
?>

  <div class="buttonSet">
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
    echo $OSCOM_Hooks->call('progress', 'progressBar', $arr = array('style' => 'progress-bar progress-bar-striped progress-bar-animated bg-info', 'markers' => array('position' => 3, 'min' => 0, 'max' => 100, 'now' => 100)));
    ?>  
  </div>

</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
