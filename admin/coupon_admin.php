<?php
/*
  $Id: coupon_admin.php,v 0.01 2014/08/10 17:56:34 Melanie Shepherd aka mommaroodles
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
*/
  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  if ($_GET['selected_box']) {
      $_GET['action'] = '';
      $_GET['old_action'] = '';
  }
  if (($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address']) && (!$_POST['back_x'])) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS);
        $mail_sent_to = TEXT_ALL_CUSTOMERS;
        break;
      case '**D':
        $mail_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
        $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
        break;
      default:
        $customers_email_address = tep_db_prepare_input($_POST['customers_email_address']);
        $mail_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($customers_email_address) . "'");
        $mail_sent_to = $_POST['customers_email_address'];
        break;
    }
	//get the coupon code to include in the email for customer
    $coupon_query = tep_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
    $coupon_result = tep_db_fetch_array($coupon_query);
    $from = tep_db_prepare_input($_POST['from']);
    $subject = tep_db_prepare_input($_POST['subject']);
	 while ($mail = tep_db_fetch_array($mail_query)) {
      $message = tep_db_prepare_input($_POST['message']);
	    $message .= 'Dear ' . $mail['customers_firstname'] . "\n\n";
      $message .= TEXT_TO_REDEEM . "\n\n";
      $message .= TEXT_VOUCHER_IS . $coupon_result['coupon_code'] . "\n\n";
      $message .= TEXT_REMEMBER . "\n\n";
      $message .= TEXT_VISIT . "\n\n";
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
    }
    tep_redirect(tep_href_link(FILENAME_COUPON_ADMIN, 'mail_sent_to=' . urlencode($mail_sent_to)));
  }
  if ( ($_GET['action'] == 'preview_email') && (!$_POST['customers_email_address']) ) {
    $_GET['action'] = 'email';
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
  }
  if ($_GET['mail_sent_to']) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'notice');
  }
 switch ($_GET['action']) {
      case 'setstatus':
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if (isset($_GET['cid'])) {
            tep_set_coupon_status($_GET['cid'], $_GET['flag']);
          }
        }
        tep_redirect(tep_href_link(FILENAME_COUPON_ADMIN, '&cid=' . $_GET['cid']));
        break;
    case 'confirmdelete':
     $coupon_id = tep_db_prepare_input($_GET['cid']);
	 tep_db_query("delete from " . TABLE_COUPONS . " where coupon_id='".$_GET['cid']."'");
     tep_db_query("delete from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id='".$_GET['cid']."'");
     break;
    case 'update':
      // get all $_POST and validate
      $_POST['coupon_code'] = trim($_POST['coupon_code']);
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $_POST['coupon_name'][$language_id] = trim($_POST['coupon_name'][$language_id]);
          $_POST['coupon_desc'][$language_id] = trim($_POST['coupon_desc'][$language_id]);
        }
      $_POST['coupon_amount'] = trim($_POST['coupon_amount']);
      $update_errors = 0;
      if (!$_POST['coupon_name']) {
        $update_errors = 1;
        $messageStack->add(ERROR_NO_COUPON_NAME, 'error');
      }
      if ((!$_POST['coupon_amount']) && (!$_POST['coupon_free_ship'])) {
        $update_errors = 1;
        $messageStack->add(ERROR_NO_COUPON_AMOUNT, 'error');
      }
      if (!$_POST['coupon_code']) {
        $coupon_code = create_coupon_code();
      }
      if ($_POST['coupon_code']) $coupon_code = $_POST['coupon_code'];
      $query1 = tep_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_code = '" . tep_db_prepare_input($coupon_code) . "'");
      if (tep_db_num_rows($query1) && $_POST['coupon_code'] && $_GET['oldaction'] != 'voucheredit')  {
        $update_errors = 1;
        $messageStack->add(ERROR_COUPON_EXISTS, 'error');
      }
      if ($update_errors != 0) {
        $_GET['action'] = 'new';
      } else {
        $_GET['action'] = 'update_preview';
      }
      break;
    case 'update_confirm':
      if ( ($_POST['back_x']) || ($_POST['back_y']) ) {
        $_GET['action'] = 'new';
      } else {
        $coupon_type = "F";  // F =  fixed amount discount code
		if (isset($_GET['cid'])) $coupon_id = tep_db_prepare_input($_GET['cid']);
        if (substr($_POST['coupon_amount'], -1) == '%') $coupon_type='P'; // P = percentage amount discount code
        if ($_POST['coupon_free_ship']) $coupon_type = 'S'; // S = free shipping coupon
        $coupon_start_date = tep_db_prepare_input($_POST['coupon_start_date']);
		$coupon_start_date = (date('Y-m-d') < $coupon_start_date) ? $coupon_start_date : 'now()';
        $sql_data_array = array('coupon_code' => tep_db_prepare_input($_POST['coupon_code']),
                                'coupon_amount' => tep_db_prepare_input($_POST['coupon_amount']),
                                'coupon_type' => tep_db_prepare_input($coupon_type),
                                'uses_per_coupon' => tep_db_prepare_input($_POST['coupon_uses_coupon']),
                                'uses_per_user' => tep_db_prepare_input($_POST['coupon_uses_user']),
                                'coupon_minimum_order' => tep_db_prepare_input($_POST['coupon_min_order']),
                                'restrict_to_products' => tep_db_prepare_input($_POST['coupon_products']),
                                'restrict_to_categories' => tep_db_prepare_input($_POST['coupon_categories']),
                                'coupon_start_date' => $coupon_start_date,
                                'coupon_expire_date' => tep_db_prepare_input($_POST['coupon_expire_date']),
                                'coupon_status' => tep_db_prepare_input($_POST['coupon_status']),
                                'date_created' => 'now()',
                                'date_modified' => 'now()');
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $sql_data_marray[$i] = array('coupon_name' => tep_db_prepare_input($_POST['coupon_name'][$language_id]),
                                       'coupon_description' => tep_db_prepare_input($_POST['coupon_desc'][$language_id])
                                 );
        }
        if ($_GET['oldaction']=='voucheredit') {
          tep_db_perform(TABLE_COUPONS, $sql_data_array, 'update', "coupon_id='" . $_GET['cid']."'");
          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
            $update = tep_db_query("update " . TABLE_COUPONS_DESCRIPTION . " set coupon_name = '" . tep_db_prepare_input($_POST['coupon_name'][$language_id]) . "', coupon_description = '" . tep_db_prepare_input($_POST['coupon_desc'][$language_id]) . "' where coupon_id = '" . $_GET['cid'] . "' and language_id = '" . $language_id . "'");
          }
        } else {
          $query = tep_db_perform(TABLE_COUPONS, $sql_data_array);
          $insert_id = tep_db_insert_id();
          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
            $sql_data_marray[$i]['coupon_id'] = $insert_id;
            $sql_data_marray[$i]['language_id'] = $language_id;
            tep_db_perform(TABLE_COUPONS_DESCRIPTION, $sql_data_marray[$i]);
          }
      }
    }
  }
  require(DIR_WS_INCLUDES . 'template_top.php');
