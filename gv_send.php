<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require('includes/classes/http_client.php');

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_GV_SEND);

  if (($_POST['back_x']) || ($_POST['back_y'])) {
    $_GET['action'] = '';
  }
  if ($_GET['action'] == 'send') {
    $error = false;
    if (!tep_validate_email(trim($_POST['email']))) {
      $error = true;
      $error_email = ERROR_ENTRY_EMAIL_ADDRESS_CHECK;
    }
    $gv_query = tep_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . $customer_id . "'");
    $gv_result = tep_db_fetch_array($gv_query);
    $customer_amount = $gv_result['amount'];
    $gv_amount = trim($_POST['amount']);
    if (preg_match('/^0-9/', $gv_amount)) {
      $error = true;
      $error_amount = ERROR_ENTRY_AMOUNT_CHECK;
    }
    if ($gv_amount>$customer_amount || $gv_amount == 0) {
      $error = true;
      $error_amount = ERROR_ENTRY_AMOUNT_CHECK;
    }
  }
  if ($_GET['action'] == 'process') {
    $id1 = create_coupon_code($mail['customers_email_address']);
    $gv_query = tep_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id='".$customer_id."'");
    $gv_result = tep_db_fetch_array($gv_query);
    $new_amount = $gv_result['amount'] - $_POST['amount'];
    if ($new_amount < 0) {
      $error= true;
      $error_amount = ERROR_ENTRY_AMOUNT_CHECK;
      $_GET['action'] = 'send';
    } else {
      $gv_query=tep_db_query("update " . TABLE_COUPON_GV_CUSTOMER . " set amount = '" . $new_amount . "' where customer_id = '" . $customer_id . "'");
      $gv_query=tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
      $gv_customer=tep_db_fetch_array($gv_query);
      $gv_query=tep_db_query("insert into " . TABLE_COUPONS . " (coupon_type, coupon_code, date_created, coupon_amount) values ('G', '" . $id1 . "', NOW(), '" . $_POST['amount'] . "')");
      $insert_id = tep_db_insert_id();
      $gv_query=tep_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, sent_lastname, emailed_to, date_sent) values ('" . $insert_id . "' ,'" . $customer_id . "', '" . addslashes($gv_customer['customers_firstname']) . "', '" . addslashes($gv_customer['customers_lastname']) . "', '" . $_POST['email'] . "', now())");

      $gv_email = STORE_NAME . "\n" .
              EMAIL_SEPARATOR . "\n" .
              sprintf(EMAIL_GV_TEXT_HEADER, $currencies->format($_POST['amount'])) . "\n" .
              EMAIL_SEPARATOR . "\n" .
              sprintf(EMAIL_GV_FROM, stripslashes($_POST['send_name'])) . "\n";
      if (isset($_POST['message'])) {
        $gv_email .= EMAIL_GV_MESSAGE . "\n";
        if (isset($_POST['to_name'])) {
          $gv_email .= sprintf(EMAIL_GV_SEND_TO, stripslashes($_POST['to_name'])) . "\n\n";
        }
        $gv_email .= stripslashes($_POST['message']) . "\n\n";
      }
      $gv_email .= sprintf(EMAIL_GV_REDEEM, $id1) . "\n\n";
      $gv_email .= EMAIL_GV_LINK . tep_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $id1,'NONSSL',false);;
      $gv_email .= "\n\n";
      $gv_email .= EMAIL_GV_FIXED_FOOTER . "\n\n";
      $gv_email .= EMAIL_GV_SHOP_FOOTER . "\n\n";;
      $gv_email_subject = sprintf(EMAIL_GV_TEXT_SUBJECT, stripslashes($_POST['send_name']));
      tep_mail('', $_POST['email'], $gv_email_subject, nl2br($gv_email), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');
    }
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_GV_SEND));

  require(DIR_WS_INCLUDES . 'template_top.php');
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">
   <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ($_GET['action'] == 'process') {
?>


    <div class="contentText">
      <p class="main"><?php echo TEXT_SUCCESS; ?><br><br><?php echo 'gv '.$id1; ?></p>
    </div>

  <div class="buttonSet">
    <span class="buttonAction"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'triangle-1-e', tep_href_link(FILENAME_DEFAULT)); ?></span>
  </div>

<?php
  }

  if ($_GET['action'] == 'send' && !$error) {
    // validate entries
      $gv_amount = (double) $gv_amount;
      $gv_query = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
      $gv_result = tep_db_fetch_array($gv_query);
      $send_name = $gv_result['customers_firstname'] . ' ' . $gv_result['customers_lastname'];
?>

<tr>
        <td><form action="<?php echo tep_href_link(FILENAME_GV_SEND, 'action=process', 'NONSSL'); ?>" method="post"><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo sprintf(MAIN_MESSAGE, $currencies->format($_POST['amount']), stripslashes($_POST['to_name']), $_POST['email'], stripslashes($_POST['to_name']), $currencies->format($_POST['amount']), $send_name); ?></td>
          </tr>
<?php
      if ($_POST['message']) {
?>
           <tr>
            <td class="main"><?php echo sprintf(PERSONAL_MESSAGE, $gv_result['customers_firstname']); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo stripslashes($_POST['message']); ?></td>
          </tr>
<?php
      }

      echo tep_draw_hidden_field('send_name', $send_name) . tep_draw_hidden_field('to_name', stripslashes($_POST['to_name'])) . tep_draw_hidden_field('email', $_POST['email']) . tep_draw_hidden_field('amount', $gv_amount) . tep_draw_hidden_field('message', stripslashes($_POST['message']));
?>
          <tr>
            <td class="main"><?php echo tep_image_submit('button_back.gif', IMAGE_BUTTON_BACK, 'name=back') . '</a>'; ?></td>
            <td align="right"><br><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'triangle-1-e', null, 'primary'); ?></td>
          </tr>
        </table></form></td>
      </tr>
<?php
  } elseif ($_GET['action']=='' || $error) {
?>
      <tr>
        <td class="main"><?php echo HEADING_TEXT; ?></td>
      </tr>
      <tr>
        <td><form action="<?php echo tep_href_link(FILENAME_GV_SEND, 'action=send', 'NONSSL'); ?>" method="post"><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_NAME; ?><br><?php echo tep_draw_input_field('to_name', stripslashes($_POST['to_name']));?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_EMAIL; ?><br><?php echo tep_draw_input_field('email', $_POST['email']); if ($error) echo $error_email; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_AMOUNT; ?><br><?php echo tep_draw_input_field('amount', $_POST['amount'], '', '', false); if ($error) echo $error_amount; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_MESSAGE; ?><br><?php echo tep_draw_textarea_field('message', 'soft', 50, 15, stripslashes($_POST['message'])); ?></td>
          </tr>
        </table>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
<?php
    $back = sizeof($navigation->path)-2;
?>
            <td class="main"><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'triangle-1-w', tep_href_link(FILENAME_ACCOUNT, '', 'SSL')); ?></td>
            <td class="main" align="right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'triangle-1-e', null, 'primary'); ?></td>
          </tr>
        </table></form></td>
      </tr>
<?php
  }
?>
</table>


</div>

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
