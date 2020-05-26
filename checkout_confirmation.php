<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

// if the customer is not logged on, redirect them to the login page
  $parameters = [
    'page' => 'checkout_payment.php',
    'mode' => 'SSL',
  ];
  $OSCOM_Hooks->register_pipeline('loginRequired', $parameters);

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($_SESSION['cart']->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($_SESSION['cart']->cartID, $_SESSION['cartID'])) {
    if ($_SESSION['cart']->cartID != $_SESSION['cartID']) {
      tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
    }
  }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
  if (!isset($_SESSION['shipping'])) {
    tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
  }

  $OSCOM_Hooks->register_pipeline('progress');

  if (isset($_POST['payment'])) {
    $_SESSION['payment'] = $_POST['payment'];
  } elseif (!array_key_exists('payment', $_SESSION)) {
    $_SESSION['payment'] = null;
  }


  if (isset($_POST['comments']) && tep_not_null($_POST['comments'])) {
    $_SESSION['comments'] = tep_db_prepare_input($_POST['comments']);
  } elseif (!array_key_exists('comments', $_SESSION)) {
    $_SESSION['comments'] = null;
  }

// load the selected payment module
  $payment_modules = new payment($_SESSION['payment']);

  $order = new order();

  $payment_modules->update_status();

  if ( ($payment_modules->selected_module != $_SESSION['payment']) || ( is_array($payment_modules->modules) && (count($payment_modules->modules) > 1) && !is_object(${$_SESSION['payment']}) ) || !${$_SESSION['payment']}->enabled ) {
    tep_redirect(tep_href_link('checkout_payment.php', 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
  }

  if (is_array($payment_modules->modules)) {
    $payment_modules->pre_confirmation_check();
  }

// load the selected shipping module
  $shipping_modules = new shipping($shipping);

  $order_total_modules = new order_total();
  $order_total_modules->process();

  // Stock Check
  $any_out_of_stock = false;
  if (STOCK_CHECK == 'true') {
    foreach ($order->products as $product) {
      if (tep_check_stock($product['id'], $product['qty'])) {
        $any_out_of_stock = true;
      }
    }

    // Out of Stock
    if ( (STOCK_ALLOW_CHECKOUT != 'true') && $any_out_of_stock ) {
      tep_redirect(tep_href_link('shopping_cart.php'));
    }
  }

  require "includes/languages/$language/checkout_confirmation.php";

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_shipping.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2);

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size('checkout_confirmation') > 0) {
    echo $messageStack->output('checkout_confirmation');
  }

  $form_action_url = ${$_SESSION['payment']}->form_action_url ?? tep_href_link('checkout_process.php', '', 'SSL');

  echo tep_draw_form('checkout_confirmation', $form_action_url, 'post');
?>

  <div class="row">
    <div class="col-sm-7">
      <h5 class="mb-1"><?php echo LIST_PRODUCTS; ?><small><a class="font-weight-lighter ml-2" href="<?php echo tep_href_link('shopping_cart.php', '', 'SSL'); ?>"><?php echo TEXT_EDIT; ?></a></small></h5>
      <div class="border">
        <ul class="list-group list-group-flush">
          <?php
          foreach ($order->products as $product) {
            echo '<li class="list-group-item">';
              echo '<span class="float-right">' . $currencies->display_price($product['final_price'], $product['tax'], $product['qty']) . '</span>';
              echo '<h5 class="mb-1">' . $product['name'] . '<small> x ' . $product['qty'] . '</small></h5>';

              if ( (isset($product['attributes'])) && (count($product['attributes']) > 0) ) {
                echo '<p class="w-100 mb-1">';
                foreach ($product['attributes'] as $attribute) {
                  echo '- ' . $attribute['option'] . ': ' . $attribute['value'] . '<br>';
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
          $address = $customer_data->get_module('address');
          if ($_SESSION['sendto']) {
            echo '<li class="list-group-item">';
              echo '<i class="fas fa-shipping-fast fa-fw fa-3x float-right text-black-50"></i>';
              echo '<h5 class="mb-0">' . HEADING_DELIVERY_ADDRESS . '<small><a class="font-weight-lighter ml-2" href="' . tep_href_link('checkout_shipping_address.php', '', 'SSL') . '">' . TEXT_EDIT . '</a></small></h5>';
              echo '<p class="w-100 mb-1">' . $address->format($order->delivery, 1, ' ', '<br>') . '</p>';
            echo '</li>';
          }

          echo '<li class="list-group-item">';
            echo '<i class="fas fa-file-invoice-dollar fa-fw fa-3x float-right text-black-50"></i>';
            echo '<h5 class="mb-0">' . HEADING_BILLING_ADDRESS . '<small><a class="font-weight-lighter ml-2" href="' . tep_href_link('checkout_payment_address.php', '', 'SSL') . '">' . TEXT_EDIT . '</a></small></h5>';
            echo '<p class="w-100 mb-1">' . $address->format($order->billing, 1, ' ', '<br>') . '</p>';
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
          <i class="fas fa-comments fa-fw fa-3x float-right text-black-50"></i>
          <?php
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
              foreach ($confirmation['fields'] as $field) {
                $fields .= $field['title'] . ' ' . $field['field'] . '<br>';
              }
              if (strlen($fields) > 4) echo substr($fields, 0, -4);
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
    $parameters = ['style' => 'progress-bar progress-bar-striped progress-bar-animated bg-info', 'markers' => ['position' => 3, 'min' => 0, 'max' => 100, 'now' => 100]];
    echo $OSCOM_Hooks->call('progress', 'progressBar', $parameters);
    ?>
  </div>

</form>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
