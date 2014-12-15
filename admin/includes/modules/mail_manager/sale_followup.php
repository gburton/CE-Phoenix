<?php
/*
  $Id: sale_followup.php 1739 2007-12-20 00:52:16Z hpdl $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  adapted from xsell, http://addons.oscommerce.com/info/1415 and mailbeez, http://addons.oscommerce.com/info/7425
  Copyright (c) 2002 osCommerce
  Released under the GNU General Public License
*/
/*
Sale Followup is a query that loads into mm_bulkmail.php, and selects customers who have ordered within a 
designated timeframe. Output includes a list of products ordered and if xsell is installed a cooresponding
product recommendation.

CONFIGURATION:
Change the following to customize the sale_followup module:

$wait_until is the php date function that yields the number of days (in the format OSCommerce uses in it's database), to wait before a 
follow up email is sent. If the number is -10 the program ignores any order that was placed within the last ten days.
*/
$wait_until = date('Y-m-d h:m:s', strtotime("-10 days"));

/*
$ignore_after is the php date function that yields the number of days (in the format OSCommerce uses in the database), after which no email is sent. 
If the is set to -30 the program ignores any order that was placed more than 30 days ago. 
*/
$ignore_before = date('Y-m-d h:m:s', strtotime("-30 days"));

/*
$status_select is the orders_status of an order in the orders table of the database. If you set this to '1' the program will only
select orders with an order_status_id of 1. This is originally set in admin/orders in the usual way.
Note:the default installation of OSCommerce uses the following order_status values to order_status name coorelation.
1 = pending
2 = processing
3 = delivered
*/
$status_select = '1';

//$status_updateto is the value that the order_status of an order in the orders table of the database is updated to. 
$status_updateto = '3';

//$limit_products is the maximum number of products to display in the email.
$limit_products = '3';

