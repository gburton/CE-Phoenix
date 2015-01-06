<?php
/*
status_update.php, 2011
mail manager for oscommerce
Copyright (c) 2011 Niora http://www.css-oscommerce.com
Released under the GNU General Public License
*/

//get status of mail manager status update  email
$mail_manager_status_query = tep_db_query("select status, template, htmlcontent, txtcontent from  " . TABLE_MM_RESPONSEMAIL . "  where mail_id = '2'");
$mail_manager_status = tep_db_fetch_array($mail_manager_status_query);

if (isset($mail_manager_status['status']) && ($mail_manager_status['status'] == '1')) { 		

//retrieve html and txt headers 
$header_query = tep_db_query("select htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '".$mail_manager_status['template']."'");
$header = tep_db_fetch_array($header_query);
 
//build email
$output_content_html = $header['htmlheader'].$mail_manager_status['htmlcontent'].$header['htmlfooter']; 
$output_content_txt = $header['txtheader'].$mail_manager_status['txtcontent'].$header['txtfooter']; 
$output_subject = EMAIL_TEXT_SUBJECT;

//define values for placeholder variables
$order_no = EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID;
$order_date = EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']);
$status_newhtml = sprintf(EMAIL_HTML_STATUS_UPDATE, $orders_status_array[$status]);
$status_newtxt = sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
$comments = $notify_comments;
$invoice_url = EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL');
    		
//define placeholders
$placeholders=array('$storeurl', '$storename','$storeemail','$customername','$customeremail','$emailsubject', '$orderno', '$orderdate', '$statusnewhtml', '$statusnewtxt', '$comments', '$invoiceurl', '$separator');
$values=array(HTTP_CATALOG_SERVER,STORE_NAME,STORE_OWNER_EMAIL_ADDRESS, $name, $email_address, EMAIL_TEXT_SUBJECT, $order_no , $order_date, $status_newhtml , $status_newtxt, $comments, $invoice_url, EMAIL_SEPARATOR);
$output_content_html=str_replace($placeholders, $values, $output_content_html);
$output_content_txt=str_replace($placeholders, $values, $output_content_txt);

//send email      
tep_mm_sendmail($check_status['customers_name'], $check_status['customers_email_address'], STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $output_subject, $output_content_html, $output_content_txt);	

//if mail manager status update email 'inactive', or value=0, process normally via oscommerce
}else{						
  tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
			  }
?>