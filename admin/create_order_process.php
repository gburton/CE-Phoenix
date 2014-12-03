<?php
/*
  $Id: account_edit_process.php,v 1.2 2002/11/28 23:39:44 wilt Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require_once('includes/functions/password_funcs.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_ORDER_PROCESS);
 // if ($_POST['action'] != 'process') {
  //  tep_redirect(tep_href_link(FILENAME_CREATE_ORDER, '', 'SSL'));
  //}
  $customers_id = (empty($_POST['customers_id']) ? 0 : (int)tep_db_prepare_input($_POST['customers_id']));
  $gender = tep_db_prepare_input($_POST['customers_gender']);
  $firstname = tep_db_prepare_input($_POST['customers_firstname']);
  $lastname = tep_db_prepare_input($_POST['customers_lastname']);
  $dob = tep_db_prepare_input($_POST['customers_dob']);
  $email_address = tep_db_prepare_input($_POST['customers_email_address']);
  $telephone = tep_db_prepare_input($_POST['customers_telephone']);
  $fax = tep_db_prepare_input($_POST['customers_fax']);
  $newsletter = tep_db_prepare_input($_POST['newsletter']);
  $confirmation = tep_db_prepare_input($_POST['confirmation']);
  $street_address = tep_db_prepare_input($_POST['entry_street_address']);
  $company = tep_db_prepare_input($_POST['entry_company']);
  $suburb = tep_db_prepare_input($_POST['entry_suburb']);
  $postcode = tep_db_prepare_input($_POST['entry_postcode']);
  $city = tep_db_prepare_input($_POST['entry_city']);
  $zone_id = (empty($_POST['zone_id']) ? 0 : tep_db_prepare_input($_POST['zone_id'])) ;
  $state = tep_db_prepare_input($_POST['entry_state']);
  $country = tep_db_prepare_input( tep_get_country_name($_POST['entry_country']));
  $country_id = (int)tep_db_prepare_input($_POST['entry_country']);
  $customers_newsletter = tep_db_prepare_input($_POST['customers_newsletter']);
  $customers_password = tep_db_prepare_input($_POST['customers_password']);

  $format_id = tep_get_address_format_id($country_id);
  $size = "1";
  $payment_method = DEFAULT_PAYMENT_METHOD;
  $new_value = "1";
  $error = false; // reset error flag
  $temp_amount = "0";
  $temp_amount = number_format($temp_amount, 2, '.', '');
  
  $currency_text = DEFAULT_CURRENCY . ", 1";
  if(isset($_POST['Currency']))
  {
  	$currency_text = tep_db_prepare_input($_POST['Currency']);
  }
  
  $currency_array = explode(",", $currency_text);
  
  $currency = $currency_array[0];
  $currency_value = $currency_array[1];

  $customer_service_id = tep_db_prepare_input($_POST['cust_service']);

  // we are creating a customer account for this one
  if ($_POST['customers_create_type'] == 'new') {
  
    $inuse_query = tep_db_query("select customers_id, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . $email_address . "'");
	if ($inuse = tep_db_fetch_array($inuse_query)) {
	    tep_redirect(tep_href_link(FILENAME_CREATE_ORDER, 'Customer=' . $inuse['customers_id'] . '&cust_select_button=Select&message='. urlencode(TEXT_EMAIL_EXISTS_ERROR), 'SSL'));
	}
  
    // do customers table entry
	$sql_data_array = array('customers_firstname' => $firstname,
							'customers_lastname' => $lastname,
							'customers_email_address' => $email_address,
							'customers_telephone' => $telephone,
							'customers_fax' => $fax,
							'customers_newsletter' => $customers_newsletter);
 
	if (!empty($customers_password)) $sql_data_array['customers_password'] = tep_encrypt_password($customers_password);
	if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
	if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

	tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);

    $customers_id = tep_db_insert_id();
  
	// do customers info entry
	$sql_data_array = array('customers_info_id' => $customers_id,
							'customers_info_number_of_logons' => 0,
							'global_product_notifications' => 0);

	tep_db_perform(TABLE_CUSTOMERS_INFO, $sql_data_array);

	tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_created = now() where customers_info_id = '" . (int)$customers_id . "'");
    
	// do address book entry
	$sql_data_array = array('customers_id' => (int)$customers_id,
							'entry_firstname' => $firstname,
							'entry_lastname' => $lastname,
							'entry_street_address' => $street_address,
							'entry_postcode' => $postcode,
							'entry_city' => $city,
							'entry_country_id' => $country_id);

	if (ACCOUNT_GENDER == 'true') $sql_data_array['entry_gender'] = $gender;
	if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $company;
	if (ACCOUNT_SUBURB == 'true') $sql_data_array['entry_suburb'] = $suburb;

	if (ACCOUNT_STATE == 'true') {
	  $zone_query = tep_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "' and zone_name = '" . $state . "'");
	  if (tep_db_num_rows($zone_query)) {
		$zone = tep_db_fetch_array($zone_query);
		$zone_id = $zone['zone_id'];
	  }

	  if ($zone_id > 0) {
		$sql_data_array['entry_zone_id'] = $zone_id;
		$sql_data_array['entry_state'] = '';
	  } else {
		$sql_data_array['entry_zone_id'] = '0';
		$sql_data_array['entry_state'] = $state;
	  }
	}

	tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
	
	$default_address_id = tep_db_insert_id();
	
	tep_db_query("update " . TABLE_CUSTOMERS . " set customers_default_address_id = '".$default_address_id."' where customers_id = '" . (int)$customers_id . "'");
  
  }


    $sql_data_array = array('customers_id' => $customers_id,
							'customers_name' => $firstname . ' ' . $lastname,
							'customers_company' => $company,

							'customers_street_address' => $street_address,
							'customers_suburb' => $suburb,
							'customers_city' => $city,
							'customers_postcode' => $postcode,
							'customers_state' => $state,
							'customers_country' => $country,

							'customers_telephone' => $telephone,
                            'customers_email_address' => $email_address,
							'customers_address_format_id' => $format_id,
							'delivery_name' => $firstname . ' ' . $lastname,
							'delivery_company' => $company,
                            'delivery_street_address' => $street_address,
							'delivery_suburb' => $suburb,
							'delivery_city' => $city,
							'delivery_postcode' => $postcode,
							'delivery_state' => $state,
							'delivery_country' => $country,
							'delivery_address_format_id' => $format_id,
							'billing_name' => $firstname . ' ' . $lastname,
							'billing_company' => $company,
							'billing_street_address' => $street_address,
							'billing_suburb' => $suburb,
							'billing_city' => $city,
							'billing_postcode' => $postcode,
							'billing_state' => $state,
							'billing_country' => $country,
							'billing_address_format_id' => $format_id,
							'date_purchased' => 'now()', 
							'orders_status' => DEFAULT_ORDERS_STATUS_ID,
							'currency' => $currency,
							'currency_value' => $currency_value,

							'customer_service_id' => $customer_service_id,
							'payment_method' => $payment_method
							); 

  //old
  tep_db_perform(TABLE_ORDERS, $sql_data_array);
  $insert_id = tep_db_insert_id();
 
 
    $sql_data_array = array('orders_id' => $insert_id,
                            'orders_status_id' => DEFAULT_ORDERS_STATUS_ID,
                            'date_added' => 'now()');
    tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
  
  
      if (defined('MODULE_ORDER_TOTAL_INSTALLED') && tep_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
        $order_total_modules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);
        $co_modules = array();

        foreach($order_total_modules as $key => $value) {
          include(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language . '/modules/order_total/' . $value);
          include(DIR_FS_CATALOG . DIR_WS_MODULES . 'order_total/' . $value);

          $class = substr($value, 0, strrpos($value, '.'));
          $co_modules[$class] = new $class;
		  
          if ($co_modules[$class]->enabled) {
		  
			$sql_data_array = array('orders_id' => $insert_id,
									'title' => $co_modules[$class]->title,
									'text' => $temp_amount,
									'value' => "0.00", 
									'class' => $co_modules[$class]->code, 
									'sort_order' => $co_modules[$class]->sort_order);
			tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
		  
          }
        }
      }
  
/*	

    $sql_data_array = array('orders_id' => $insert_id,
                            'title' => TEXT_SUBTOTAL,
                            'text' => $temp_amount,
                            'value' => "0.00", 
                            'class' => "ot_subtotal", 
                            'sort_order' => "1");
    tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
*/
/*
    $sql_data_array = array('orders_id' => $insert_id,
                            'title' => TEXT_DISCOUNT,
                            'text' => $temp_amount,
                            'value' => "0.00",
                            'class' => "ot_customer_discount",
                            'sort_order' => "2");
    tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
*/
/*
    $sql_data_array = array('orders_id' => $insert_id,
                            'title' => TEXT_DELIVERY,
                            'text' => $temp_amount,
                            'value' => "0.00", 
                            'class' => "ot_shipping", 
                            'sort_order' => "3");
    tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
	
    $sql_data_array = array('orders_id' => $insert_id,
                            'title' => TEXT_TAX,
                            'text' => $temp_amount,
                            'value' => "0.00", 
                            'class' => "ot_tax", 
                            'sort_order' => "4");
    tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
  
      $sql_data_array = array('orders_id' => $insert_id,
                            'title' => TEXT_TOTAL,
                            'text' => $temp_amount,
                            'value' => "0.00", 
                            'class' => "ot_total", 
                            'sort_order' => "5");
    tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);

*/
  tep_redirect(tep_href_link(FILENAME_ORDERS_EDIT, 'oID=' . $insert_id, 'SSL'));

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>