// $limit_xsell_products is the maximum number of xsell products to display in the email.
$limit_xsell_products = '3';

 switch ($action){
	case 'send';
 		//count the target group. mmstatus must be set to '0'
 		
 		$count_query = tep_db_query("select count(*) as count 
					from " . TABLE_CUSTOMERS . " c, " . TABLE_ORDERS . " o
					where o.customers_id = c.customers_id
					and o.orders_status = '".$status_select."'
					and o.date_purchased <= '" . $wait_until . "' 
 					and o.date_purchased > '" . $ignore_before . "'
 					and c.mmstatus = '0' ");
 		$count = tep_db_fetch_array($count_query); 		 
		echo '<tr><td class="main">email orders older than (<=): '.$wait_until;
		echo '<br />but no older than (>): '.$ignore_before.'</td></tr>';		
	break;

	case 'confirm_send';
	    //count  email addresses in  target group (number to be mailed). mmstatus must be set to '0'
	    $queue_query = tep_db_query("select count(*) as count 
					from " . TABLE_CUSTOMERS . " c, " . TABLE_ORDERS . " o
					where o.customers_id = c.customers_id
					and o.orders_status = '".$status_select."' 
					and o.date_purchased <= '" . $wait_until . "' 
 					and o.date_purchased > '" . $ignore_before . "'
 					and c.mmstatus = '0' ");		
 		$queue = tep_db_fetch_array($queue_query);
 		 
 				
 		//count email addresses that have been mailed. mmstatus must be set to '9'
 		$mailed_query = tep_db_query("select count(*) as count 
					from  " . TABLE_CUSTOMERS . " c, ". TABLE_ORDERS . " o
					where o.customers_id = c.customers_id
					and o.orders_status = '".$status_updateto."'
					and o.date_purchased <= '" . $wait_until . "' 
 					and o.date_purchased > '" . $ignore_before . "'
 					and c.mmstatus = '9' ");
 		$mailed = tep_db_fetch_array($mailed_query); 		
 	
//get the target group. mmstatus must be set to '0'
		$mail_query = tep_db_query("select c.customers_firstname, c.customers_lastname, c.mmstatus, o.orders_id, 
					o.customers_id, c.customers_email_address, o.date_purchased, o.date_purchased as status_date
					from " . TABLE_ORDERS . " o, " . TABLE_CUSTOMERS . " c
					where o.customers_id = c.customers_id
					and o.orders_status = '".$status_select."'					
					and o.date_purchased <= '" . $wait_until . "' 
 					and o.date_purchased > '" . $ignore_before . "'
 					and c.mmstatus = '0' ");		
 		$mail = tep_db_fetch_array($mail_query);
// get the products 		 
 		 $query_products_purchased = tep_db_query("select p.products_id, op.products_name, op.products_model, p.products_image
							   from " . TABLE_ORDERS_PRODUCTS		 . " op, " . TABLE_PRODUCTS . " p
							   where op.products_id = p.products_id
                               and p.products_status = '1'                             
                               and op.orders_id = '" . $mail['orders_id'] . "' Limit ". $limit_products."");	
           
                               	     
            //'additional_htmlcontent' and 'additional txtcontent' are attached to the end of 'htmlcontent' and 'txtcontent' in admin/bulkmail_manager.php
				$additional_htmlcontent .= '
				<table align="center" width="100%"><tr><td>'.TEXT_ADDITIONAL_HTMLCONTENT.'</td></tr><tr><td align="center">
				';
				$additional_txtcontent .= "\n\n". TEXT_ADDITIONAL_TXTCONTENT."\n\n";
				while ($products_purchased = tep_db_fetch_array($query_products_purchased)) {			             
                	if (DIR_WS_CATALOG==NULL){
                	$backslash= '/';
                	}
                 
                 //compile html content   
                    $additional_htmlcontent .= '
                	<div style="float:left;width:200px;">
                	<a href="' . HTTP_CATALOG_SERVER.DIR_WS_CATALOG.$backslash.'product_info.php?products_id='.$products_purchased['products_id'] . '">' . tep_image(HTTP_CATALOG_SERVER.DIR_WS_CATALOG.$backslash.DIR_WS_IMAGES .'/'.$products_purchased['products_image'], $products_purchased['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>
                	<p><a href="' . HTTP_CATALOG_SERVER.DIR_WS_CATALOG.$backslash.'product_info.php?products_id='. $products_purchased['products_id']  . '">' . $products_purchased['products_name'] . '</a></p>
                	<p><a href="' . HTTP_CATALOG_SERVER.DIR_WS_CATALOG.$backslash.'product_reviews_write.php?products_id='.$products_purchased['products_id'] . '">Write a Review!</a></p>
                	</div>
                	';						
				  //compile text content
					$additional_txtcontent .= 
					$products_purchased['products_name'].' ' ."\n"
					. HTTP_CATALOG_SERVER.DIR_WS_CATALOG.$backslash.'product_info.php?products_id='. $products_purchased['products_id'] 
                	."\n".'Write a Review!'."\n"
                	. HTTP_CATALOG_SERVER.DIR_WS_CATALOG.$backslash.'product_reviews_write.php?products_id='.$products_purchased['products_id']."\n\n"
					;			
				          
				            	
				  }
				  
				  //get exsell items            		
                   			/*$xsell_query = tep_db_query("select distinct p.products_id, p.products_image, pd.products_name, p.products_tax_class_id, products_price, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, specials_new_products_price
	               			from " . TABLE_PRODUCTS_XSELL . " xp left join " . TABLE_PRODUCTS . " p on xp.xsell_id = p.products_id
	               			left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "'
	               			left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id 
	               			where xp.products_id = '" . $products_purchased['products_id'] . "'
	               			and p.products_status = '1'
	               			order by sort_order asc Limit ". $limit_xsell_products."");*/
							
							$xsell_query = tep_db_query("select distinct p.products_id, p.products_image, pd.products_name, p.products_tax_class_id, products_price, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, specials_new_products_price
							from " . TABLE_PRODUCTS_XSELL . " xp left join " . TABLE_PRODUCTS . " p on xp.xsell_id = p.products_id
							left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "'
							left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id
							where xp.products_id in (select p.products_id
											  from " . TABLE_ORDERS_PRODUCTS   . " op, " . TABLE_PRODUCTS . " p
											  where op.products_id = p.products_id
																					   and p.products_status = '1'                                                  
																					   and op.orders_id = '" . $mail['orders_id'] . "')
							and p.products_status = '1'
							and p.products_id not in (select p.products_id
											  from " . TABLE_ORDERS_PRODUCTS   . " op, " . TABLE_PRODUCTS . " p
											  where op.products_id = p.products_id
																					   and p.products_status = '1'                                                  
																					   and op.orders_id in (
											 select orders_id from " . TABLE_ORDERS . "
											 where customers_id = '" . $mail['customers_id'] . "'))
							order by sort_order asc limit " . $limit_xsell_products."");
				        								
							while ($xsell = tep_db_fetch_array($xsell_query)) {
                 
                 		 		//compile html xsell content   
                    				$xselladditional_htmlcontent .= '
                					<div style="float:left;width:200px;">
                					<a href="' . HTTP_CATALOG_SERVER.DIR_WS_CATALOG.$backslash.'product_info.php?products_id='.$xsell['xsell_id'] . '">' . tep_image(HTTP_CATALOG_SERVER.DIR_WS_CATALOG.$backslash.DIR_WS_IMAGES .'/'.$xsell['products_image'], $xsell['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>
                					<p><a href="' . HTTP_CATALOG_SERVER.DIR_WS_CATALOG.$backslash.'product_info.php?products_id='. $xsell['products_id']  . '">' . $xsell['products_name'] . '</a></p>
                					</div>
                					';						
				  		 		//compile text xsell content
									$xselladditional_txtcontent .= 
									$xsell['products_name'].' ' ."\n"
									. HTTP_CATALOG_SERVER.DIR_WS_CATALOG.$backslash.'product_info.php?products_id='. $xsell['products_id'] 
                					."\n".'Write a Review!'."\n"
                					. HTTP_CATALOG_SERVER.DIR_WS_CATALOG.$backslash.'product_reviews_write.php?products_id='.$xsell['products_id']."\n\n"
									;			
				            	}
				  $additional_htmlcontent .= '</td></tr></table>';
				  $additional_htmlcontent .= '<table align="center" width="100%"><tr><td>'.TEXT_ADDITIONAL_XSELLHTMLCONTENT.'</td></tr><tr><td align="center">'
				  							 .$xselladditional_htmlcontent.'</td></tr></table>';
				  $additional_txtcontent .= "\n\n". TEXT_ADDITIONAL_XSELLTXTCONTENT."\n\n".$xselladditional_txtcontent;

				$order_id = $mail['orders_id'];
				// change status
				tep_db_query("update " . TABLE_ORDERS . " set  orders_status = '".$status_updateto."' where orders_id = '" . $order_id . "'");
			    // update order history
                $comments = EMAIL_TEXT_MMCOMMENTS_UPDATE;            
			    tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . $order_id . "', '" . $status_updateto . "', now(), '2', '" . $comments . "')");
            		
 	break;
 	}
?>