?>
<!-- body_text //-->
<!-- Voucher Report //-->
<?php
  switch ($_GET['action']) {
  case 'voucherreport':
?>
      <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo CUSTOMER_ID; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo CUSTOMER_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo IP_ADDRESS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo REDEEM_DATE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $cc_query_raw = "select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $_GET['cid'] . "'";
    $cc_query = tep_db_query($cc_query_raw);
    while ($cc_list = tep_db_fetch_array($cc_query)) {
      $rows++;
      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
      if (((!$_GET['uid']) || (@$_GET['uid'] == $cc_list['unique_id'])) && (!$cInfo)) {
        $cInfo = new objectInfo($cc_list);
      }
      if ( (is_object($cInfo)) && ($cc_list['unique_id'] == $cInfo->unique_id) ) {
        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_COUPON_ADMIN, tep_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cInfo->coupon_id . '&action=voucherreport&uid=' . $cinfo->unique_id) . '\'">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_COUPON_ADMIN, tep_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cc_list['coupon_id'] . '&action=voucherreport&uid=' . $cc_list['unique_id']) . '\'">' . "\n";
      }
$customer_query = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $cc_list['customer_id'] . "'");
$customer = tep_db_fetch_array($customer_query);
$redeem_ip = tep_get_ip_address();
?>
                <td class="dataTableContent"><?php echo $cc_list['customer_id']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $customer['customers_firstname'] . ' ' . $customer['customers_lastname']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $redeem_ip; ?></td>
                <td class="dataTableContent" align="center"><?php echo tep_date_short($cc_list['redeem_date']); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($cc_list['unique_id'] == $cInfo->unique_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&cid=' . $cc_list['coupon_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
             </table></td>
<?php
      $heading = array();
      $contents = array();
      $coupon_description_query = tep_db_query("select c.coupon_code, c.coupon_amount, cd.coupon_name, cd.coupon_description from " . TABLE_COUPONS . " c, " . TABLE_COUPONS_DESCRIPTION . " cd where c.coupon_id = '" . $_GET['cid'] . "'");
      $coupon_desc = tep_db_fetch_array($coupon_description_query);
      $count_customers = tep_db_query("select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $_GET['cid'] . "' and customer_id = '" . $cInfo->customer_id . "'");
      $heading[] = array('text' => '<b>' . TEXT_REDEMPTIONS . '</b>');
	    $contents[] = array('text' => COUPON_ID . ':  ' . $_GET['cid']);
      $contents[] = array('text' => COUPON_CODE . ': ' . $coupon_desc['coupon_code']);
	    $contents[] = array('text' => COUPON_NAME . ': ' . $coupon_desc['coupon_name']);
	    $contents[] = array('text' => COUPON_DESC . ': ' . $coupon_desc['coupon_description']);
	    $contents[] = array('text' => COUPON_AMOUNT . ': ' . $coupon_desc['coupon_amount']);
      $contents[] = array('text' => TEXT_REDEMPTIONS_TOTAL . ' = ' . tep_db_num_rows($cc_query));
      $contents[] = array('text' => TEXT_REDEMPTIONS_CUSTOMER . ' = ' . tep_db_num_rows($count_customers));
?>
<!-- End Of Voucher Report //-->
<?php
    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
      echo '<td width="25%" valign="top">' . "\n";
      $box = new box;
      echo $box->infoBox($heading, $contents);
      echo '</td>' . "\n";
    }
?>
<?php
    break;
  case 'preview_email':
    $coupon_query = tep_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
    $coupon_result = tep_db_fetch_array($coupon_query);
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
      <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
          <tr><?php echo tep_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=send_email_to_user&cid=' . $_GET['cid']); ?>
            <td><table border="0" width="100%" cellpadding="0" cellspacing="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_CUSTOMER; ?></b><br><?php echo $mail_sent_to; ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo COUPON_CODE; ?></b><br><?php echo $coupon_result['coupon_code']; ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_FROM; ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['from'])); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_SUBJECT; ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_MESSAGE; ?></b><br><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td>
<?php
/* Re-Post all POST'ed variables */
    reset($_POST);
    while (list($key, $value) = each($_POST)) {
      if (!is_array($_POST[$key])) {
        echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
      }
    }
?>
                <table border="0" width="100%" cellpadding="0" cellspacing="2">
                  <tr>
                    <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_COUPON_ADMIN) . '">' . tep_draw_button(IMAGE_CANCEL,'cancel') . '</a> ' . tep_draw_button(IMAGE_SEND_EMAIL,'check', null, 'primary'); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </form></tr>
