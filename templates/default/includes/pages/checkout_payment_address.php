<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $OSCOM_Hooks->register_pipeline('progress');

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_payment.php'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('checkout_payment_address.php'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?= HEADING_TITLE ?></h1>

<?php
  if ($messageStack->size($message_stack_area) > 0) {
    echo $messageStack->output($message_stack_area);
  }
?>

  <div class="row">
    <div class="col-sm-7">
      <h5 class="mb-1"><?= TABLE_HEADING_ADDRESS_BOOK_ENTRIES ?></h5>
      <div><?= tep_draw_form('select_address', tep_href_link('checkout_payment_address.php'), 'post', '', true) ?>
        <table class="table border-right border-left border-bottom table-hover m-0">
          <?php
  $addresses_query = $customer->get_all_addresses_query();
  while ($address = $addresses_query->fetch_assoc()) {
?>
          <tr class="table-selection">
            <td><label for="cpa_<?= $address['address_book_id'] ?>"><?= $customer_data->get_module('address')->format($address, true, ' ', ', ') ?></label></td>
            <td align="text-right">
              <div class="custom-control custom-radio custom-control-inline">
                <?= tep_draw_selection_field('address', 'radio', $address['address_book_id'], ($address['address_book_id'] == $_SESSION['billto']), 'id="cpa_' . $address['address_book_id'] . '" aria-describedby="cpa_' . $address['address_book_id'] . '" class="custom-control-input"') ?>
                <label class="custom-control-label" for="cpa_<?= $address['address_book_id'] ?>">&nbsp;</label>
              </div>
            </td>
          </tr>
          <?php
  }
?>
        </table>
        <div class="buttonSet mt-1">
          <?= tep_draw_hidden_field('action', 'submit') . tep_draw_button(BUTTON_SELECT_ADDRESS, 'fas fa-user-cog', null, 'primary', null, 'btn-success btn-lg btn-block') ?>
        </div>
      </form></div>
    </div>
    <div class="col-sm-5">
      <h5 class="mb-1"><?= TABLE_HEADING_PAYMENT_ADDRESS ?></h5>
      <div class="border">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><?= PAYMENT_FA_ICON . $customer->make_address_label($_SESSION['billto'], true, ' ', '<br>') ?>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <?php
  if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) {
?>

    <hr>

    <h5 class="mb-1"><?= TABLE_HEADING_NEW_PAYMENT_ADDRESS ?></h5>

    <p class="font-weight-lighter"><?= TEXT_CREATE_NEW_PAYMENT_ADDRESS ?></p>

    <?php
    echo tep_draw_form('checkout_new_address', tep_href_link('checkout_payment_address.php'), 'post', '', true) . PHP_EOL;
    require $oscTemplate->map_to_template('checkout_new_address.php', 'component');
    echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
    echo tep_draw_hidden_field('action', 'submit');
    echo tep_draw_button(BUTTON_ADD_NEW_ADDRESS, 'fas fa-user-cog', null, 'primary', null, 'btn-success btn-lg btn-block');
    echo '</form>' . PHP_EOL;
  }
?>

  <div class="buttonSet">
    <?= tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('checkout_payment.php'), null, null, 'btn-light mt-1') ?>
  </div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
