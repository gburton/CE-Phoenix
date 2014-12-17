<?php
/*
create_account.php, 2011
mail manager for oscommerce
Copyright (c) 2011 Niora http://www.css-oscommerce.com
Released under the GNU General Public License
*/
//refigure gender
if (ACCOUNT_GENDER == 'true') {
         if ($gender == 'm') {
           $mmgreet = sprintf(EMAIL_GREET_MR, $lastname);
         } else {
           $mmgreet = sprintf(EMAIL_GREET_MS, $lastname);
         }
      } else {
        $mmgreet = sprintf(EMAIL_GREET_NONE, $firstname);
      }


//get status of mail manager create account  email
$mail_manager_status_query = tep_db_query("select status, template, htmlcontent, txtcontent from  " . TABLE_MM_RESPONSEMAIL . "  where mail_id = '0'");
$mail_manager_status = tep_db_fetch_array($mail_manager_status_query);

if (isset($mail_manager_status['status']) && ($mail_manager_status['status'] == '1')) { 		

//retrieve html and txt headers 
$header_query = tep_db_query("select htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '".$mail_manager_status['template']."'");
$header = tep_db_fetch_array($header_query);
 
//build email
$output_content_html = $header['htmlheader'].$mail_manager_status['htmlcontent'].$header['htmlfooter']; 
$output_content_txt = $header['txtheader'].$mail_manager_status['txtcontent'].$header['txtfooter']; 
$output_subject = EMAIL_SUBJECT;

//define values for placeholder variables
$mmwelcome = EMAIL_WELCOME;
$mmtext = EMAIL_TEXT;
$mmcontact = EMAIL_CONTACT;
$mmwarning = EMAIL_WARNING;

//define placeholders
$placeholders=array('$storeurl', '$storename','$storeemail','$customername', '$mmgreet','$mmwelcome','$mmtext','$mmcontact','$mmwarning');
$values=array(HTTP_SERVER,STORE_NAME,STORE_OWNER_EMAIL_ADDRESS,$name,$mmgreet, EMAIL_WELCOME,EMAIL_TEXT,EMAIL_CONTACT,EMAIL_WARNING);
$output_content_html=str_replace($placeholders, $values, $output_content_html);
$output_content_txt=str_replace($placeholders, $values, $output_content_txt);

//send email      
tep_mm_sendmail($name, $email_address, STORE_NAME, STORE_OWNER_EMAIL_ADDRESS, $output_subject, $output_content_html, $output_content_txt);	

//if mail manager status update email is 'inactive' process normally via oscommerce
}else{						
  	  
      tep_mail($name, $email_address, EMAIL_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
       }
     			  
?>