<?php
    break;
  case 'email':
    $coupon_query = tep_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
    $coupon_result = tep_db_fetch_array($coupon_query);
?>
      <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
          </tr>
          <tr>
            <td class="main">Email an existing discount coupon to a customer</td>
          </tr>
        </table></td>
      </tr>
      <tr><?php echo tep_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=preview_email&cid='. $_GET['cid']); ?>
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
                <td class="main"><?php echo COUPON_CODE; ?>&nbsp;&nbsp;</td>
                <td class="main red"><?php echo $coupon_result['coupon_code']; ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '10', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_CUSTOMER; ?>&nbsp;&nbsp;</td>
                <td><?php echo tep_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']);?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_FROM; ?>&nbsp;&nbsp;</td>
                <td><?php echo tep_draw_input_field('from', EMAIL_FROM); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_SUBJECT; ?>&nbsp;&nbsp;</td>
                <td><?php echo tep_draw_input_field('subject'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo TEXT_MESSAGE; ?>&nbsp;&nbsp;</td>
                <td><?php echo tep_draw_textarea_field('message', 'soft', '60', '15'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><?php echo tep_draw_button('Preview Email'); ?></td>
              </tr>
            </table></td>
          </form></tr>
      </tr>
      </td>
<?php
    break;
  case 'update_preview':
?>
      <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
         </tr>
        </table></td>
      </tr>
      <tr>
      <td>
<?php echo tep_draw_form('coupon', FILENAME_COUPON_ADMIN, 'action=update_confirm&oldaction=' . $_GET['oldaction'] . '&cid=' . $_GET['cid']); ?>
      <table border="0" width="100%" cellspacing="0" cellpadding="6">
      <tr>
        <td align="left" class="main"><?php echo COUPON_NAME; ?></td>
        <td align="left" class="main"><?php echo $_POST['coupon_status'] ? 'Active' : 'Inactive'; ?></td>
      </tr>
<?php
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
?>
      <tr>
        <td align="left" class="main"><?php echo COUPON_NAME; ?></td>
        <td align="left" class="main"><?php echo $_POST['coupon_name'][$language_id]; ?></td>
      </tr>
<?php
}
?>
<?php
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
?>
      <tr>
        <td align="left" class="main"><?php echo COUPON_DESC; ?></td>
        <td align="left" class="main"><?php echo $_POST['coupon_desc'][$language_id]; ?></td>
      </tr>
<?php
}
?>
      <tr>
        <td align="left" class="main"><?php echo COUPON_AMOUNT; ?></td>
        <td align="left" class="main"><?php echo $_POST['coupon_amount']; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_MIN_ORDER; ?></td>
        <td align="left" class="main"><?php echo $_POST['coupon_min_order']; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_FREE_SHIP; ?></td>
