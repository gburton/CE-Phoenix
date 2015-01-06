<?php
/*
  $Id: gv_mail.php, v2.3.3.4 22:54:01 mommaroodles Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2014 osCommerce
  Released under the GNU General Public License
*/
  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  if ( ($action == 'send_email_to_user') && isset($_POST['customers_email_address']) && !isset($_POST['back_x']) ) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_query = tep_db_query("select customers_id, customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS);
        $mail_sent_to = TEXT_ALL_CUSTOMERS;
        break;
      case '**D':
        $mail_query = tep_db_query("select customers_id, customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
        $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
        break;
      default:
        $customers_email_address = tep_db_prepare_input($_POST['customers_email_address']);
        $mail_query = tep_db_query("select customers_id, customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($customers_email_address) . "'");
        $mail_sent_to = $_POST['customers_email_address'];
        break;
    }
    // create a coupon code from customers email address
    $id1 = create_coupon_code($mail['customers_email_address']);
    $from = tep_db_prepare_input($_POST['from']);
    $subject = tep_db_prepare_input($_POST['subject']);
	$amount = tep_db_prepare_input($_POST['amount']);
	while ($mail = tep_db_fetch_array($mail_query)) {
	$customers_id = $mail['customers_id'];
	$customers_name = $mail['customers_firstname'] . ' ' . $mail['customers_lastname'];
    $message = tep_db_prepare_input($_POST['message']);
	$message .= 'Dear ' . $customers_name . "\n\n";
	$message .= TEXT_GV_WORTH  . $currencies->format($_POST['amount']) . "\n\n";
    $message .= TEXT_TO_REDEEM;
    $message .= TEXT_WHICH_IS . $id1 . TEXT_IN_CASE . "\n\n";
    $message .= TEXT_OR_VISIT . HTTP_SERVER . DIR_WS_CATALOG . TEXT_ENTER_CODE . "\n\n";
    $message .= TEXT_SIGN_OFF . "\n\n";
    //Let's build a message object using the email class
    $mimemessage = new email(array('X-Mailer: osCommerce'));
    // Build the text version
    $text = strip_tags($message);
    if (EMAIL_USE_HTML == 'true') {
      $mimemessage->add_html($message, $text);
    } else {
      $mimemessage->add_text($text);
    }
      $mimemessage->build_message();
      $mimemessage->send($mail['customers_firstname'] . ' ' . $mail['customers_lastname'], $mail['customers_email_address'], '', $from, $subject);
// Now create the coupon main and email entry
       $coupon_id = tep_db_insert_id();
       tep_db_query("insert into " . TABLE_COUPONS . " (coupon_id, coupon_code, coupon_type, coupon_amount, coupon_status, date_created) values ('" . (int)$coupon_id . "','" . $id1 . "', 'G', '" . $_POST['amount'] . "', '1', now() )");
       $coupon_id = tep_db_insert_id();
       tep_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, sent_lastname, emailed_to, date_sent) values ('" . (int)$coupon_id . "', '". $mail['customers_id'] . "', '" . $mail['customers_firstname'] . "', '" . $mail['customers_lastname'] . "', '" . $mail['customers_email_address'] . "', now() )");
}
    tep_redirect(tep_href_link(FILENAME_GV_MAIL, 'mail_sent_to=' . urlencode($mail_sent_to)));
  }
  if ( ($_GET['action'] == 'preview') && (!$_POST['customers_email_address']) ) {
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
  }
  if ( ($action == 'preview') && (!$_POST['amount']) ) {
    $messageStack->add(ERROR_NO_AMOUNT_SELECTED, 'error');
  }
  if ($_GET['mail_sent_to']) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'success');
  }
  require(DIR_WS_INCLUDES . 'template_top.php');
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
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
?>
          <tr><?php echo tep_draw_form('mail', FILENAME_GV_MAIL, 'action=send_email_to_user'); ?>
            <td><table border="0" width="100%" cellpadding="0" cellspacing="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><strong><?php echo TEXT_CUSTOMER; ?></strong><br><?php echo $mail_sent_to; ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><strong><?php echo TEXT_FROM; ?></strong><br><?php echo htmlspecialchars(stripslashes($_POST['from'])); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><strong><?php echo TEXT_SUBJECT; ?></strong><br><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><strong><?php echo TEXT_AMOUNT; ?></strong><br><?php echo htmlspecialchars(stripslashes($_POST['amount'])); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><strong><?php echo TEXT_MESSAGE; ?></strong><br><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText" align="right">
<?php
/* Re-Post all POST'ed variables */
    reset($_POST);
    while (list($key, $value) = each($_POST)) {
      if (!is_array($_POST[$key])) {
        echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
      }
    }
    echo tep_draw_button(IMAGE_SEND_EMAIL, 'mail-closed', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link(FILENAME_GV_MAIL));
?>
                </td>
              </tr>
            </table></td>
          </form></tr>
<?php
  } else {
?>
          <tr><?php echo tep_draw_form('mail', FILENAME_GV_MAIL, 'action=preview'); ?>
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php
    $customers = array();
    $customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
    $customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
    $customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
    $mail_query = tep_db_query("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
    while($customers_values = tep_db_fetch_array($mail_query)) {
      $customers[] = array('id' => $customers_values['customers_email_address'],
                           'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
    }
?>
              <tr>
                <td class="main"><?php echo TEXT_CUSTOMER; ?></td>
                <td><?php echo tep_draw_pull_down_menu('customers_email_address', $customers, (isset($_GET['customer']) ? $_GET['customer'] : ''));?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_FROM; ?></td>
                <td><?php echo tep_draw_input_field('from', EMAIL_FROM); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_SUBJECT; ?></td>
                <td><?php echo tep_draw_input_field('subject'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_AMOUNT; ?></td>
                <td><?php echo tep_draw_input_field('amount'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo TEXT_MESSAGE; ?></td>
                <td><?php echo tep_draw_textarea_field('message', 'soft', '60', '15'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText" colspan="2" align="right"><?php echo tep_draw_button(IMAGE_PREVIEW, 'document', null, 'primary'); ?></td>
              </tr>
            </table></td>
          </form></tr>
<?php
  }
?>
        </table></td>
      </tr>
    </table>
<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
