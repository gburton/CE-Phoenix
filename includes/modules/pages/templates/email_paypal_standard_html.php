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
                        <h1 style="color:#606060 !important; font-size:26px;"><?php echo EMAIL_TEXT_SUBJECT; ?></h1>
                        <table id="content" width="100%">
                          <tr>
                            <td><?php echo EMAIL_TEXT_ORDER_NUMBER . ' ' . $order_id . "<br />" .
                                           EMAIL_TEXT_INVOICE_URL . ' <a href="' . tep_href_link('account_history_info.php', 'order_id=' . $order_id, 'SSL', false) . '">' . EMAIL_TEXT_MY_ORDER . "</a><br />" .
                                           EMAIL_TEXT_DATE_ORDERED . ' ' . strftime(DATE_FORMAT_LONG); ?></td>
                          </tr>
                          <tr>
                            <td>
                            <?php if ($order->info['comments']) { ?>
                              <h2><?php echo EMAIL_COMMENTS; ?></h2>
                              <blockquote style="background: #f9f9f9; border-left: 10px solid #ccc; margin: 1.5em 10px; padding: 0.5em 10px;"><?php echo tep_convert_linefeeds(array("\r\n", "\n", "\r"), "<br />", $order->info['comments']); ?></blockquote>
                            <?php } ?>
                            </td>
                          </tr>
                          <tr>
                            <td colspan="2"><h2><?php echo EMAIL_TEXT_PRODUCTS; ?></h2></td>
                          </tr>
                          <tr>
                            <td>
                              <table align="right" cellspacing="1" cellpadding="2">
                                <thead>
                                  <tr style="color:#FFFFFF; background-color:#737373;"><td><?php echo EMAIL_TABLE_HEADER_QUANTITY; ?></td><td><?php echo EMAIL_TABLE_HEADER_PRODUCTS; ?></td><td class="mobileHide"><?php echo EMAIL_TABLE_HEADER_MODEL; ?></td><td><?php echo EMAIL_TABLE_HEADER_AMOUNT; ?></td></tr>
                                </thead>
                                <tbody>
                                <?php
                                  $products = explode("\n", $products_ordered);
                                  $max = count($products);
                                  $row = 0;

                                  for ($n=0; $n<$max; $n++) {
                                    if (strpos($products[$n], "\t") === false && !empty($products[$n])) {
                                      $row++;
                                      echo '<tr class="' . (($row % 2 == 0) ? 'odd' : 'even') . '">';
                                      $columns = str_replace(array(" x ", " (", ") = "), " == ",  $products[$n]);
                                      $column = explode(" == ", $columns);
                                      echo '<td align="right" style="border-top: 1px dotted #cccccc;">' . $column[0] . '&nbsp;x&nbsp;</td><td style="border-top: 1px dotted #cccccc;">' . $column[1];
                                      if (isset($products[$n+1]) && strpos($products[$n+1], "\t") !== false) {
                                        for ($i=$n+1; $i<$max; $i++) {
                                          if (strpos($products[$i], "\t") === false) {
                                            $n=$i-1;
                                            break;
                                          }
                                          echo '<br />&nbsp;-&nbsp;<small>' . str_replace("\t", "", $products[$i]) . '</small>';
                                        }
                                      }
                                      echo  '</td><td class="mobileHide" style="border-top: 1px dotted #cccccc;">' . $column[2] . '</td><td align="right" style="border-top: 1px dotted #cccccc;">' . $column[3] . '</td></tr>';
                                    }
                                  }
                                ?>
                                </tbody>
                                <tfoot>
                                <?php
                                  for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) { ?>
                                    <tr><td class="mobileHide" <?php echo ($i==0 ? 'style="border-top: 1px solid #cccccc;"' : ''); ?>>&nbsp;</td><td colspan="2" align="right"  <?php echo ($i==0 ? 'style="border-top: 1px solid #cccccc;"' : ''); ?>><?php echo strip_tags($order_totals[$i]['title']); ?></td><td align="right" <?php echo ($i==0 ? 'style="border-top: 1px solid #cccccc;"' : ''); ?>><?php echo strip_tags($order_totals[$i]['text']); ?></td></tr>
                                <?php
                                  }
                                ?>
                                </tfoot>
                              </table>
                            </td>
                          </tr>
                          <tr>
                            <td>
                            <?php
                              if ($order->content_type != 'virtual') {
                            ?>
                                <h2><?php echo EMAIL_TEXT_DELIVERY_ADDRESS; ?></h2>
                                <hr>
                            <?php
                                echo tep_address_label($customer_id, $sendto, 0, '', "<br />");
                              }
                            ?>
                            <h2><?php echo EMAIL_TEXT_BILLING_ADDRESS; ?></h2>
                            <hr>
                            <?php
                                echo tep_address_label($customer_id, $billto, 0, '', "<br />");
                            ?>
                            </td>
                          </tr>
                          <?php
                            if (is_object($$payment)) {
                          ?>
                          <tr>
                            <td>
                            <h2><?php echo EMAIL_TEXT_PAYMENT_METHOD; ?></h2>
                            <hr>
                            <?php echo $payment_class->title . '<br />'; ?>
                            </td>
                          </tr>
                          <?php
                            }
                          ?>
                        </table>
                      </td>
                    </tr>
                  </table>
                  <table id="footer" width="100%">
                    <tr>
                      <td style="padding-top:30px; text-align: center;"><?php echo tep_convert_linefeeds(array("\r\n", "\n", "\r"), "<br />", STORE_ADDRESS); ?><br /><?php echo STORE_PHONE; ?><br /><a href="<?php echo tep_href_link('contact_us.php'); ?>"><?php echo EMAIL_CONTACT_US; ?></a>
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
