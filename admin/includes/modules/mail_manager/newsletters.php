<?php
/*
  $Id: newsletter.php 1739 2007-12-20 00:52:16Z hpdl $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2002 osCommerce
  Released under the GNU General Public License
*/

 switch ($action){
	case 'send';
 		//count the target group
 		$count_query = tep_db_query("select count(*) as count from " . TABLE_CUSTOMERS . " where customers_newsletter = '1' and mmstatus = '0' ");
 		$count = tep_db_fetch_array($count_query);
	break;
	
	case 'confirm_send';
		//count the target group (number to be mailed)
		$queue_query = tep_db_query("select count(*) as count from " . TABLE_CUSTOMERS . " where customers_newsletter = '1' and mmstatus = '0' ");		
 		$queue = tep_db_fetch_array($queue_query);
 		
 		//count how many have been mailed
 		$mailed_query = tep_db_query("select count(*) as count from " . TABLE_CUSTOMERS . " where customers_newsletter = '1' and mmstatus = '9' ");
 		$mailed = tep_db_fetch_array($mailed_query);
 		
		//get the selected group
		$mail_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address, customers_newsletter, mmstatus from " . TABLE_CUSTOMERS . " where customers_newsletter = '1' and mmstatus = '0' ");
 		$mail = tep_db_fetch_array($mail_query);

 	break;
 	}
?>