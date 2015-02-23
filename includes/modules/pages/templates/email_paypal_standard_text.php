<?php
  echo STORE_NAME . "\n" .
       EMAIL_SEPARATOR . "\n" .
       EMAIL_TEXT_ORDER_NUMBER . ' ' . $order_id . "\n" .
       EMAIL_TEXT_INVOICE_URL . ' ' . tep_href_link('account_history_info.php', 'order_id=' . $order_id, 'SSL', false) . "\n" .
       EMAIL_TEXT_DATE_ORDERED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";

  if ($order->info['comments']) {
    echo tep_db_output($order->info['comments']) . "\n\n";
  }
  echo EMAIL_TEXT_PRODUCTS . "\n" .
       EMAIL_SEPARATOR . "\n" .
       $products_ordered .
       EMAIL_SEPARATOR . "\n";

  for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
    echo strip_tags($order_totals[$i]['title']) . ' ' . strip_tags($order_totals[$i]['text']) . "\n";
  }

  if ($order->content_type != 'virtual') {
    echo "\n" . EMAIL_TEXT_DELIVERY_ADDRESS . "\n" .
                EMAIL_SEPARATOR . "\n" .
                tep_address_label($customer_id, $sendto, 0, '', "\n") . "\n";
  }

  echo "\n" . EMAIL_TEXT_BILLING_ADDRESS . "\n" .
              EMAIL_SEPARATOR . "\n" .
              tep_address_label($customer_id, $billto, 0, '', "\n") . "\n\n";

  if (is_object($$payment)) {
    echo EMAIL_TEXT_PAYMENT_METHOD . "\n" .
         EMAIL_SEPARATOR . "\n";
    echo $payment_class->title . "\n\n";
  }
?>