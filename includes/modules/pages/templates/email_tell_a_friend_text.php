<?php
  echo $email_body;
  if (tep_not_null($message)) {
    echo $message . "\n\n";
  }
  echo sprintf(TEXT_EMAIL_LINK, tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id'], 'NONSSL', false)) . "\n\n" .
       sprintf(TEXT_EMAIL_SIGNATURE, STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");
?>
