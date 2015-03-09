<?php
  // better comments for htmls
  if (isset($_POST['notify_comments']) && ($_POST['notify_comments'] == 'on')) {
    $notify_comments = '<h2>' . tep_convert_linefeeds(array("%s", "\r\n", "\n", "\r"), "", EMAIL_TEXT_COMMENTS_UPDATE) . '</h2><blockquote style="background: #f9f9f9; border-left: 10px solid #ccc; margin: 1.5em 10px; padding: 0.5em 10px;"><i>' . tep_convert_linefeeds(array("\r\n", "\n", "\r"), "<br />", $comments) . '</i></blockquote>';
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <?php include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/html_email_head.php'); ?>
</head>

<body style="background-color:#F2F2F2;">
  <center>
    <table id="bodyTable" cellspacing="0" cellpadding="0" border="0" width="100%" height="100%" style="background-color:#F2F2F2;">
      <tbody>
        <tr>
          <td id="bodyCell" valign="top" align="center" style="padding:40px 20px;">
            <table id="contentContainer" cellspacing="0" cellpadding="0" border="0" style="max-width:600px !important; width:100% !important;">
              <tr>
                <td align="center" valign="top" style="padding-bottom:0px;">
                  <table id="emailBody" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#FFFFFF; border-collapse:separate !important; border-radius:4px;">
                    <tr>
                      <td align="center" valign="top" class="mobilePadding" style=" padding-top:40px; padding-right:40px; padding-bottom:30px; padding-left:40px;">
                        <table id="header" width="100%">
                          <tr>
                            <td><img src="<?php echo (ENABLE_SSL_CATALOG == 'true' ? HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG : HTTP_CATALOG_SERVER . DIR_WS_CATALOG) . DIR_WS_IMAGES . STORE_LOGO; ?>" title="<?php echo STORE_NAME; ?>" alt="<?php echo STORE_NAME; ?>"></td>
                          </tr>
                        </table>
                        <h1 style="color:#606060 !important; font-size:26px;"><?php echo EMAIL_TEXT_SUBJECT; ?></h1>
                        <table id="content" width="100%">
                          <tr><td><?php echo EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID; ?></td></tr>
                          <tr><td><?php echo EMAIL_TEXT_INVOICE_URL . ' <a href="' . tep_catalog_href_link('account_history_info.php', 'order_id=' . $oID, 'SSL') . '">' . EMAIL_TEXT_VIEW_MY_ORDER . '</a>'; ?></td></tr>
                          <tr><td><?php echo EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']); ?></td></tr>
                          <tr><td><?php echo $notify_comments; ?></td></tr>
                          <tr><td><?php echo tep_convert_linefeeds(array("\r\n", "\n", "\r"), '<br />', sprintf(EMAIL_TEXT_STATUS_UPDATE, '<strong>' . $orders_status_array[$status] . '</strong>')); ?></td></tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                  <table id="footer" width="100%">
                    <tr>
                      <td style="padding-top:30px; text-align: center;"><?php echo tep_convert_linefeeds(array("\r\n", "\n", "\r"), "<br />", STORE_ADDRESS); ?><br /><?php echo STORE_PHONE; ?>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </center>
  <p><?php include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/html_email_foot.php'); ?></p>
</body>
</html>
