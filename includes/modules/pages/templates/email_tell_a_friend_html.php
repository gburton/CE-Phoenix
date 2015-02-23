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
                            <td><img src="<?php echo ((defined(ENABLE_SSL_CATALOG)) ? ( ENABLE_SSL_CATALOG == 'true' ? HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG : HTTP_CATALOG_SERVER . DIR_WS_CATALOG ) : (ENABLE_SSL == 'true' ? HTTPS_SERVER . DIR_WS_HTTPS_CATALOG : HTTP_SERVER . DIR_WS_HTTPS_CATALOG)) . DIR_WS_IMAGES . STORE_LOGO; ?>" title="<?php echo STORE_NAME; ?>" alt="<?php echo STORE_NAME; ?>"></td>
                          </tr>
                        </table>

                        <table id="content" width="100%">
                          <tr><td><?php echo tep_convert_linefeeds(array("\r\n", "\n", "\r"), "<br />", $email_body); ?></td></tr>
                          <?php if (tep_not_null($message)) { ?> 
                          <tr><td><blockquote style="background: #f9f9f9; border-left: 10px solid #ccc; margin: 1.5em 10px; padding: 0.5em 10px;"><?php echo tep_convert_linefeeds(array("\r\n", "\n", "\r"), "<br />", $message); ?></blockquote></td></tr>
                          <?php } ?>
                          <tr><td><?php echo tep_convert_linefeeds(array("\r\n", "\n", "\r"), "<br />", sprintf(TEXT_EMAIL_LINK, '<a href="' . tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id'], 'NONSSL', false) . '">' . EMAIL_LINK . '</a>')) . '<br />' .
                          tep_convert_linefeeds(array("\r\n", "\n", "\r"), "<br />", sprintf(TEXT_EMAIL_SIGNATURE, STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n")); ?></td></tr>
                        </table>
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
