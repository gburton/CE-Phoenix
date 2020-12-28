<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('address_book.php'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?= HEADING_TITLE ?></h1>

<?php
  if ($messageStack->size('addressbook') > 0) {
    echo $messageStack->output('addressbook');
  }
?>

  <h4><?= PRIMARY_ADDRESS_TITLE ?></h4>

  <div class="row">

    <div class="col-sm-8">
      <div class="alert alert-info" role="alert"><?= PRIMARY_ADDRESS_DESCRIPTION ?></div>
    </div>

    <div class="col-sm-4">
      <div class="card mb-2 text-white bg-info">
        <div class="card-header"><?= PRIMARY_ADDRESS_TITLE ?></div>

        <div class="card-body"><?= $customer->make_address_label($customer->get_default_address_id(), true, ' ', '<br>') ?></div>
      </div>
    </div>

  </div>

  <div class="w-100"></div>

  <h4><?= ADDRESS_BOOK_TITLE ?></h4>

  <div class="alert alert-danger" role="alert"><?= sprintf(TEXT_MAXIMUM_ENTRIES, MAX_ADDRESS_BOOK_ENTRIES) ?></div>

  <div class="row">
    <?php
    $addresses_query = $customer->get_all_addresses_query();
    while ($address = $addresses_query->fetch_assoc()) {
      ?>
      <div class="col-sm-4">
        <div class="card mb-2 <?= ($address['address_book_id'] == $customer->get_default_address_id()) ? ' text-white bg-info' : ' bg-light' ?>">
          <div class="card-header"><?= htmlspecialchars($customer_data->get('name', $address)) ?></strong><?php if ($customer->get('default_address_id') == $address['address_book_id']) echo '&nbsp;<small><i>' . PRIMARY_ADDRESS . '</i></small>'; ?></div>
          <div class="card-body">
            <?= $customer_data->get_module('address')->format($address, true, ' ', '<br>') ?>
          </div>
          <div class="card-footer text-center"><?= tep_draw_button(SMALL_IMAGE_BUTTON_EDIT, 'fas fa-file', tep_href_link('address_book_process.php', 'edit=' . $address['address_book_id']), null, null, 'btn btn-dark btn-sm') . ' ' . tep_draw_button(SMALL_IMAGE_BUTTON_DELETE, 'fas fa-trash-alt', tep_href_link('address_book_process.php', 'delete=' . $address['address_book_id']), null, null, 'btn btn-dark btn-sm') ?></div>
        </div>
      </div>
      <?php
      }
    ?>
  </div>

  <div class="buttonSet">
    <?php
    if ($customer->count_addresses() < MAX_ADDRESS_BOOK_ENTRIES) {
      ?>
      <div class="text-right"><?= tep_draw_button(IMAGE_BUTTON_ADD_ADDRESS, 'fas fa-home', tep_href_link('address_book_process.php'), 'primary', null, 'btn-success btn-lg btn-block') ?></div>
      <?php
      }
    ?>
    <p><?= tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('account.php')) ?></p>
  </div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