<?php
    if ($_POST['coupon_free_ship']) {
?>
        <td align="left" class="main"><?php echo TEXT_FREE_SHIPPING; ?></td>
<?php
    } else {
?>
        <td align="left" class="main"><?php echo TEXT_NO_FREE_SHIPPING; ?></td>
<?php
    }
?>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_CODE; ?></td>
<?php
    if ($_POST['coupon_code']) {
      $c_code = $_POST['coupon_code'];
    } else {
      $c_code = $coupon_code;
    }
?>
        <td align="left" class="main"><?php echo $c_code; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_USES_COUPON; ?></td>
        <td align="left" class="main"><?php echo $_POST['coupon_uses_coupon']; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_USES_USER; ?></td>
        <td align="left" class="main"><?php echo $_POST['coupon_uses_user']; ?></td>
      </tr>
       <tr>
        <td align="left" class="main"><?php echo COUPON_PRODUCTS; ?></td>
        <td align="left" class="main"><?php echo $_POST['coupon_products']; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_CATEGORIES; ?></td>
        <td align="left" class="main"><?php echo $_POST['coupon_categories']; ?></td>
      </tr>
      <tr>
        <td class="main"><?php echo COUPON_STARTDATE; ?></td>
        <td align="left" class="main"><?php echo $_POST['coupon_start_date']; ?></td>
      </tr>
      <tr>
        <td class="main"><?php echo COUPON_FINISHDATE; ?></td>
        <td align="left" class="main"><?php echo $_POST['coupon_expire_date']; ?></td>
      </tr>
