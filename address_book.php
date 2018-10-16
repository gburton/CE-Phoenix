<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  require('includes/languages/' . $language . '/address_book.php');

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('address_book.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size('addressbook') > 0) {
    echo $messageStack->output('addressbook');
  }
?>

<div class="contentContainer">
  
  <h4><?php echo PRIMARY_ADDRESS_TITLE; ?></h4>

  <div class="row">
  
    <div class="col-sm-8">
      <div class="alert alert-info"><?php echo PRIMARY_ADDRESS_DESCRIPTION; ?></div>
    </div>
    
    <div class="col-sm-4">
      <div class="card text-white bg-info">
        <div class="card-header"><?php echo PRIMARY_ADDRESS_TITLE; ?></div>

        <div class="card-body">
          <?php echo tep_address_label($customer_id, $customer_default_address_id, true, ' ', '<br />'); ?>
        </div>
      </div>
    </div>
    
  </div>

  <div class="clearfix"></div>

  <h4><?php echo ADDRESS_BOOK_TITLE; ?></h4>
  
  <div class="alert alert-danger"><?php echo sprintf(TEXT_MAXIMUM_ENTRIES, MAX_ADDRESS_BOOK_ENTRIES); ?></div>

  <div class="row">
<?php
  $addresses_query = tep_db_query("select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "' order by firstname, lastname");
  while ($addresses = tep_db_fetch_array($addresses_query)) {
    $format_id = tep_get_address_format_id($addresses['country_id']);
?>
    <div class="col-sm-4">
      <div class="card <?php echo ($addresses['address_book_id'] == $customer_default_address_id) ? ' text-white bg-info' : ' bg-light'; ?>">
        <div class="card-header"><?php echo tep_output_string_protected($addresses['firstname'] . ' ' . $addresses['lastname']); ?></strong><?php if ($addresses['address_book_id'] == $customer_default_address_id) echo '&nbsp;<small><i>' . PRIMARY_ADDRESS . '</i></small>'; ?></div>
        <div class="card-body">
          <?php echo tep_address_format($format_id, $addresses, true, ' ', '<br />'); ?>
        </div>
        <div class="card-footer text-center"><?php echo tep_draw_button(SMALL_IMAGE_BUTTON_EDIT, 'fa fa-file', tep_href_link('address_book_process.php', 'edit=' . $addresses['address_book_id'], 'SSL'), null, null, 'btn btn-dark btn-sm') . ' ' . tep_draw_button(SMALL_IMAGE_BUTTON_DELETE, 'fas fa-trash-alt', tep_href_link('address_book_process.php', 'delete=' . $addresses['address_book_id'], 'SSL'), null, null, 'btn btn-dark btn-sm'); ?></div>
      </div>
    </div>
<?php
  }
?>
  </div>
  
  <div class="buttonSet">
<?php
  if (tep_count_customer_address_book_entries() < MAX_ADDRESS_BOOK_ENTRIES) {
?>
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_ADD_ADDRESS, 'fa fa-home', tep_href_link('address_book_process.php', '', 'SSL'), 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
<?php
  }
?>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', tep_href_link('account.php', '', 'SSL')); ?></p>
  </div>

</div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
