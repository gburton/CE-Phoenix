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

// if there is nothing in the customer's cart, redirect to the shopping cart page
  if ($_SESSION['cart']->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

  $OSCOM_Hooks->register_pipeline('progress');

  // needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/checkout_shipping_address.php";

  $order = new order();

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
  if ($order->content_type == 'virtual') {
    $_SESSION['shipping'] = false;
    $_SESSION['sendto'] = false;
    tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
  }

  $message_stack_area = 'checkout_address';

  $error = false;
  $process = false;
  if (tep_validate_form_action_is('submit')) {
// process a new shipping address
    $customer_details = $customer_data->process($customer_data->get_fields_for_page('address_book'));
    $OSCOM_Hooks->call('siteWide', 'injectFormVerify');
    if (tep_form_processing_is_valid()) {
      $customer_details['id'] = $customer->get_id();
      $customer_data->add_address($customer_details);

      $_SESSION['sendto'] = $customer_data->get('address_book_id', $customer_details);

      unset($_SESSION['shipping']);

      tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
    }
  } elseif (isset($_POST['address']) && tep_validate_form_action_is('select')) {
    // change to the selected shipping destination
    $reset_shipping = (isset($_SESSION['sendto']) && ($_SESSION['sendto'] != $_POST['address']) && isset($_SESSION['shipping']));
    $_SESSION['sendto'] = $_POST['address'];

    if ($customer->fetch_to_address((int)$_SESSION['sendto'])) {
      if ($reset_shipping) {
        unset($_SESSION['shipping']);
      }

      tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
    } else {
      unset($_SESSION['sendto']);
    }
  }

// if no shipping destination address was selected, use their own address as default
  if (!isset($_SESSION['sendto'])) {
    $_SESSION['sendto'] = $customer->get_default_address_id();
    $sendto =& $_SESSION['sendto'];
  }

  $addresses_count = $customer->count_addresses();

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_shipping.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('checkout_shipping_address.php', '', 'SSL'));

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size($message_stack_area) > 0) {
    echo $messageStack->output($message_stack_area);
  }
?>

  <div class="row">
    <div class="col-sm-7">
      <h5 class="mb-1"><?php echo TABLE_HEADING_ADDRESS_BOOK_ENTRIES; ?></h5>
      <div><?php echo tep_draw_form('select_address', tep_href_link('checkout_shipping_address.php', '', 'SSL'), 'post', '', true); ?>
        <table class="table border-right border-left border-bottom table-hover m-0">
          <tbody>
            <?php
  $address_query = $customer->get_all_addresses_query();
  while ($address = tep_db_fetch_array($address_query)) {
?>
            <tr class="table-selection">
              <td><label for="csa_<?php echo $address['address_book_id']; ?>"><?php echo $customer_data->get_module('address')->format($address, true, ' ', ', '); ?></label></td>
              <td align="text-right">
                <div class="custom-control custom-radio custom-control-inline">
                  <?php echo tep_draw_radio_field('address', $address['address_book_id'], ($address['address_book_id'] == $_SESSION['sendto']), 'id="csa_' . $address['address_book_id'] . '" aria-describedby="csa_' . $address['address_book_id'] . '" class="custom-control-input"'); ?>
                  <label class="custom-control-label" for="csa_<?php echo $address['address_book_id']; ?>">&nbsp;</label>
                </div>
              </td>
            </tr>
            <?php
  }
?>
          </tbody>
        </table>
        <div class="buttonSet mt-1">
          <?php echo tep_draw_hidden_field('action', 'select') . tep_draw_button(BUTTON_SELECT_ADDRESS, 'fas fa-user-cog', null, 'primary', null, 'btn-success btn-lg btn-block'); ?>
        </div>
      </form></div>
    </div>
    <div class="col-sm-5">
      <h5 class="mb-1"><?php echo TABLE_HEADING_SHIPPING_ADDRESS; ?></h5>
      <div class="border">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><?php echo SHIPPING_FA_ICON . $customer->make_address_label($_SESSION['sendto'], true, ' ', '<br>'); ?></li>
        </ul>
      </div>
    </div>
  </div>

<?php
  if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) {
?>
    <hr>

    <h5 class="mb-1"><?php echo TABLE_HEADING_NEW_SHIPPING_ADDRESS; ?></h5>

    <p class="font-weight-lighter"><?php echo TEXT_CREATE_NEW_SHIPPING_ADDRESS; ?></p>
<?php
    echo tep_draw_form('checkout_new_address', tep_href_link('checkout_shipping_address.php', '', 'SSL'), 'post', '', true) . PHP_EOL;
      require 'includes/modules/checkout_new_address.php';
      echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
      echo tep_draw_hidden_field('action', 'submit');
      echo tep_draw_button(BUTTON_ADD_NEW_ADDRESS, 'fas fa-user-cog', null, 'primary', null, 'btn-success btn-lg btn-block');
    echo '</form>' . PHP_EOL;
  }
?>

  <div class="buttonSet">
    <?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('checkout_shipping.php', '', 'SSL'), null, null, 'btn-light mt-1'); ?>
  </div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