<?php
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          echo tep_draw_hidden_field('coupon_name[' . $languages[$i]['id'] . ']', $_POST['coupon_name'][$language_id]);
          echo tep_draw_hidden_field('coupon_desc[' . $languages[$i]['id'] . ']', $_POST['coupon_desc'][$language_id]);
       }
    echo tep_draw_hidden_field('coupon_amount', $_POST['coupon_amount']);
    echo tep_draw_hidden_field('coupon_min_order', $_POST['coupon_min_order']);
    echo tep_draw_hidden_field('coupon_free_ship', $_POST['coupon_free_ship']);
    echo tep_draw_hidden_field('coupon_code', $c_code);
    echo tep_draw_hidden_field('coupon_uses_coupon', $_POST['coupon_uses_coupon']);
    echo tep_draw_hidden_field('coupon_uses_user', $_POST['coupon_uses_user']);
    echo tep_draw_hidden_field('coupon_products', $_POST['coupon_products']);
    echo tep_draw_hidden_field('coupon_categories', $_POST['coupon_categories']);
	  echo tep_draw_hidden_field('coupon_start_date', $_POST['coupon_start_date']);
	  echo tep_draw_hidden_field('coupon_expire_date', $_POST['coupon_expire_date']);
	  echo tep_draw_hidden_field('coupon_status', $_POST['coupon_status']);
?>
     <tr>
        <td align="left"><?php echo tep_draw_button('Confirm Update','help',null,'primary'); ?></td>
        <td align="left"><?php echo tep_draw_button('Back', 'arrowthick-1-w',null,'primary'); ?></td>
      </td>
      </tr>
      </td></table></form>
      </tr>
      </table></td>
<?php
    break;
  case 'voucheredit':
    $languages = tep_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $language_id = $languages[$i]['id'];
      $coupon_query = tep_db_query("select coupon_name, coupon_description from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" .  $_GET['cid'] . "' and language_id = '" . (int)$language_id . "'");
      $coupon = tep_db_fetch_array($coupon_query);
      $coupon_name[$language_id] = $coupon['coupon_name'];
      $coupon_desc[$language_id] = $coupon['coupon_description'];
    }
    $coupon_query = tep_db_query("select coupon_code, coupon_amount, coupon_type, coupon_minimum_order, coupon_start_date, coupon_expire_date, uses_per_coupon, uses_per_user, restrict_to_products, restrict_to_categories, coupon_status from " . TABLE_COUPONS . " where coupon_id = '" . $_GET['cid'] . "'");
    $coupon=tep_db_fetch_array($coupon_query);
    $coupon_amount = $coupon['coupon_amount'];
    if ($coupon['coupon_type']=='P') {
      $coupon_amount .= '%';
    }
    if ($coupon['coupon_type']=='S') {
      $coupon_free_ship .= true;
    }
    $coupon_min_order = $coupon['coupon_minimum_order'];
    $coupon_code = $coupon['coupon_code'];
    $coupon_uses_coupon = $coupon['uses_per_coupon'];
    $coupon_uses_user = $coupon['uses_per_user'];
    $coupon_products = $coupon['restrict_to_products'];
    $coupon_categories = $coupon['restrict_to_categories'];
	$coupon_start_date = $coupon['coupon_start_date'];
	$coupon_expire_date = $coupon['coupon_expire_date'];
	$coupon_status = $coupon['coupon_status'];
  case 'new':
// set some defaults
    if (!$coupon_uses_user) $coupon_uses_user = 1;
// sets status of coupon
 switch ($cInfo->coupon_status) {
   case '0': $active = false; $inactive = true;
   break;
   case '1':
   default: $active = true; $inactive = false;
 }
?>
      <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
      <td>
<?php
    echo tep_draw_form('coupon', FILENAME_COUPON_ADMIN, 'action=update&oldaction='.$_GET['action'] . '&cid=' . $_GET['cid']);
?>
      <table border="0" width="100%" cellspacing="0" cellpadding="6">
         <tr>
            <td class="main"><?php echo TEXT_COUPON_STATUS; ?></td>
            <td class="main"><?php echo tep_draw_radio_field('coupon_status', '1', $active) . '&nbsp;' . TEXT_COUPON_IS_ACTIVE . '&nbsp;' . tep_draw_radio_field('coupon_status', '0', $inactive) . '&nbsp;' . TEXT_COUPON_NOT_ACTIVE; ?></td>
            <td align="left" class="main" width="40%"><?php echo COUPON_STATUS_HELP; ?></td>
         </tr>
