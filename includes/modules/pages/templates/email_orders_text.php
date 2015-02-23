<?php
  echo STORE_NAME . "\n" .
       EMAIL_SEPARATOR . "\n" .
       EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" .
       EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link('account_history_info.php', 'order_id=' . $oID, 'SSL') . "\n" .
       EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" .
       strip_tags($notify_comments) . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
?>
