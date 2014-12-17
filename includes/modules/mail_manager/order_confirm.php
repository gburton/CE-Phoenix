<?php
/*
order_confirm.php, 2011
mail manager for oscommerce
Copyright (c) 2011 Niora http://www.css-oscommerce.com
Released under the GNU General Public License
*/
//get status of mail manager create account  email
$mail_manager_status_query = tep_db_query("select status, template, htmlcontent, txtcontent from  " . TABLE_MM_RESPONSEMAIL . "  where mail_id = '1'");
$mail_manager_status = tep_db_fetch_array($mail_manager_status_query);

//default to tep_mail if order_confirm mailpiece inactived in admin
if (isset($mail_manager_status['status']) && ($mail_manager_status['status'] == '1')) { 		

// create the order totals variable
  for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
    $mm_ordertotal .= strip_tags($order_totals[$i]['title']) . ' ' . strip_tags($order_totals[$i]['text']) . "\n".'<br/ >'; 
  }

//retrieve html and txt headers 
$header_query = tep_db_query("select htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '".$mail_manager_status['template']."'");
$header = tep_db_fetch_array($header_query);
 
//build email
$output_content_html = $header['htmlheader'].$mail_manager_status['htmlcontent'].$header['htmlfooter']; 
$output_content_txt = $header['txtheader'].$mail_manager_status['txtcontent'].$header['txtfooter']; 
$output_subject = EMAIL_TEXT_CONFIRM.STORE_NAME;

//define values for placeholder variables
$order_no = EMAIL_TEXT_ORDER_NUMBER . ' ' . $insert_id;
$order_date = EMAIL_TEXT_DATE_ORDERED . ' ' . strftime(DATE_FORMAT_LONG);
$invoice_url = EMAIL_TEXT_INVOICE_URL . ' ' . tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $insert_id, 'SSL', false);
$delivery_address = tep_address_label($customer_id, $sendto, 0, '', '<br />');
$billing_address = tep_address_label($customer_id, $billto, 0, '', '<br />');
$order_comments = $order->info['comments'];
$paymentmethod = $order->info['payment_method'];
$ccardtype = $order->info['cc_type'];
$payment_class = $payment_class->email_footer;

//define placeholders
$placeholders=array('$storeurl', '$storename','$storeemail','$separator','$orderno','$orderdate','$invoiceurl','$productsorderedhead','$productsordered','$deliveryaddresshead','$deliveryaddress','$billingaddresshead', '$billingaddress', '$paymethodhead', '$paymentmethod', '$ccardtype','$ordercomments','$totaltext','subtotaltext', '$ordertotal');
$values=array(HTTP_SERVER,STORE_NAME,STORE_OWNER_EMAIL_ADDRESS,EMAIL_SEPARATOR, $order_no,$order_date, $invoice_url,EMAIL_TEXT_PRODUCTS,$products_ordered, EMAIL_TEXT_DELIVERY_ADDRESS,$delivery_address, EMAIL_TEXT_BILLING_ADDRESS, $billing_address, EMAIL_TEXT_PAYMENT_METHOD, $paymentmethod, $ccardtype, $order_comments, EMAIL_TEXT_TOTAL, EMAIL_TEXT_SUBTOTAL, $mm_ordertotal);
$output_content_html=str_replace($placeholders, $values, $output_content_html);
$output_content_txt=str_replace($placeholders, $values, $output_content_txt);

//send email      
tep_mm_sendmail($order->customer['firstname'] . ' ' . $order->customer['lastname'], $order->customer['email_address'], STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $output_subject, $output_content_html, strip_tags($output_content_txt));	
//if mail manager status update email 'inactive' process normally via oscommerce
}else{						
  	 tep_mail($order->customer['firstname'] . ' ' . $order->customer['lastname'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
  }
?>