<?php
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
?>
      <tr>
        <td align="left" class="main"><?php if ($i==0) echo COUPON_NAME; ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_name[' . $languages[$i]['id'] . ']', $coupon_name[$language_id]) . '&nbsp;' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?></td>
        <td align="left" class="main" width="40%"><?php if ($i==0) echo COUPON_NAME_HELP; ?></td>
      </tr>
<?php
}
?>
<?php
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
?>
      <tr>
        <td align="left" valign="top" class="main"><?php if ($i==0) echo COUPON_DESC; ?></td>
        <td align="left" valign="top"><?php echo tep_draw_textarea_field('coupon_desc[' . $languages[$i]['id'] . ']','physical','24','3', $coupon_desc[$language_id]) . '&nbsp;' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?></td>
        <td align="left" valign="top" class="main"><?php if ($i==0) echo COUPON_DESC_HELP; ?></td>
      </tr>
<?php
}
?>
      <tr>
        <td align="left" class="main"><?php echo COUPON_AMOUNT; ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_amount', $coupon_amount); ?></td>
        <td align="left" class="main"><?php echo COUPON_AMOUNT_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_MIN_ORDER; ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_min_order', $coupon_min_order); ?></td>
        <td align="left" class="main"><?php echo COUPON_MIN_ORDER_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_FREE_SHIP; ?></td>
        <td align="left"><?php echo tep_draw_checkbox_field('coupon_free_ship', $coupon_free_ship); ?></td>
        <td align="left" class="main"><?php echo COUPON_FREE_SHIP_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_CODE; ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_code', $coupon_code); ?></td>
        <td align="left" class="main"><?php echo COUPON_CODE_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_USES_COUPON; ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_uses_coupon', $coupon_uses_coupon); ?></td>
        <td align="left" class="main"><?php echo COUPON_USES_COUPON_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_USES_USER; ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_uses_user', $coupon_uses_user); ?></td>
        <td align="left" class="main"><?php echo COUPON_USES_USER_HELP; ?></td>
      </tr>
       <tr>
        <td align="left" class="main"><?php echo COUPON_PRODUCTS; ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_products', $coupon_products); ?> <a href="validproducts.php" target="_blank" onclick="window.open('validproducts.php', 'Valid_Products', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600'); return false">Get Product's ID's</a></td>
        <td align="left" class="main"><?php echo COUPON_PRODUCTS_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo COUPON_CATEGORIES; ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_categories', $coupon_categories); ?> <a href="validcategories.php" target="_blank" onclick="window.open('validcategories.php', 'Valid_Categories', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600'); return false">Get Category's ID's </a></td>
        <td align="left" class="main"><?php echo COUPON_CATEGORIES_HELP; ?></td>
      </tr>
      <tr>
        <td class="main"><?php echo COUPON_STARTDATE; ?></td>
        <td class="main"><?php echo tep_draw_input_field('coupon_start_date',  $coupon_start_date, 'id="coupon_start_date"'); ?></td>
        <td align="left" class="main"><?php echo COUPON_STARTDATE_HELP; ?></td>
      </tr>
      <tr>
        <td class="main"><?php echo COUPON_FINISHDATE; ?></td>
        <td class="main"><?php echo tep_draw_input_field('coupon_expire_date', $coupon_expire_date, 'id="coupon_expire_date"'); ?></td>
         <td align="left" class="main"><?php echo COUPON_FINISHDATE_HELP; ?></td>
      </tr>
      <tr>
        <td align="left"><?php echo tep_draw_button(COUPON_BUTTON_PREVIEW,'disk', null, 'primary'); ?></td>
        <td align="left"><?php echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_COUPON_ADMIN, ''); ?>"><?php echo tep_draw_button(IMAGE_CANCEL,'cancel'); ?></a>
      </td>
      </tr>
      </td></table>
