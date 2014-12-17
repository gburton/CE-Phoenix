<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
  Developed by Toni Roger.
*/

require(DIR_WS_LANGUAGES . $language . '/modules/newsletters/product_notification.php');

  switch ($action){
	case 'send';
		echo '<script language="javascript"><!--
function mover(move) {
  if (move == \'remove\') {
    for (x=0; x<(document.notifications.products.length); x++) {
      if (document.notifications.products.options[x].selected) {
        with(document.notifications.elements[\'chosen[]\']) {
          options[options.length] = new Option(document.notifications.products.options[x].text,document.notifications.products.options[x].value);
        }
        document.notifications.products.options[x] = null;
        x = -1;
      }
    }
  }
  if (move == \'add\') {
    for (x=0; x<(document.notifications.elements[\'chosen[]\'].length); x++) {
      if (document.notifications.elements[\'chosen[]\'].options[x].selected) {
        with(document.notifications.products) {
          options[options.length] = new Option(document.notifications.elements[\'chosen[]\'].options[x].text,document.notifications.elements[\'chosen[]\'].options[x].value);
        }
        document.notifications.elements[\'chosen[]\'].options[x] = null;
        x = -1;
      }
    }
  }
  return true;
}

function selectAll(FormName, SelectBox) {
  temp = "document." + FormName + ".elements[\'" + SelectBox + "\']";
  Source = eval(temp);

  for (x=0; x<(Source.length); x++) {
    Source.options[x].selected = "true";
  }

  if (x<1) {
    alert(\'' . JS_PLEASE_SELECT_PRODUCTS . '\');
    return false;
  } else {
    return true;
  }
}
//--></script>';
	
	$products_array = array();
      $products_query = tep_db_query("select pd.products_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.language_id = '" . $languages_id . "' and pd.products_id = p.products_id and p.products_status = '1' order by pd.products_name");
      while ($products = tep_db_fetch_array($products_query)) {
        $products_array[] = array('id' => $products['products_id'],
                                  'text' => $products['products_name']);
      }
	 $global_button = '<script language="javascript"><!--' . "\n" .
                       'document.write(\'<input type="button" value="' . BUTTON_GLOBAL . '" style="width: 8em;" onclick="document.location=\\\'' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID'] . '&action=confirm&global=true') . '\\\'">\');' . "\n" .
                       '//--></script><noscript><a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID'] . '&action=confirm&global=true') . '">[ ' . BUTTON_GLOBAL . ' ]</a></noscript>';

      $cancel_button = '<script language="javascript"><!--' . "\n" .
                       'document.write(\'<input type="button" value="' . BUTTON_CANCEL . '" style="width: 8em;" onclick="document.location=\\\'' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '\\\'">\');' . "\n" .
                       '//--></script><noscript><a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">[ ' . BUTTON_CANCEL . ' ]</a></noscript>';

      echo '<tr><td><form name="notifications" action="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID'] . '&action=confirm') . '" method="post" onSubmit="return selectAll(\'notifications\', \'chosen[]\')"><table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n" .
                                 '  <tr>' . "\n" .
                                 '    <td align="center" class="main"><b>' . TEXT_PRODUCTS . '</b><br>' . tep_draw_pull_down_menu('products', $products_array, '', 'size="20" style="width: 20em;" multiple') . '</td>' . "\n" .
                                 '    <td align="center" class="main">&nbsp;<br>' . $global_button . '<br><br><br><input type="button" value="' . BUTTON_SELECT . '" style="width: 8em;" onClick="mover(\'remove\');"><br><br><input type="button" value="' . BUTTON_UNSELECT . '" style="width: 8em;" onClick="mover(\'add\');"><br><br><br><input type="submit" value="' . BUTTON_SUBMIT . '" style="width: 8em;"><br><br>' . $cancel_button . '</td>' . "\n" .
                                 '    <td align="center" class="main"><b>' . TEXT_SELECTED_PRODUCTS . '</b><br>' . tep_draw_pull_down_menu('chosen[]', array(), '', 'size="20" style="width: 20em;" multiple') . '</td>' . "\n" .
                                 '  </tr>' . "\n" .
                                 '</table></form></td></tr>';

	break;
	
	case 'confirm':
		if (isset($HTTP_GET_VARS['global']) && ($HTTP_GET_VARS['global'] == 'true')) {
		  $count_query = tep_db_query("SELECT (
			SELECT COUNT( * ) AS count
			FROM " . TABLE_CUSTOMERS . ", " . TABLE_CUSTOMERS_INFO . "
			WHERE mmstatus =  '0'
			AND global_product_notifications =  '1'
			AND customers_id = customers_info_id
			) + ( 
			SELECT COUNT( DISTINCT p.customers_id ) AS count
			FROM " . TABLE_PRODUCTS_NOTIFICATIONS . " p, " . TABLE_CUSTOMERS . " c
			WHERE p.customers_id = c.customers_id
			AND c.mmstatus =  '0' ) AS count");
		  $count = tep_db_fetch_array($count_query);
		  
		} else {
		  $chosen = $HTTP_POST_VARS['chosen'];
  
		  $ids = implode(',', $chosen);
		  
		  $count_query = tep_db_query("SELECT (
			SELECT COUNT( * ) AS count
			FROM " . TABLE_CUSTOMERS . ", " . TABLE_CUSTOMERS_INFO . "
			WHERE mmstatus =  '0'
			AND global_product_notifications =  '1'
			AND customers_id = customers_info_id
			) + ( 
			SELECT COUNT( distinct p.customers_id ) AS count
			FROM " . TABLE_PRODUCTS_NOTIFICATIONS . " p, " . TABLE_CUSTOMERS . " c
			WHERE c.mmstatus =  '0'
			AND p.customers_id = c.customers_id
			AND p.products_id in (" . $ids . ")) AS count");
		  $count = tep_db_fetch_array($count_query);
		  
		}
	break;
	
	case 'confirm_send';
	    if (isset($HTTP_GET_VARS['global']) && ($HTTP_GET_VARS['global'] == 'true')) {
			
		  //get the target group
		  $queue_query = tep_db_query("SELECT (
			SELECT COUNT( * ) AS count
			FROM " . TABLE_CUSTOMERS . ", " . TABLE_CUSTOMERS_INFO . "
			WHERE mmstatus =  '0'
			AND global_product_notifications =  '1'
			AND customers_id = customers_info_id
			) + ( 
			SELECT COUNT( DISTINCT p.customers_id ) AS count
			FROM " . TABLE_PRODUCTS_NOTIFICATIONS . " p, " . TABLE_CUSTOMERS . " c
			WHERE p.customers_id = c.customers_id
			AND c.mmstatus =  '0' ) AS count");
		  $queue = tep_db_fetch_array($queue_query);
		  
		  //count remaining email addresses in  target group (number to be mailed).
		  $mailed_query = tep_db_query("SELECT (
			SELECT COUNT( * ) AS count
			FROM " . TABLE_CUSTOMERS . ", " . TABLE_CUSTOMERS_INFO . "
			WHERE mmstatus =  '9'
			AND global_product_notifications =  '1'
			AND customers_id = customers_info_id
			) + ( 
			SELECT COUNT( DISTINCT p.customers_id ) AS count
			FROM " . TABLE_PRODUCTS_NOTIFICATIONS . " p, " . TABLE_CUSTOMERS . " c
			WHERE p.customers_id = c.customers_id
			AND c.mmstatus =  '9' ) AS count");
		  $mailed = tep_db_fetch_array($mailed_query);
		  
		  //count how many email addresses have been mailed.
		  $mail_query = tep_db_query("(SELECT customers_firstname, customers_lastname, customers_email_address, mmstatus
			FROM " . TABLE_CUSTOMERS . ", " . TABLE_CUSTOMERS_INFO . "
			WHERE mmstatus =  '0'
			AND global_product_notifications =  '1'
			AND customers_id = customers_info_id
			) UNION ( 
			SELECT customers_firstname, customers_lastname, customers_email_address, mmstatus
			FROM " . TABLE_PRODUCTS_NOTIFICATIONS . " p, " . TABLE_CUSTOMERS . " c
			WHERE p.customers_id = c.customers_id
			AND c.mmstatus =  '0' )");
		  $mail = tep_db_fetch_array($mail_query);
		  
		} else {
			if (isset($HTTP_GET_VARS['chosen'])) {
		  		$chosen = $HTTP_GET_VARS['chosen'];
			}
			else {
				$chosen = "0";
			}
  
		  $ids = str_replace("-",",", $chosen);
		  
		  //get the target group
		  $queue_query = tep_db_query("SELECT (
			SELECT COUNT( * ) AS count
			FROM " . TABLE_CUSTOMERS . ", " . TABLE_CUSTOMERS_INFO . "
			WHERE mmstatus =  '0'
			AND global_product_notifications =  '1'
			AND customers_id = customers_info_id
			) + ( 
			SELECT COUNT( distinct p.customers_id ) AS count
			FROM " . TABLE_PRODUCTS_NOTIFICATIONS . " p, " . TABLE_CUSTOMERS . " c
			WHERE c.mmstatus =  '0'
			AND p.customers_id = c.customers_id
			AND p.products_id in (" . $ids . ")) AS count");
		  $queue = tep_db_fetch_array($queue_query);
		  
		  //count remaining email addresses in  target group (number to be mailed).
		  $mailed_query = tep_db_query("SELECT (
			SELECT COUNT( * ) AS count
			FROM " . TABLE_CUSTOMERS . ", " . TABLE_CUSTOMERS_INFO . "
			WHERE mmstatus =  '9'
			AND global_product_notifications =  '1'
			AND customers_id = customers_info_id
			) + ( 
			SELECT COUNT( distinct p.customers_id ) AS count
			FROM " . TABLE_PRODUCTS_NOTIFICATIONS . " p, " . TABLE_CUSTOMERS . " c
			WHERE c.mmstatus =  '9'
			AND p.customers_id = c.customers_id
			AND p.products_id in (" . $ids . ")) AS count");
		  $mailed = tep_db_fetch_array($mailed_query);
		  
		  //count how many email addresses have been mailed.
		  $mail_query = tep_db_query("(SELECT customers_firstname, customers_lastname, customers_email_address, mmstatus
			FROM " . TABLE_CUSTOMERS . ", " . TABLE_CUSTOMERS_INFO . "
			WHERE mmstatus =  '0'
			AND global_product_notifications =  '1'
			AND customers_id = customers_info_id
			) UNION ( 
			SELECT customers_firstname, customers_lastname, customers_email_address, mmstatus
			FROM " . TABLE_PRODUCTS_NOTIFICATIONS . " p, " . TABLE_CUSTOMERS . " c
			WHERE c.mmstatus =  '0'
			AND p.customers_id = c.customers_id
			AND p.products_id in (" . $ids . "))");
		  $mail = tep_db_fetch_array($mail_query);
		  
		}
 	break;
 	}
?>
