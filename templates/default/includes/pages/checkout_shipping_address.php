<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $OSCOM_Hooks->register_pipeline('progress');

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_shipping.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('checkout_shipping_address.php', '', 'SSL'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size($message_stack_area) > 0) {
    echo $messageStack->output($message_stack_area);
  }
?>

<div class="contentContainer">

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
    require $oscTemplate->map_to_template('checkout_new_address.php', 'component');
    echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
    echo tep_draw_hidden_field('action', 'submit');
    echo tep_draw_button(BUTTON_ADD_NEW_ADDRESS, 'fas fa-user-cog', null, 'primary', null, 'btn-success btn-lg btn-block');
    echo '</form>' . PHP_EOL;
  }
?>

  <div class="buttonSet">
    <?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('checkout_shipping.php', '', 'SSL'), null, null, 'btn-light mt-1'); ?>
  </div>
</div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