<script type="text/javascript">
$('#coupon_start_date,#coupon_expire_date').datepicker({
  dateFormat: 'yy-mm-dd',
  changeMonth: true,
  changeYear: true
});
</script>
      </form>
      </tr>
      </table></td>
<?php
    break;
  default:
?>
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
      </tr>
      <tr>
        <td class="main"><br></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo COUPON_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo COUPON_AMOUNT; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo COUPON_CODE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo COUPON_STARTDATE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo COUPON_FINISHDATE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TEXT_COUPON_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  if ($_GET['page'] > 1) $rows = $_GET['page'] * 20 - 20;

      $cc_query_raw = "select coupon_id, coupon_code, coupon_amount, coupon_type, coupon_start_date,coupon_expire_date,uses_per_user,uses_per_coupon,restrict_to_products, restrict_to_categories, coupon_status, date_created, date_modified from " . TABLE_COUPONS ." where coupon_type != 'G'";


    $cc_query = tep_db_query($cc_query_raw);
    while ($cc_list = tep_db_fetch_array($cc_query)) {
    $coupon_start_date = $cc_list['coupon_start_date'];
  	$coupon_expire_date = $cc_list['coupon_expire_date'];

      $rows++;
      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
      if (((!$_GET['cid']) || (@$_GET['cid'] == $cc_list['coupon_id'])) && (!$cInfo)) {
        $cInfo = new objectInfo($cc_list);
      }
      if ( (is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id) ) {
        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_COUPON_ADMIN, tep_get_all_get_params(array('cid', 'action')) . 'cid=' . $cInfo->coupon_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_COUPON_ADMIN, tep_get_all_get_params(array('cid', 'action')) . 'cid=' . $cc_list['coupon_id']) . '\'">' . "\n";
      }
      $coupon_description_query = tep_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cc_list['coupon_id'] . "' and language_id = '" . $languages_id . "'");
      $coupon_desc = tep_db_fetch_array($coupon_description_query);
?>
                <td class="dataTableContent"><?php echo $coupon_desc['coupon_name']; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($cc_list['coupon_type'] == 'P') {
        echo $cc_list['coupon_amount'] . '%';
      } elseif ($cc_list['coupon_type'] == 'S') {
        echo TEXT_FREE_SHIPPING;
      } else {
        echo $currencies->format($cc_list['coupon_amount']);
      }
