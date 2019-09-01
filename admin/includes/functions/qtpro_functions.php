<?php
//These are functions for calculating and dooing different QTPro things.
//The future goal is that this kit of functions will make the integration of other contributions easier.
//Contributors: Please feel free to ad new functions to this kit. But please make sure that they are error free.


//-------------------------//
//---  Small Tools  ---//
//-------------------------//
/*
This function will take a string looking like "1-2,3-4,5-6" and return an array looking like:
Array
(
    [1] => 2
    [3] => 4
    [5] => 6
)
*/
function qtpro_products_attributes_string2array($products_attributes_string) {
  $ret_array = array();
  
	$optionchoise_array =explode(',', $products_attributes_string);// values in $option_choise_array looks like: '1-2'
	//Now put them into $ret_array in a correct way:
	foreach ($optionchoise_array as $optionchoise) {
		$splitted_array = explode('-', $optionchoise);
		$option = $splitted_array[0];
		$choise = $splitted_array[1];
		$ret_array[$option] = $choise;
	}
	
	return $ret_array;
}

/*
This function will take a string looking like "1-2,3-4,5-6" and return an array looking like:
Array
(
    [0] => 1
    [2] => 3
    [3] => 5
)
*/
function qtpro_products_attributes_string2options_array($products_attributes_string) {
  $ret_array = array();
  
	$optionchoise_array = explode(',', $products_attributes_string);// values in $option_choise_array looks like: '1-2'
	//Now put them into $ret_array in a correct way:
	foreach ($optionchoise_array as $optionchoise) {
		$splitted_array = explode('-', $optionchoise);		
		$ret_array[] = $splitted_array[0];
	}
	
	return $ret_array;
}

//-------------------------//
//---  Doctor Functions  ---//
//-------------------------//

