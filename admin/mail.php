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
  if ( ($action == 'send_email_to_user') && isset($_POST['customers_email_address']) && !isset($_POST['back_x']) ) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_query = tep_db_query($customer_data->build_read(['name', 'email_address'], 'customers'));
        $mail_sent_to = TEXT_ALL_CUSTOMERS;
        break;
      case '**D':
        $mail_query = tep_db_query($customer_data->build_read(['name', 'email_address'], 'customers', ['newsletter' => true]));
        $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
        break;
      default:
        $customers_email_address = tep_db_prepare_input($_POST['customers_email_address']);

        $mail_query = tep_db_query($customer_data->build_read(['name', 'email_address'], 'customers', ['email_address' => $customers_email_address]));
        $mail_sent_to = $_POST['customers_email_address'];
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
    while ($mail = tep_db_fetch_array($mail_query)) {
      $mimemessage->send($customer_data->get('name', $mail), $customer_data->get('email_address', $mail), $from_name, $from_address, $subject);
    }

    tep_redirect(tep_href_link('mail.php', 'mail_sent_to=' . urlencode($mail_sent_to)));
  }

  if ( ($action == 'preview') && !isset($_POST['customers_email_address']) ) {
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
  }

  if (isset($_GET['mail_sent_to'])) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'success');
  }

  require 'includes/template_top.php';
?>

  <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ( ($action == 'preview') && isset($_POST['customers_email_address']) ) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_sent_to = TEXT_ALL_CUSTOMERS;
        break;
      case '**D':
        $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
        break;
      default:
        $mail_sent_to = $_POST['customers_email_address'];
        break;
    }

    echo tep_draw_form('mail', 'mail.php', 'action=send_email_to_user');
?>

      <table class="table table-striped">
        <tr>
          <th><?php echo TEXT_CUSTOMER; ?></th>
          <td><?php echo $mail_sent_to; ?></td>
        </tr>
        <tr>
          <th><?php echo TEXT_FROM; ?></th>
          <td><?php echo htmlspecialchars(stripslashes($_POST['from_name'])); ?></td>
        </tr>
        <tr>
          <th><?php echo TEXT_FROM_ADDRESS; ?></th>
          <td><?php echo htmlspecialchars(stripslashes($_POST['from_address'])); ?></td>
        </tr>
        <tr>
          <th><?php echo TEXT_SUBJECT; ?></th>
          <td><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
        </tr>
        <tr>
          <th><?php echo TEXT_MESSAGE; ?></th>
          <td><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
        </tr>
      </table>

      <div class="buttonSet">
<?php
    /* Re-Post all POST'ed variables */
    foreach ($_POST as $key => $value) {
      if (!is_array($_POST[$key])) {
        echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
      }
    }

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

      <div class="form-group row">
        <label for="Customer" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_CUSTOMER; ?></label>
        <div class="col-sm-9">
          <?php echo tep_draw_pull_down_menu('customers_email_address', $customers, ($_GET['customer'] ?? ''), 'id="Customer" required="required" aria-required="true"'); ?>
        </div>
      </div>

      <div class="form-group row">
        <label for="FromName" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_FROM; ?></label>
        <div class="col-sm-9">
          <?php echo tep_draw_input_field('from_name', STORE_OWNER, 'id="FromName" required="required" aria-required="true"'); ?>
        </div>
      </div>

      <div class="form-group row">
        <label for="FromAddress" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_FROM_ADDRESS; ?></label>
        <div class="col-sm-9">
          <?php echo tep_draw_input_field('from_address', STORE_OWNER_EMAIL_ADDRESS, 'id="FromAddress" required="required" aria-required="true"'); ?>
        </div>
      </div>

      <div class="form-group row">
        <label for="Subject" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_SUBJECT; ?></label>
        <div class="col-sm-9">
          <?php echo tep_draw_input_field('subject', null, 'id="Subject" required="required" aria-required="true"'); ?>
        </div>
      </div>

      <div class="form-group row">
        <label for="Message" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_MESSAGE; ?></label>
        <div class="col-sm-9">
          <?php echo tep_draw_textarea_field('message', 'soft', '60', '15', null, 'id="Message" required="required" aria-required="true"'); ?>
        </div>
      </div>

      <div class="buttonSet">
        <?php echo tep_draw_bootstrap_button(IMAGE_PREVIEW, 'fas fa-eye', null, 'primary', null, 'btn-success btn-block btn-lg'); ?>
      </div>

    </form>
<?php
  }

  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