?>
               </td>
                <td class="dataTableContent" align="center"><?php echo $cc_list['coupon_code']; ?></td>
                <td class="dataTableContent" align="center"><?php echo date("d/m/Y", strtotime($coupon_start_date)); ?></td>
                <td class="dataTableContent" align="center"><?php echo date("d/m/Y", strtotime($coupon_expire_date)); ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($cc_list['coupon_status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_COUPON_ADMIN, 'action=setstatus&flag=0&cid=' . $cc_list['coupon_id'] . '&page=' . $_GET['page']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_COUPON_ADMIN, 'action=setstatus&flag=1&cid=' . $cc_list['coupon_id'] . '&page=' . $_GET['page']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&cid=' . $cc_list['coupon_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
          <tr>
            <td colspan="5" align="right" class="smallText"><br><br><?php echo '<a href="' . tep_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&cID=' . $cInfo->coupon_id . '&action=new') . '">' . tep_draw_button(IMAGE_INSERT,'plus') . '</a>'; ?></td>
          </tr>
        </table></td>
<?php
    $heading = array();
    $contents = array();
    switch ($_GET['action']) {
    case 'release':
      break;
    case 'voucherreport':
      break;
    case 'new':
      break;
    default:
      $amount = $cInfo->coupon_amount;
      if ($cInfo->coupon_type == 'P') {
        $amount .= '%';
      } else {
        $amount = $currencies->format($amount);
      }
      if ($_GET['action'] == 'voucherdelete') {
      	$heading[] = array('text' => '<strong>' . COUPON_DELETE . '</strong>');
        $contents[] = array('text'=> TEXT_CONFIRM_DELETE . '<br><br>' .
                '<a href="'.tep_href_link(FILENAME_COUPON_ADMIN,'action=confirmdelete&cid='.$_GET['cid'],'NONSSL').'">'.tep_draw_button('Confirm Delete Voucher','notice').'</a>' .
                '<a href="'.tep_href_link(FILENAME_COUPON_ADMIN,'cid='.$cInfo->coupon_id,'NONSSL').'">'.tep_draw_button('Cancel','cancel').'</a>'
                );
      } else {
        $prod_details = NONE;
        if ($cInfo->restrict_to_products) {
          $prod_details = '<a href="listproducts.php?cid=' . $cInfo->coupon_id . '" target="_blank" onclick="window.open(\'listproducts.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false">View</a>';
        }
        $cat_details = NONE;
        if ($cInfo->restrict_to_categories) {
          $cat_details = '<a href="listcategories.php?cid=' . $cInfo->coupon_id . '" target="_blank" onclick="window.open(\'listcategories.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false">View</a>';
        }
		$coupon_name_query = tep_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cInfo->coupon_id . "' and language_id = '" . (int)$languages_id . "'");
        $coupon_name = tep_db_fetch_array($coupon_name_query);
		$heading[] = array('text' => '<strong>' . COUPON_INFO . '</strong>');
        $contents[] = array('text'=>COUPON_ID . ':&nbsp;' . $cInfo->coupon_id);
		$contents[] = array('text'=>COUPON_CODE . ':&nbsp;' . $cInfo->coupon_code . '<br>');
        $contents[] = array('text'=>COUPON_NAME . ':&nbsp;' . $coupon_name['coupon_name'] . '<br>');
        $contents[] = array('text'=>COUPON_AMOUNT . ':&nbsp; ' . $amount . '<br>');
        $contents[] = array('text'=>COUPON_STARTDATE . ':&nbsp; ' . tep_date_short($coupon_start_date) . '<br>');
        $contents[] = array('text'=>COUPON_FINISHDATE . ':&nbsp; ' . tep_date_short($coupon_expire_date) . '<br>');
        $contents[] = array('text'=>COUPON_USES_COUPON . ':&nbsp; ' . $cInfo->uses_per_coupon . '<br>');
        $contents[] = array('text'=>COUPON_USES_USER . ':&nbsp; ' . $cInfo->uses_per_user . '<br>');
        $contents[] = array('text'=>COUPON_PRODUCTS . ':&nbsp;' . $prod_details . '<br>');
        $contents[] = array('text'=>COUPON_CATEGORIES . ':&nbsp;' . $cat_details . '<br>');
        $contents[] = array('text'=>DATE_CREATED . ':&nbsp;' . tep_date_short($cInfo->date_created) . '<br>');
        $contents[] = array('text'=>DATE_MODIFIED . ':&nbsp;' . tep_date_short($cInfo->date_modified) . '<br><br>');
        $contents[] = array('text'=>
        '<a href="'.tep_href_link(FILENAME_COUPON_ADMIN,'action=voucheredit&cid='.$cInfo->coupon_id,'NONSSL').'">'.tep_draw_button('Edit','pencil').'</a>&nbsp;&nbsp;' .
        '<a href="'.tep_href_link(FILENAME_COUPON_ADMIN,'action=voucherdelete&cid='.$cInfo->coupon_id,'NONSSL').'">'.tep_draw_button('Delete','trash').'</a><br><br>' .
        '<a href="'.tep_href_link(FILENAME_COUPON_ADMIN,'action=email&cid='.$cInfo->coupon_id,'NONSSL').'">'.tep_draw_button('Email Coupon','mail-closed').'</a>&nbsp;&nbsp;' .
        '<a href="'.tep_href_link(FILENAME_COUPON_ADMIN,'action=voucherreport&cid='.$cInfo->coupon_id,'NONSSL').'">'.tep_draw_button('Redemption Report','document').'</a>');
        }
        break;
      }
?>
<?php
    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
      echo '<td width="25%" valign="top">' . "\n";
      $box = new box;
      echo $box->infoBox($heading, $contents);
      echo '</td>' . "\n";
    }
   }
?>
      </tr>
    </table>
<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>