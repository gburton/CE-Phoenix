<?php
/*
password_forgotten.php, 2011
mail manager for oscommerce
Copyright (c) 2011 Niora http://www.css-oscommerce.com
Released under the GNU General Public License
*/

//get status of mail manager status update  email
$mail_manager_status_query = tep_db_query("select status, template, htmlcontent, txtcontent from  " . TABLE_MM_RESPONSEMAIL . "  where mail_id = '3'");
$mail_manager_status = tep_db_fetch_array($mail_manager_status_query);

if (isset($mail_manager_status['status']) && ($mail_manager_status['status'] == '1')) { 		

//retrieve html and txt headers 
$header_query = tep_db_query("select htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '".$mail_manager_status['template']."'");
$header = tep_db_fetch_array($header_query);

//build email
$output_content_html = $header['htmlheader'].$mail_manager_status['htmlcontent'].$header['htmlfooter']; 
$output_content_txt = $header['txtheader'].$mail_manager_status['txtcontent'].$header['txtfooter']; 

// define subject
$output_subject = EMAIL_PASSWORD_RESET_SUBJECT;

//define values for placeholder variables
$firstname = $check_customer['customers_firstname'];
$lastname = $check_customer['customers_lastname'];
$newpwandmsg = sprintf(EMAIL_PASSWORD_RESET_BODY, $reset_key_url);
   	
//define placeholders. 
$placeholders=array('$storeurl', '$storename','$storeemail','$customerfirstname','$customerlastname','$customeremail','$emailsubject','$newpwandmsg');
$values=array(HTTP_CATALOG_SERVER,STORE_NAME,STORE_OWNER_EMAIL_ADDRESS, $firstname,$lastname, $email_address, EMAIL_PASSWORD_RESET_SUBJECT, $newpwandmsg);
$output_content_html=str_replace($placeholders, $values, $output_content_html);
$output_content_txt=str_replace($placeholders, $values, $output_content_txt);

//send email      
tep_mm_sendmail($check_customer['customers_firstname']. ' ' . $check_customer['customers_lastname'], $email_address, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $output_subject, $output_content_html, $output_content_txt);	
 
//if mail manager status update email 'inactive', or value=0, process normally via oscommerce
}else{						
  tep_mail($check_customer['customers_firstname'] . ' ' . $check_customer['customers_lastname'], $email_address, EMAIL_PASSWORD_RESET_SUBJECT, sprintf(EMAIL_PASSWORD_RESET_BODY, $reset_key_url), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
			  }
?>