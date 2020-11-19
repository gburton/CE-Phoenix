<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/


  require 'includes/application_top.php';

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('mail', 'preAction');

  if (isset($_POST['customers_email_address'])) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_sent_to = TEXT_ALL_CUSTOMERS;
        break;
      case '**D':
        $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
        break;
      default:
        if (filter_var($_POST['customers_email_address'], FILTER_VALIDATE_EMAIL)) {
          $mail_sent_to = $_POST['customers_email_address'];
        } elseif ($action) {
          $messageStack->add(sprintf(ERROR_INVALID_EMAIL, htmlspecialchars($_POST['customers_email_address'])), 'error');
          $action = '';
        }
    }
  } elseif ($action) {
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
    $action = '';
  }

  switch ($action) {
    case 'send_email_to_user':
      if ( !isset($_POST['customers_email_address']) || isset($_POST['back_x']) ) {
        break;
      }

      switch ($_POST['customers_email_address']) {
        case '***':
          $mail_query = tep_db_query($customer_data->build_read(['name', 'email_address'], 'customers'));
          break;
        case '**D':
          $mail_query = tep_db_query($customer_data->build_read(['name', 'email_address'], 'customers', ['newsletter' => true]));
          break;
        default:
          $mail_query = tep_db_query($customer_data->build_read(
            ['name', 'email_address'],
            'customers',
            ['email_address' => tep_db_prepare_input($_POST['customers_email_address'])]));
          break;
      }

      $from_name = tep_db_prepare_input($_POST['from_name']);
      $from_address = tep_db_prepare_input($_POST['from_address']);
      $subject = tep_db_prepare_input($_POST['subject']);
      $message = tep_db_prepare_input($_POST['message']);

      //Let's build a message object using the email class
      $mimemessage = new email();
      $mimemessage->add_message($message);
      $mimemessage->build_message();

      $count = 0;
      while ($mail = tep_db_fetch_array($mail_query)) {
        if ($mimemessage->send($customer_data->get('name', $mail), $customer_data->get('email_address', $mail), $from_name, $from_address, $subject)) {
          $count++;
        }
      }

      $OSCOM_Hooks->call('mail', 'sendEmailToUserAction');
      if ($count > 0) {
        $messageStack->add_session(sprintf(NOTICE_EMAIL_SENT_TO, $mail_sent_to), 'success');
      }
      tep_redirect(tep_href_link('mail.php'));
      break;
  }

  $OSCOM_Hooks->call('mail', 'postAction');
  require 'includes/template_top.php';
?>

  <h1 class="display-4 mb-2"><?= HEADING_TITLE; ?></h1>

<?php
  if ( ($action == 'preview') ) {
    echo tep_draw_form('mail', 'mail.php', 'action=send_email_to_user');
?>

      <table class="table table-striped">
        <tr>
          <th><?= TEXT_CUSTOMER; ?></th>
          <td><?= $mail_sent_to; ?></td>
        </tr>
        <tr>
          <th><?= TEXT_FROM; ?></th>
          <td><?= htmlspecialchars(stripslashes($_POST['from_name'])); ?></td>
        </tr>
        <tr>
          <th><?= TEXT_FROM_ADDRESS; ?></th>
          <td><?= htmlspecialchars(stripslashes($_POST['from_address'])); ?></td>
        </tr>
        <tr>
          <th><?= TEXT_SUBJECT; ?></th>
          <td><?= htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
        </tr>
        <tr>
          <th><?= TEXT_MESSAGE; ?></th>
          <td><?= nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
        </tr>
        <?= $OSCOM_Hooks->call('mail', 'formPreview'); ?>
      </table>

<?php
    /* Re-Post all POST'ed variables */
    foreach ($_POST as $key => $value) {
      if (!is_array($_POST[$key])) {
        echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
      }
    }
?>

      <div class="buttonSet">
<?php
    echo tep_draw_bootstrap_button(IMAGE_SEND_EMAIL, 'fas fa-paper-plane', null, 'primary', null, 'btn-success btn-block btn-lg');
    echo tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('mail.php'), 'primary', null, 'btn-light mt-2');
?>
      </div>
    </form>
<?php
  } else {
    echo tep_draw_form('mail', 'mail.php', 'action=preview');

    $customers = [];
    $customers[] = ['id' => '', 'text' => TEXT_SELECT_CUSTOMER];
    $customers[] = ['id' => '***', 'text' => TEXT_ALL_CUSTOMERS];
    $customers[] = ['id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS];

    $sql = $customer_data->add_order_by($customer_data->build_read(['sortable_name', 'email_address'], 'customers'), ['sortable_name']);
    $mail_query = tep_db_query($sql);
    while ($customers_values = tep_db_fetch_array($mail_query)) {
      $customers[] = [
        'id' => $customer_data->get('email_address', $customers_values),
        'text' => $customer_data->get('sortable_name', $customers_values) . ' (' . $customer_data->get('email_address', $customers_values) . ')',
      ];
    }
?>

      <div class="form-group row" id="zCustomer">
        <label for="Customer" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_CUSTOMER; ?></label>
        <div class="col-sm-9">
          <?= tep_draw_pull_down_menu('customers_email_address', $customers, ($_GET['customer'] ?? ''), 'id="Customer" required aria-required="true"'); ?>
        </div>
      </div>

      <div class="form-group row" id="zFromName">
        <label for="FromName" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_FROM; ?></label>
        <div class="col-sm-9">
          <?= tep_draw_input_field('from_name', STORE_OWNER, 'id="FromName" required aria-required="true"'); ?>
        </div>
      </div>

      <div class="form-group row" id="zFromAddress">
        <label for="FromAddress" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_FROM_ADDRESS; ?></label>
        <div class="col-sm-9">
          <?= tep_draw_input_field('from_address', STORE_OWNER_EMAIL_ADDRESS, 'id="FromAddress" required aria-required="true"'); ?>
        </div>
      </div>

      <div class="form-group row" id="zSubject">
        <label for="Subject" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_SUBJECT; ?></label>
        <div class="col-sm-9">
          <?= tep_draw_input_field('subject', null, 'id="Subject" required aria-required="true"'); ?>
        </div>
      </div>

      <div class="form-group row" id="zMessage">
        <label for="Message" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_MESSAGE; ?></label>
        <div class="col-sm-9">
          <?= tep_draw_textarea_field('message', 'soft', '60', '15', null, 'id="Message" required aria-required="true"'); ?>
        </div>
      </div>

      <?= $OSCOM_Hooks->call('mail', 'formNew'); ?>

      <div class="buttonSet">
        <?= tep_draw_bootstrap_button(IMAGE_PREVIEW, 'fas fa-eye', null, 'primary', null, 'btn-success btn-block btn-lg'); ?>
      </div>

    </form>
<?php
  }

  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
