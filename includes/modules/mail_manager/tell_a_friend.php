<?php
/*
tell_a_friend.php, 2011
mail manager for oscommerce
Copyright (c) 2011 Niora http://www.css-oscommerce.com
Released under the GNU General Public License
*/

//get status of mail manager status update  email
$mail_manager_status_query = tep_db_query("select status, template, htmlcontent, txtcontent from  " . TABLE_MM_RESPONSEMAIL . "  where mail_id = '4'");
$mail_manager_status = tep_db_fetch_array($mail_manager_status_query);

if (isset($mail_manager_status['status']) && ($mail_manager_status['status'] == '1')) { 		

//retrieve html and txt headers 
$header_query = tep_db_query("select htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '".$mail_manager_status['template']."'");
$header = tep_db_fetch_array($header_query);
 
//build email
$output_content_html = $header['htmlheader'].$mail_manager_status['htmlcontent'].$header['htmlfooter']; 
$output_content_txt = $header['txtheader'].$mail_manager_status['txtcontent'].$header['txtfooter']; 

// define subject
$output_subject = $to_name.' ' .TEXT_RECOMMEND.' '. $product_info['products_name'].' '.TEXT_FROM.' '.STORE_NAME;

//See catalog/includes/configure to construct the correct value of $image_urlfix
if (DIR_WS_HTTP_CATALOG==NULL){
	$image_urlfix = HTTP_SERVER;
	}else{
	$image_urlfix = HTTP_SERVER.DIR_WS_HTTP_CATALOG;
	}

//define values for placeholder variables
$product_link = tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $HTTP_GET_VARS['products_id'], 'NONSSL', false);
$product_image = tep_image($image_urlfix.DIR_WS_IMAGES . $product_info['products_image_med'], $product_info['products_name'], '', '', '');
$product_name = $product_info['products_name'];

		
//define placeholders
$placeholders=array('$storeurl', '$storename','$storeemail', '$toname', '$fromname', '$product', '$link', '$image','$message');
$values=array(HTTP_SERVER,STORE_NAME,STORE_OWNER_EMAIL_ADDRESS, $to_name, $from_name, $product_name, $product_link, $product_image, $message);
$output_content_html=str_replace($placeholders, $values, $output_content_html);
$output_content_txt=str_replace($placeholders, $values, $output_content_txt);

//send email      
tep_mm_sendmail($to_name, $to_email_address, $from_name, $from_email_address, $output_subject, $output_content_html, $output_content_txt);	

//if mail manager status update email 'inactive', or value=0, process normally via oscommerce

}else{						
  tep_mail($to_name, $to_email_address, $email_subject, $email_body, $from_name, $from_email_address);
			  }

?>