<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  if (!isset($_SESSION['customer_id'])) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  $message_stack_area = 'addressbook';

// needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/address_book_process.php";

  if (is_numeric($_GET['delete'] ?? null) && tep_validate_form_action_is('deleteconfirm', 2)) {
    if ((int)$_GET['delete'] == $customer->get_default_address_id()) {
      $messageStack->add_session('addressbook', WARNING_PRIMARY_ADDRESS_DELETION, 'warning');
    } else {
      tep_db_query("DELETE FROM address_book WHERE address_book_id = " . (int)$_GET['delete'] . " and customers_id = " . (int)$_SESSION['customer_id']);

      $messageStack->add_session($message_stack_area, SUCCESS_ADDRESS_BOOK_ENTRY_DELETED, 'success');
    }

    tep_redirect(tep_href_link('address_book.php', '', 'SSL'));
  }

// error checking when updating or adding an entry
  if (tep_validate_form_action_is(['process', 'update'])) {
    $customer_details = $customer_data->process($customer_data->get_fields_for_page('address_book'));
    if ($customer_details) {
      if ($_POST['action'] == 'update') {
        $check_query = tep_db_query("SELECT * FROM address_book WHERE address_book_id = '" . (int)$_GET['edit'] . "' AND customers_id = " . (int)$_SESSION['customer_id'] . " LIMIT 1");
        if (tep_db_num_rows($check_query) === 1) {
          if ( 'on' === ($_POST['primary'] ?? null) ) {
            $table = 'both';
            $customer_details['default_address_id'] = (int)$_GET['edit'];
          } else {
            $table = 'address_book';
          }
          $customer_data->update($customer_details, ['address_book_id' => (int)$_GET['edit'], 'id' => (int)$_SESSION['customer_id']], $table);

          $messageStack->add_session($message_stack_area, SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED, 'success');
        }
      } else {
        if ($customer->count_addresses() < MAX_ADDRESS_BOOK_ENTRIES) {
          if (!isset($customer_details['id'])) {
            $customer_details['id'] = (int)$_SESSION['customer_id'];
          }
          $customer_data->add_address($customer_details);

          if ( 'on' === ($_POST['primary'] ?? null) ) {
            $customer_data->update(['default_address_id' => $customer_details['address_book_id']], ['id' => (int)$_SESSION['customer_id']], 'customers');
          }

          $messageStack->add_session($message_stack_area, SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED, 'success');
        }
      }

      tep_redirect(tep_href_link('address_book.php', '', 'SSL'));
    }
  }

  if (is_numeric($_GET['edit'] ?? null)) {
    if (is_null($customer->fetch_to_address((int)$_GET['edit']))) {
      $messageStack->add_session($message_stack_area, ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY);

      tep_redirect(tep_href_link('address_book.php', '', 'SSL'));
    }

    $page_heading = HEADING_TITLE_MODIFY_ENTRY;
    $navbar_title_3 = NAVBAR_TITLE_MODIFY_ENTRY;
    $navbar_link_3 = tep_href_link('address_book_process.php', 'edit=' . $_GET['edit'], 'SSL');
    $back_link = tep_href_link('address_book.php', '', 'SSL');
  } elseif (is_numeric($_GET['delete'] ?? null)) {
    if ($_GET['delete'] == $customer->get_default_address_id()) {
      $messageStack->add_session($message_stack_area, WARNING_PRIMARY_ADDRESS_DELETION, 'warning');

      tep_redirect(tep_href_link('address_book.php', '', 'SSL'));
    } else {
      $check_query = tep_db_query("SELECT COUNT(*) AS total FROM address_book WHERE address_book_id = " . (int)$_GET['delete'] . " AND customers_id = " . (int)$_SESSION['customer_id']);
      $check = tep_db_fetch_array($check_query);

      if ($check['total'] < 1) {
        $messageStack->add_session($message_stack_area, ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY);

        tep_redirect(tep_href_link('address_book.php', '', 'SSL'));
      }
    }

    $page_heading = HEADING_TITLE_DELETE_ENTRY;
    $navbar_title_3 = NAVBAR_TITLE_DELETE_ENTRY;
    $navbar_link_3 = tep_href_link('address_book_process.php', 'delete=' . $_GET['delete'], 'SSL');
  } else {
    if ($customer->count_addresses() >= MAX_ADDRESS_BOOK_ENTRIES) {
      $messageStack->add_session($message_stack_area, ERROR_ADDRESS_BOOK_FULL);

      tep_redirect(tep_href_link('address_book.php', '', 'SSL'));
    }

    $entry = [];
    $page_heading = HEADING_TITLE_ADD_ENTRY;
    $navbar_title_3 = NAVBAR_TITLE_ADD_ENTRY;
    $navbar_link_3 = tep_href_link('address_book_process.php', '', 'SSL');

    if (count($navigation->snapshot) > 0) {
      $back_link = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], [session_name()]), $navigation->snapshot['mode']);
    } else {
      $back_link = tep_href_link('address_book.php', '', 'SSL');
    }
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('address_book.php', '', 'SSL'));
  $breadcrumb->add($navbar_title_3, $navbar_link_3);

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo $page_heading; ?></h1>

<?php
  if ($messageStack->size($message_stack_area) > 0) {
    echo $messageStack->output($message_stack_area);
  }

  if (isset($_GET['delete'])) {
?>

<div class="contentContainer">

  <div class="row">
    <div class="col-sm-8">
      <div class="alert alert-danger" role="alert"><?php echo DELETE_ADDRESS_DESCRIPTION; ?></div>
    </div>
    <div class="col-sm-4">
      <div class="card mb-2">
        <div class="card-header"><?php echo DELETE_ADDRESS_TITLE; ?></div>

        <div class="card-body">
          <?php echo $customer->make_address_label((int)$_GET['delete'], true, ' ', '<br />'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_DELETE, 'fas fa-trash-alt', tep_href_link('address_book_process.php', 'delete=' . $_GET['delete'] . '&action=deleteconfirm&formid=' . md5($_SESSION['sessiontoken']), 'SSL'), 'primary', NULL, 'btn-danger btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('address_book.php', '', 'SSL')); ?></p>
  </div>

</div>

<?php
  } else {
    echo tep_draw_form('addressbook', tep_href_link('address_book_process.php', (isset($_GET['edit']) ? 'edit=' . $_GET['edit'] : ''), 'SSL'), 'post', '', true);
    if (is_numeric($_GET['edit'] ?? null)) {
      echo tep_draw_hidden_field('action', 'update') . tep_draw_hidden_field('edit', $_GET['edit']);
      $action_button = tep_draw_button(IMAGE_BUTTON_UPDATE, 'fas fa-sync', null, 'primary', null, 'btn-success btn-lg btn-block');
    } else {
      echo tep_draw_hidden_field('action', 'process');
      $action_button = tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block');
    }
?>

<div class="contentContainer">

<?php
    include 'includes/modules/address_book_details.php';
?>

  <div class="buttonSet">
    <div class="text-right"><?php echo $action_button; ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', $back_link); ?></p>
  </div>

</div>

</form>

<?php
  }
?>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