//This is the most detailed doctor function for examining a product.
//When is a product not healthy? Answer: there are three types of errors. The first is called "intruder" error. This is when there exist an attribute in the products_stock_attributes string which is not stocktracked. The Second type of error called "lack" error. This is when an attribute is missing in products_stock_attributes; that is: when the product has an attribute with tracked stock and that attribute is not in the products_stock_attributes. The third type of error is when the current summary stock for a product isn't the summary stock we get if we calculate it.
function qtpro_doctor_investigate_product($products_id) {
  $facts_array = array();
  $facts_array['id'] = $products_id;
  $facts_array['any_problems'] = false;
  $facts_array['has_tracked_options'] = false;

  $facts_array['summary_and_calc_stock_match'] = true; //If summary_stock and calc_stock is the same value; this = true; else this = false
  $facts_array['summary_stock'] = 0; //The current summary stock for this product in the database.
  $facts_array['calc_stock'] = 0; //The summary stock calculated by looking at the options products_stock.

  $facts_array['stock_entries_healthy'] = true; //If any row is sick; this = true; else this = false;
  $facts_array['stock_entries_count'] = 0; //The number of rows this product had in the options products_stock database table.
  $facts_array['sick_stock_entries_count'] = 0;//The number of sick rows this product had in the options products_stock database table.
  $facts_array['lacks_id_array'] = array(); //An array with all the id:s of the options that were lacked anywhere in the options products_stock table.
  $facts_array['intruders_id_array'] = array(); //An array with all the id:s of the options that were intruding anywhere in the options products_stock table.

	$facts_array['has_tracked_options'] = qtpro_product_has_tracked_options($products_id);
	$facts_array['summary_stock'] = qtpro_get_products_summary_stock($products_id);
	
	//Calculate the summary stock by looking att the stock for the different options
	if ($facts_array['has_tracked_options']) {
		$products_stock_quantity_query = tep_db_query("SELECT products_stock_quantity FROM products_stock WHERE products_id = '" . $products_id . "'");
		
		while ($row = tep_db_fetch_array($products_stock_quantity_query)) {
			if ($row['products_stock_quantity'] > 0) { //If they are negative they are oversold and this do not affect what we have on stock.
				$facts_array['calc_stock']+= $row['products_stock_quantity'];
			}
		}
		
	} else {
		//Set the calc_stock to the summary stock
		$facts_array['calc_stock'] = $facts_array['summary_stock'];
	}
	
	//Decide summary_and_calc_stock_match
	if ($facts_array['summary_stock'] == $facts_array['calc_stock']) {
		$facts_array['summary_and_calc_stock_match'] = true;
	} else {
		$facts_array['summary_and_calc_stock_match'] = false;
	}

	//Get all products_stock entries for the product. ---------------------------------------
	$attributes_stock_query = tep_db_query("SELECT products_stock_attributes
											                    FROM products_stock 
											                    WHERE products_id = '" . $products_id . "'");
											
	$facts_array['stock_entries_count'] = tep_db_num_rows($attributes_stock_query);
	
	if ($facts_array['stock_entries_count'] == 0) {
		$facts_array['sick_stock_entries_count'] = 0;
		$facts_array['stock_entries_healthy'] = true;	
	} else {
		//Get the id for all options this product has and put them in the array: $products_options_array  ---------------------------------------
		$products_options_query = tep_db_query("SELECT DISTINCT options_id
												                    FROM products_attributes 
												                    WHERE products_id = '" . $products_id . "'");
		$products_options_array = array();
		while ($products_option_id = tep_db_fetch_array($products_options_query)) {
			$products_options_array[] =$products_option_id['options_id'];
		}
												
		//Get the id for all attributes which are tracked and put them in the array: $tracked_options_array  ---------------------------------------
		$tracked_options_query = tep_db_query("SELECT DISTINCT products_options_id
												                   FROM products_options 
												                   WHERE products_options_track_stock = 1");
		$tracked_options_array = array();
		while ($tracked_option_id = tep_db_fetch_array($tracked_options_query)) {
			$tracked_options_array[] =$tracked_option_id['products_options_id'];
		}
		//Ok so now we can check if the option_id 8 is tracked by running: in_array(8, $tracked_options_array) =)
		

		//Check every products_stock_attributes for errors
		while ($products_stock_attributes = tep_db_fetch_array($attributes_stock_query)) {
			$this_row_is_sick = false;
			$string_options_array = qtpro_products_attributes_string2options_array($products_stock_attributes['products_stock_attributes']);
			
			//Now check for "intruder" errors (check for attributes which are there but should not as they are not stocktracked)
			foreach ($string_options_array as $option) {
				if (!in_array($option, $tracked_options_array)) {
					$this_row_is_sick = false;
					$facts_array['sick_stock_entries_count']++;
					$facts_array['stock_entries_healthy'] = false;
					$facts_array['intruders_id_array'][] = $option;
				}
			}
			
			//Now check for "lack" errors (check for attributes should be there, because they are stocktracked, but for some reason are not)
			foreach ($products_options_array as $product_option) {
				if (in_array($product_option, $tracked_options_array) && !in_array($product_option, $string_options_array)) {
					$this_row_is_sick = false;
					
					$facts_array['stock_entries_healthy'] = false;
					$facts_array['lacks_id_array'][] = $product_option;
				}
			}

			if ($this_row_is_sick) {
				$facts_array['sick_stock_entries_count']++;
			}
			
		}
	}
	
	//Set the overwiev variables:
	if (!$facts_array['summary_and_calc_stock_match'] or !$facts_array['stock_entries_healthy']) {
		$facts_array['any_problems'] = true;
	}

	return $facts_array;
}

function qtpro_doctor_formulate_database_investigation() {
	print '<p>' . WARNING_SICK_PRODUCTS . '</p>';
	$prod_query = tep_db_query("SELECT products_id FROM products");
	while ($product = tep_db_fetch_array($prod_query)) {
		$investigation= qtpro_doctor_investigate_product($product['products_id']);
		if ($investigation['any_problems']) {
			print '<p class="messageStackWarning">' . WARNING_PRODUCT_ID . $product['products_id'] . ': ' . qtpro_doctor_formulate_product_investigation($investigation, 'short_suggestion') . '</p>';
		}
	}
}

function qtpro_doctor_formulate_product_investigation($facts_array, $formulate_style) {
  $str_ret ='';
  
	switch ($formulate_style) {
		case 'short_suggestion':
			if ($facts_array['any_problems']) {
				if ( $facts_array['summary_and_calc_stock_match'] != true && $facts_array['stock_entries_healthy'] != true ) {
					$str_ret = WARNING_PRODUCT_DATABASE_ENTRY_SUMMARY . '<a href="' . tep_href_link("stock.php", 'product_id=' . $facts_array['id']) . '" class="headerLink">' . WARNING_PRODUCTS_STOCK . '</a>.';
				} elseif (!$facts_array['summary_and_calc_stock_match']) {
					$str_ret = WARNING_PRODUCT_SUMMARY_STOCK . '<a href="' . tep_href_link("stock.php", 'product_id=' . $facts_array['id']) . ' " class="headerLink">products stock</a>.';
				} elseif (!$facts_array['stock_entries_healthy']) {
					$str_ret =WARNING_PRODUCT_DATABASE_ENTRY . ' <a href="' . tep_href_link("stock.php", 'product_id=' . $facts_array['id']) . ' " class="headerLink">products stock</a>.';
				} else {
					$str_ret ="Errorcatsh 754780+94322354678";
				}
			} else {
				$str_ret = WARNING_PRODUCT_OK;
			}
		
		break;
		case 'detailed':
			//Create Header
			if ($facts_array['any_problems']) {
				$str_ret = TEXT_DETAILED_STOCK_NEEDS_ATTENTION;
			}else{
				$str_ret = TEXT_DETAILED_STOCK_ALL_OK;
			}
			
			//Talk about summary and calc stock
			if ($facts_array['summary_and_calc_stock_match']) {
				$str_ret .= sprintf(TEXT_DETAILED_STOCK_MATCH_TRUE, $facts_array['summary_stock']);
			} else {
				$str_ret .= sprintf(TEXT_DETAILED_STOCK_MATCH_FALSE, $facts_array['summary_stock'], $facts_array['calc_stock']);
			}

			//Talk about the health of the stock entries
			if ($facts_array['stock_entries_healthy']) {
				$str_ret .= sprintf(TEXT_DETAILED_STOCK_ENTRIES_HEALTHY, $facts_array['stock_entries_count'], $facts_array['sick_stock_entries_count']);
				
			} else {
				$str_ret .= sprintf(TEXT_DETAILED_STOCK_ENTRIES_NOT_HEALTHY, $facts_array['stock_entries_count'], $facts_array['sick_stock_entries_count']);
				
				if (sizeof($facts_array['lacks_id_array']) > 0) {
					$str_ret .= '<b>These options were missing in row(s):</b><br />';
					foreach ($facts_array['lacks_id_array'] as $lack_id) {
						$str_ret .= '<span style="color:red;"><b>'. tep_options_name($lack_id) .'</b></span><br>';
					}
					$str_ret .= TEXT_DETAILED_STOCK_SOLUTIONS_STOP_TRACKING;
				}
				
				if (sizeof($facts_array['intruders_id_array']) > 0) {
					$str_ret .= TEXT_DETAILED_STOCK_OPTIONS_SHOULD_NOT_EXIST;
					foreach ($facts_array['intruders_id_array'] as $intruder_id) {
						$str_ret .= '<span style="color:red;"><b>'. tep_options_name($intruder_id) .'</b></span><br>';
					}
					$str_ret .= TEXT_DETAILED_STOCK_SOLUTIONS_START_TRACKING;
				}
				
			}
			
			//Talk about automatic solutions
			if ($facts_array['any_problems']) {
				$str_ret .= TEXT_DETAILED_STOCK_AUTOMATIC_SOLUTIONS_AVAILABLE;
				
				if (!$facts_array['stock_entries_healthy']) {
					$str_ret .='<p><a href="' . tep_href_link('qtprodoctor.php', 'action=amputate&pID='.$facts_array['id'], 'NONSSL') . '" class="menuBoxContentLink" target="_blank">' . TEXT_DETAILED_STOCK_LINK_AMPUTATION . '</a></p>';
				}
				if (!$facts_array['summary_and_calc_stock_match']) {
					$str_ret .='<p><a href="' . tep_href_link('qtprodoctor.php', 'action=update_summary&pID='.$facts_array['id'], 'NONSSL') . '" class="menuBoxContentLink" target="_blank">' . sprintf(TEXT_DETAILED_STOCK_LINK_SET_SUMMARY, $facts_array['calc_stock']) .'</a></p>';
				}
				
				$str_ret .='</p>';
			}
			
			
		break;
	}

	return $str_ret;
}

function qtpro_doctor_product_healthy($products_id) {
	$results = qtpro_doctor_investigate_product($products_id);
	if ($results['any_problems'] == false) {
		return true;
	} else {
		return false;
	}
}

//This function will delete all option stock entries from the product.
function qtpro_doctor_amputate_all_from_product($products_id) {
	tep_db_query("DELETE FROM products_stock WHERE products_id = '" . $products_id . "'");
}

function qtpro_doctor_amputate_bad_from_product($products_id) {
  $return_amputate_count = 0;

	//MISSION CODENAME "Get information" STARTS HERE
	//Get all products_stock entries for the product. ---------------------------------------
	$attributes_stock_query = tep_db_query("SELECT products_stock_attributes, products_stock_id
											                    FROM products_stock 
											                    WHERE products_id = '" . $products_id . "'");
											
	//Ops! a sub mission to possibly save work:
	if (tep_db_num_rows($attributes_stock_query) == 0) {
		//This is normal if the product has NO strackstocked attributes
		//BUT it can also happen for products WITH strackstocked attributes. Nothing in stock that is.
		return $return_amputate_count; //The surgery is complete. Doctor says nothing to amputate :D
	}
	//Submission complete; let's continue
	
	//Get the id for all options this product has and put them in the array: $products_options_array  ---------------------------------------
	$products_options_query = tep_db_query("SELECT DISTINCT options_id
											                    FROM products_attributes 
											                    WHERE products_id = '" . $products_id . "'");
	$products_options_array = array();
	while ($products_option_id = tep_db_fetch_array($products_options_query)) {
		$products_options_array[] =$products_option_id['options_id'];
	}
											
	//Get the id for all attributes which are tracked and put them in the array: $tracked_options_array  ---------------------------------------
	$tracked_options_query = tep_db_query("SELECT DISTINCT products_options_id
											                   FROM products_options 
											                   WHERE products_options_track_stock = 1");
	$tracked_options_array = array();
	while ($tracked_option_id = tep_db_fetch_array($tracked_options_query)) {
		$tracked_options_array[] =$tracked_option_id['products_options_id'];
	}
	//Ok so now we can check if the option_id 8 is tracked by running: in_array(8, $tracked_options_array) =)
	
	//MISSION CODENAME "Get information" ENDS HERE
	
	
	//Check every row for errors
	while ($products_stock_attributes = tep_db_fetch_array($attributes_stock_query)) {
		$amputate_this = false;
		$string_options_array = qtpro_products_attributes_string2options_array($products_stock_attributes['products_stock_attributes']);
		
		//Now check for "intruder" errors (check for attributes which are there but should not as they are not stocktracked)
		foreach ($string_options_array as $option) {
			if (!in_array($option, $tracked_options_array)) {
				//aha! an "intruder"
				$amputate_this = true; //The examination is complete. Doctor says this products_stock_id must be amputated :'(
			}
		}
		
		//Now check for "lack" errors (check for attributes should be there, because they are stocktracked, but for some reason are not)
		foreach ($products_options_array as $products_option) {
			if ( in_array($products_option, $tracked_options_array) && !in_array($products_option, $string_options_array) ) {
				//aha! a "lack"
				$amputate_this = true; //The examination is complete. Doctor says this products_stock_id must be amputated :'(
			}
		}
		
		if ($amputate_this) {
			tep_db_query("DELETE 
						        FROM products_stock
						        WHERE products_stock_id =". $products_stock_attributes['products_stock_id']);	
			$return_amputate_count++;		
		}
	}

return $return_amputate_count; //This will return the array with the amputate count.
}

//This function will update the summary_stock for a product
function qtpro_update_summary_stock($products_id) {
      tep_db_query("UPDATE products  
                    SET products_quantity = " . qtpro_calculate_summary_stock($products_id) . "
                    WHERE products_id = '" . $products_id . "'");
}

//------------------------------------------//
//---  Product Investigation Functions  ---//
//----------------------------------------//

function qtpro_product_exists($products_id) {
	$prod_query = tep_db_query("SELECT products_id FROM products WHERE products_id = '" . $products_id . "'");
	if (tep_db_num_rows($prod_query) == 0) { 
		//Nothing was found so it did not exist.
		return false;
	} else {	
		return true;
	}
}

function qtpro_product_has_tracked_options($products_id) {
	//Get the id for all options this product has and put them in the array: $products_options_array  ---------------------------------------
	$products_options_query = tep_db_query("SELECT DISTINCT options_id
											                    FROM products_attributes 
											                    WHERE products_id = '" . $products_id . "'");
	$products_options_array = array();
	while ($products_option_id = tep_db_fetch_array($products_options_query)) {
		$products_options_array[] =$products_option_id['options_id'];
	}
											
	//Get the id for all attributes which are tracked and put them in the array: $tracked_options_array  ---------------------------------------
	$tracked_options_query = tep_db_query("SELECT DISTINCT products_options_id
											                   FROM products_options 
											                   WHERE products_options_track_stock = 1");
	$tracked_options_array = array();
	while ($tracked_option_id = tep_db_fetch_array($tracked_options_query)) {
		$tracked_options_array[] =$tracked_option_id['products_options_id'];
	}
	//Ok so now we can check if the option_id 8 is tracked by running: in_array(8, $tracked_options_array) =)

	//Do the test:
	foreach ($products_options_array as $products_option) {
		if (in_array($products_option, $tracked_options_array)) {
			return true;
		}
	}
	return false;
}

function qtpro_get_products_summary_stock($products_id) {
	$products_summary_stock_query = tep_db_query("SELECT products_quantity
											                          FROM products 
											                          WHERE products_id = '" . $products_id . "'");
	$product_facts = tep_db_fetch_array($products_summary_stock_query);
	if ($product_facts['products_quantity'] > 0) { //If they are negative they are oversold and this do not affect what we have on stock. added by @raiwa to avoid error if negative stock
	  return $product_facts['products_quantity'];
	} else {
	  return 0;
	}
}

//This function will calculate and return the summary_stock for a product. If it is a product without tracked attributes the summary_stock will be returned anyway.
//NOTE!!!: This function will include all entries. Even damaged ones...
function qtpro_calculate_summary_stock($products_id) {
  $summary_stock_to_return = 0;
	if (qtpro_product_has_tracked_options($products_id)) {
		//Calculate the summary stock by looking att the stock for the different options
		//Get all products_stock entries for the product. ---------------------------------------
		$products_stock_quantity_query = tep_db_query("SELECT products_stock_quantity
												                           FROM products_stock 
												                           WHERE products_id = '" . $products_id . "'");
		while ($row = tep_db_fetch_array($products_stock_quantity_query)) {
			if ($row['products_stock_quantity'] > 0) { //If they are negative they are oversold and this do not affect what we have on stock.
				$summary_stock_to_return+= $row['products_stock_quantity'];
			}
		}
		
	} else {
		//Just return he current summary stock
		$summary_stock_to_return = qtpro_get_products_summary_stock($products_id);
	}
return $summary_stock_to_return;
}

function qtpro_products_summary_stock_is_as_calculated($products_id) {
	if (qtpro_calculate_summary_stock($products_id) == qtpro_get_products_summary_stock($products_id)) {
		return true;
	} else {
		return false;
	}
}



//-------------------------//
//---  Trash-Tools ---//
//-------------------------//

//This function will determine if the parameter row (taken from database table products_stock) is trash
//It is if the products it liks to not exists.
//The $row_array must contain the keys: 'products_id'
function qtpro_stock_row_is_trash($row_array) {
  global $products_id;
	$prod_query = tep_db_query("SELECT products_id FROM products WHERE products_id = '" . $products_id . "'");

	if (qtpro_product_exists($row_array['products_id'])) { 
		return false;
	} else {
		//The product this row links to does not exists. So it is trash then
		return true;	
	}
}

//This function will count the number of strash rows in the database.
//These rows should never come to exist but this is a good statistical fact for progammers as this indicate something is wrong
function qtpro_number_of_trash_stock_rows() {
  $trash_count_ret = 0;
	$products_stock_row_query = tep_db_query("SELECT products_id FROM products_stock");
	while ($row = tep_db_fetch_array($products_stock_row_query)) {
		if (qtpro_stock_row_is_trash($row)) {
			$trash_count_ret++;
		}
	}
	return $trash_count_ret;
}

// This function will erase all trash rows in the database table for products option stock.
function qtpro_chuck_trash() {
  $trash_count_ret = 0;
	$products_stock_row_query = tep_db_query("SELECT products_stock_id, products_id FROM products_stock");
	while ($row = tep_db_fetch_array($products_stock_row_query)) {
		if (qtpro_stock_row_is_trash($row)) {
			tep_db_query("DELETE FROM products_stock WHERE products_stock_id= '" . $row['products_stock_id'] . "'");
			$trash_count_ret++;
		}
	}	
	return $trash_count_ret;
}

//-------------------------//
//---     Statistics    ---//
//-------------------------//

function qtpro_normal_product_count() {
	$prod_query = tep_db_query("SELECT products_id FROM products");
	return tep_db_num_rows($prod_query);
}

function qtpro_tracked_product_count() {
  $count_ret = 0;
	$prod_query = tep_db_query("SELECT products_id FROM products");
	while ($product = tep_db_fetch_array($prod_query)) {
		if (qtpro_product_has_tracked_options($product['products_id'])) {
			$count_ret++;
		}	
	}
	return $count_ret;
}

function qtpro_sick_product_count() {
  $count_ret = 0;
	$prod_query = tep_db_query("SELECT products_id FROM products");
	while ($product = tep_db_fetch_array($prod_query)) {
		if (!qtpro_doctor_product_healthy($product['products_id'])) {
			$count_ret++;
		}	
	}
	return $count_ret;
}

?>
