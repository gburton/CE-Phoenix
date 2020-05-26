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
  $OSCOM_Hooks->register_pipeline('loginRequired');

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($_SESSION['cart']->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

  $OSCOM_Hooks->register_pipeline('progress');

  if (isset($_SESSION['sendto'])) {
    if ( (is_numeric($_SESSION['sendto']) && empty($customer->fetch_to_address($_SESSION['sendto']))) || ([] === $_SESSION['sendto']) ) {
      $_SESSION['sendto'] = $customer->get('default_address_id');
      unset($_SESSION['shipping']);
    }
  } else {
    // if no shipping destination address was selected, use the customer's own address as default
    $_SESSION['sendto'] = $customer->get('default_address_id');
  }

  $order = new order();

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
  if (isset($_SESSION['cartID']) && ($_SESSION['cartID'] != $_SESSION['cart']->cartID)) {
    unset($_SESSION['shipping']);
  }

  $_SESSION['cartID'] = $_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
  if ($order->content_type == 'virtual') {
    $_SESSION['shipping'] = false;
    $_SESSION['sendto'] = false;
    tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
  }

  $total_weight = $_SESSION['cart']->show_weight();
  $total_count = $_SESSION['cart']->count_contents();

// load all enabled shipping modules
  $shipping_modules = new shipping();

  $free_shipping = false;
  if ( ot_shipping::is_eligible_free_shipping($order->delivery['country_id'], $order->info['total']) ) {
      $free_shipping = true;

      include "includes/languages/$language/modules/order_total/ot_shipping.php";
  }

  $module_count = tep_count_shipping_modules();
// process the selected shipping method
  if (tep_validate_form_action_is('process')) {
    function tep_process_selected_shipping_method() {
      if (tep_not_null($_POST['comments'])) {
        $_SESSION['comments'] = tep_db_prepare_input($_POST['comments']);
      }

      if ( ($GLOBALS['module_count'] <= 0) && !$GLOBALS['free_shipping'] ) {
        if ( defined('SHIPPING_ALLOW_UNDEFINED_ZONES') && (SHIPPING_ALLOW_UNDEFINED_ZONES == 'False') ) {
          unset($_SESSION['shipping']);
          return;
        }

        $_SESSION['shipping'] = false;
        tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
      }

      if ( (isset($_POST['shipping'])) && (strpos($_POST['shipping'], '_')) ) {
        $_SESSION['shipping'] = $_POST['shipping'];

        list($module, $shipping_method) = explode('_', $_SESSION['shipping']);
        if ('free_free' === $_SESSION['shipping']) {
          $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
          $quote[0]['methods'][0]['cost'] = '0';
        } elseif (is_object($GLOBALS[$module])) {
          $quote = $GLOBALS['shipping_modules']->quote($shipping_method, $module);
        } else {
          unset($_SESSION['shipping']);
          return;
        }

        if (isset($quote['error'])) {
          unset($_SESSION['shipping']);
          return;
        }

        if ( isset($quote[0]['methods'][0]['title'], $quote[0]['methods'][0]['cost']) ) {
          $way = '';
          if (!empty($quote[0]['methods'][0]['title'])) {
            $way = ' (' . $quote[0]['methods'][0]['title'] . ')';
          }

          $_SESSION['shipping'] = [
            'id' => $_SESSION['shipping'],
            'title' => ($GLOBALS['free_shipping'] ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . $way),
            'cost' => $quote[0]['methods'][0]['cost'],
          ];

          tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
        }
      }
    }

    tep_process_selected_shipping_method();
  }

// get all available shipping quotes
  $quotes = $shipping_modules->quote();

  if (!($_SESSION['shipping']->enabled ?? false)) {
    unset($_SESSION['shipping']);
  }

// if no shipping method has been selected, automatically select the cheapest method.
// if the module's status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
  if ( !isset($_SESSION['shipping']) || (!$_SESSION['shipping'] && (tep_count_shipping_modules() > 1)) ) {
    $_SESSION['shipping'] = $shipping_modules->cheapest();
  }

  require "includes/languages/$language/checkout_shipping.php";

  if ( defined('SHIPPING_ALLOW_UNDEFINED_ZONES') && (SHIPPING_ALLOW_UNDEFINED_ZONES == 'False') && !$_SESSION['shipping'] ) {
    $messageStack->add_session('checkout_address', ERROR_NO_SHIPPING_AVAILABLE_TO_SHIPPING_ADDRESS);
    tep_redirect(tep_href_link('checkout_shipping_address.php', '', 'SSL'));
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_shipping.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('checkout_shipping.php', '', 'SSL'));

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('checkout_address', tep_href_link('checkout_shipping.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process'); ?>

  <div class="row">
    <div class="col-sm-7">
      <h5 class="mb-1"><?php echo TABLE_HEADING_SHIPPING_METHOD; ?></h5>
      <div>
        <?php
        if ($module_count > 0) {
          if ($free_shipping == true) {
            ?>
        <div class="alert alert-info mb-0" role="alert">
          <p class="lead"><b><?php echo FREE_SHIPPING_TITLE; ?></b></p>
          <p class="lead"><?php echo sprintf(FREE_SHIPPING_DESCRIPTION, $currencies->format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER)) . tep_draw_hidden_field('shipping', 'free_free'); ?></p>
        </div>
            <?php
          } else {
            ?>
        <table class="table border-right border-left border-bottom table-hover m-0">
          <?php
          // GDPR - can't be checked by default?
          $checked = null;

          $n = count($quotes);
          foreach ($quotes as $quote) {
            $n2 = count($quote['methods']);
            foreach (($quote['methods'] ?? []) as $method) {
              // set the radio button to be checked if it is the method chosen
              // $checked = (($quote['id'] . '_' . $method['id'] == $shipping['id']) ? true : false);
              ?>
              <tr class="table-selection">
                <td>
                  <?php
                  echo $quote['module'];

                  if (tep_not_null($quote['icon'] ?? '')) {
                    echo '&nbsp;' . $quote['icon'];
                  }

                  if (isset($quote['error'])) {
                    echo '<div class="form-text">' . $quote['error'] . '</div>';
                  }

                  if (tep_not_null($method['title'])) {
                    echo '<div class="form-text">' . $method['title'] . '</div>';
                  }
                  ?>
                </td>
                <?php
                if ( ($n > 1) || ($n2 > 1) ) {
                  ?>
                  <td class="text-right">
                    <?php
                    if (isset($quote['error'])) {
                      echo '<div class="alert alert-error">' . $quote['error'] . '</div>';
                    } else {
                      echo '<div class="custom-control custom-radio custom-control-inline">';
                      echo tep_draw_radio_field('shipping',  $quote['id'] . '_' . $method['id'], $checked, 'id="d_' . $method['id'] . '" required aria-required="true" aria-describedby="d_' . $method['id'] . '" class="custom-control-input"');
                      echo '<label class="custom-control-label" for="d_' . $method['id'] . '">' . $currencies->format(tep_add_tax($method['cost'], (isset($quote['tax']) ? $quote['tax'] : 0))) . '</label>';
                      echo '</div>';
                    }
                    ?>
                  </td>
                  <?php
                } else {
                  ?>
                  <td class="text-right"><?php echo $currencies->format(tep_add_tax($method['cost'], (isset($quote['tax']) ? $quote['tax'] : 0))) . tep_draw_hidden_field('shipping', $quote['id'] . '_' . $method['id']); ?></td>
                  <?php
                }
                ?>
              </tr>
              <?php
              }
            }
          }
          ?>
        </table>
        <?php
        if ( !$free_shipping && (1 === $module_count) ) {
          ?>
          <p class="m-2 font-weight-lighter"><?php echo TEXT_ENTER_SHIPPING_INFORMATION; ?></p>
          <?php
        }
      }
      ?>
      </div>
    </div>

    <div class="col-sm-5">
      <h5 class="mb-1">
        <?php
        echo TABLE_HEADING_SHIPPING_ADDRESS;
        echo sprintf(LINK_TEXT_EDIT, 'font-weight-lighter ml-3', tep_href_link('checkout_shipping_address.php', '', 'SSL'));
        ?>
      </h5>
      <div class="border">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><?php echo SHIPPING_FA_ICON . $customer->make_address_label($_SESSION['sendto'], true, ' ', '<br>'); ?></li>
        </ul>
      </div>
    </div>
  </div>

  <hr>

  <div class="form-group row">
    <label for="inputComments" class="col-form-label col-sm-4 text-left text-sm-right"><?php echo ENTRY_COMMENTS; ?></label>
    <div class="col-sm-8">
      <?php
      echo tep_draw_textarea_field('comments', 'soft', 60, 5, ($_SESSION['comments'] ?? null), 'id="inputComments" placeholder="' . ENTRY_COMMENTS_PLACEHOLDER . '"');
      ?>
    </div>
  </div>

  <?php
  echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
  ?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(BUTTON_CONTINUE_CHECKOUT_PROCEDURE, 'fas fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
  </div>

  <?php
  $parameters = ['style' => 'progress-bar progress-bar-striped progress-bar-animated bg-info', 'markers' => ['position' => 1, 'min' => 0, 'max' => 100, 'now' => 33]];
  echo $OSCOM_Hooks->call('progress', 'progressBar', $parameters);
  ?>

</form>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
