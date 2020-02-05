<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $OSCOM_Hooks->register('progress');

// if the customer is not logged on, redirect them to the login page
  if (!isset($_SESSION['customer_id'])) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

  if (!$customer_data->has('address')) {
    tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
  }

  // needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/checkout_payment_address.php";

  $message_stack_area = 'checkout_address';

  $error = false;
  $process = false;
  if (tep_validate_form_action_is('submit')) {
    $customer_details = $customer_data->process($customer_data->get_fields_for_page('address_book'));
    if ($customer_details) {
      $customer_details['id'] = $customer->get_id();
      $customer_data->add_address($customer_details);

      $_SESSION['billto'] = $customer_data->get('address_book_id', $customer_details);

      unset($_SESSION['payment']);

      tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
    } elseif (isset($_POST['address'])) {
      // process the selected billing destination
      $reset_payment = isset($_SESSION['billto']) && ($_SESSION['billto'] != $_POST['address']) && isset($_SESSION['payment']);
      $_SESSION['billto'] = $_POST['address'];

      if ($customer->fetch_to_address($_SESSION['billto'])) {
        if ($reset_payment) {
          unset($_SESSION['payment']);
        }
        tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
      } else {
        unset($_SESSION['billto']);
      }
    } else {
      // no addresses to select from - customer decided to keep the current assigned address
      $_SESSION['billto'] = $customer->get_default_address_id();

      tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
    }
  }

// if no billing destination address was selected, use their own address as default
  if (!isset($_SESSION['billto'])) {
    $_SESSION['billto'] = $customer->get_default_address_id();
    $billto =& $_SESSION['billto'];
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_payment.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('checkout_payment_address.php', '', 'SSL'));

  $addresses_count = $customer->count_addresses();

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size($message_stack_area) > 0) {
    echo $messageStack->output($message_stack_area);
  }

  echo tep_draw_form('checkout_address', tep_href_link('checkout_payment_address.php', '', 'SSL'), 'post', '', true);
?>

<div class="contentContainer">

  <div class="row">
    <div class="col-sm-7">
      <h2 class="h5 mb-1"><?php echo TABLE_HEADING_ADDRESS_BOOK_ENTRIES; ?></h2>
      <div>
        <table class="table border-right border-left border-bottom table-hover m-0">
<?php
  $addresses_query = $customer->get_all_addresses_query();
  while ($address = tep_db_fetch_array($addresses_query)) {
?>
            <tr class="table-selection">
              <td><label for="cpa_<?php echo $address['address_book_id']; ?>"><?php echo $customer_data->get_module('address')->format($address, true, ' ', ', '); ?></label></td>
              <td align="text-right">
                <div class="custom-control custom-radio custom-control-inline">
<?php
    echo tep_draw_radio_field('address', $address['address_book_id'], ($address['address_book_id'] == $billto),
      'id="cpa_' . $address['address_book_id'] . '" aria-describedby="cpa_' . $address['address_book_id'] . '" class="custom-control-input"');
?>
                  <label class="custom-control-label" for="cpa_<?php echo $address['address_book_id']; ?>">&nbsp;</label>
                </div>
              </td>
            </tr>
<?php
  }
?>
        </table>
      </div>
    </div>
    <div class="col-sm-5">
      <h2 class="h5 mb-1"><?php echo TABLE_HEADING_PAYMENT_ADDRESS; ?></h2>
      <div class="border">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><?php echo PAYMENT_FA_ICON . $customer->make_address_label($billto, true, ' ', '<br />'); ?>
          </li>
        </ul>
      </div>
    </div>
  </div>

<?php
  if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) {
?>

  <hr>

  <h2 class="h5 mb-1"><?php echo TABLE_HEADING_NEW_PAYMENT_ADDRESS; ?></h2>

  <p class="font-weight-lighter"><?php echo TEXT_CREATE_NEW_PAYMENT_ADDRESS; ?></p>

<?php
    require 'includes/modules/checkout_new_address.php';
  }

  echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_hidden_field('action', 'submit') . tep_draw_button(BUTTON_CONTINUE_CHECKOUT_PROCEDURE, 'fas fa-user-cog', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p class="mt-1"><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('checkout_payment.php', '', 'SSL')); ?></p>
  </div>

</div>

</form>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
