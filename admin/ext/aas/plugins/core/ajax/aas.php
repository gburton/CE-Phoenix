<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
*/

chdir('../../../../../');
require('includes/application_top.php');

if(!isset($_SERVER['HTTP_X_AAS'])) die;
if(isset($sessionTimeout)){

	$dataType = (isset($_POST['dataType']) ? $_POST['dataType'] : 'html');
	if($dataType=='json') echo json_encode(array('response'=>'aasSessionTimeout'));
	else echo'aasSessionTimeout';
	die;
	
}
if($_SERVER['HTTP_X_AAS']!==$_SESSION['admin']['AAS']['ajaxToken']) die;
defined('AAS') or define('AAS', 1);

require('ext/aas/functions/general.php');

if(!function_disabled('set_time_limit')) set_time_limit(0);

if(file_exists('ext/aas/languages/'.$language.'.php')) include 'ext/aas/languages/'.$language.'.php'; else include 'ext/aas/languages/english.php';
if(isset($sessionTimeout)){ echo 'aasSessionTimeout'; die; }

$value_post = (isset($_POST['value']) ? $_POST['value'] : '');
$product_id_post = (isset($_POST['id']) ? $_POST['id'] : '');
$column_post = (isset($_POST['column']) ? $_POST['column'] : '');

if (tep_not_null($column_post)) {
	switch ($column_post) {
		case 'categories_name':
			 if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
					$sql_data_array = array('categories_name' => tep_db_prepare_input($value_post));
					if(tep_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', "categories_id = '" . (int)$product_id_post . "' and language_id = '" . (int)$languages_id . "'")){
					
								 if (USE_CACHE == 'true') {
									tep_reset_cache_block('categories');
									tep_reset_cache_block('also_purchased');
								  }
								  
								  tep_db_query("UPDATE ".TABLE_CATEGORIES." SET last_modified = now() WHERE categories_id='".(int)$product_id_post."' ");
								  
								echo stripslashes($value_post);
					
					}
				}else echo '0';
			}
		break;
		case 'products_name': //product name
			 if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
					$sql_data_array = array('products_name' => tep_db_prepare_input($value_post));
					if(tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "' and language_id = '" . (int)$languages_id . "'")){
					
					  echo stripslashes($value_post);
					  tep_db_query("UPDATE ".TABLE_CATEGORIES." SET last_modified = now() WHERE categories_id='".(int)$product_id_post."' ");
					  
					}
				}else echo AAS_ERROR_EMPTY_VALUE;
			}
		break;
		case 'products_description': //product description
			 if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
				 
				 	//$language_alias_post = (isset($_POST['language_alias']) ? $_POST['language_alias'] : '');
				 	$lid = (isset($_POST['lid']) ? $_POST['lid'] : '');
				 	/*
				 	
					if(tep_not_null($language_alias_post)){
					
						$languages = tep_get_languages();
						for ($i = 0, $n = sizeof($languages); $i < $n; $i++){
							if ($languages[$i]['code'] == $language_alias_post){
								$languages_id=$languages[$i]['id'];
								break;
							}
						}
					
					}
					
					*/
					$sql_data_array = array('products_description' => tep_db_prepare_input($value_post));
					if(tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "' and language_id = '" . (int)$lid . "'")){
					
  					echo 'ok';
  					tep_db_query("UPDATE ".TABLE_CATEGORIES." SET last_modified = now() WHERE categories_id='".(int)$product_id_post."' ");
					
					}
				
				
				}else echo AAS_ERROR_EMPTY_VALUE;
			}
		break;
		case 'products_model': //product model
			 if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
					$sql_data_array = array('products_model' => tep_db_prepare_input($value_post),'products_last_modified' => 'now()');
            				if(tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")) echo stripslashes($value_post);
				}else echo AAS_ERROR_EMPTY_VALUE;
			}
		break;
		case 'products_quantity': //product quantity
			 if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
					if(is_numeric($value_post)){
						$sql_data_array = array('products_quantity' => (int)tep_db_prepare_input($value_post),'products_last_modified' => 'now()');
		    				if(tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")) echo stripslashes($value_post);
					}else echo AAS_ERROR_NONUMERIC_VALUE;
				}else echo AAS_ERROR_EMPTY_VALUE;
			}
		break;
		case 'products_price': //product price
			 if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
					if(is_numeric($value_post)){
						$sql_data_array = array('products_price' => (float)tep_db_prepare_input($value_post),'products_last_modified' => 'now()');
		    				if(tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")) echo stripslashes($value_post);
					}else echo AAS_ERROR_NONUMERIC_VALUE;
				}else echo AAS_ERROR_EMPTY_VALUE;
			}
		break;
		case 'products_price_gross': //product price gross
		
			 if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
					if(is_numeric($value_post)){
					
						//calculate the net price and store it.
						
						$products_query=tep_db_query("SELECT products_tax_class_id FROM ".TABLE_PRODUCTS." WHERE products_id='".(int)$product_id_post."' LIMIT 1 ");
						$products=tep_db_fetch_array($products_query);
						
						$taxRate=tep_get_tax_rate((int)$products['products_tax_class_id']);
												
						$fin=$value_post/(($taxRate/100)+1);
						$fin=tep_round($fin, 4);

						$sql_data_array = array('products_price' => (float)tep_db_prepare_input($fin),'products_last_modified' => 'now()');
		    				if(tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")) echo stripslashes($value_post);
					}else echo AAS_ERROR_NONUMERIC_VALUE;
				}else echo AAS_ERROR_EMPTY_VALUE;
			}
		break;
		case 'products_prices': //product price
		 
			 if(tep_not_null($value_post)){
		
				$values=json_decode(stripslashes($value_post));
	
				foreach($values as $key => $val){
	
					$sql_data_array = array('products_price' => (float)tep_db_prepare_input($val),'products_last_modified' => 'now()');
    					tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$key . "'");
    				
    				}
				echo '1';
			}else echo '0';
			
		break;
		case 'products_prices_gross': //product price
		 
		 	$tax_rates_post = (isset($_POST['tax_rates']) ? $_POST['tax_rates'] : '');
		 
			 if(tep_not_null($value_post) && tep_not_null($tax_rates_post) ){
		
				$values=json_decode(stripslashes($value_post));
				$tax_rates_obj=(array)json_decode(stripslashes($tax_rates_post));
				$tax_rates=array();
				foreach($tax_rates_obj as $txok => $toxv) $tax_rates[$txok]=$toxv;
				
				foreach($values as $key => $val){
				
					$fin=$val/(($tax_rates[$key]/100)+1);
					$fin=tep_round($fin, 4);
			
					$sql_data_array = array('products_price' => (float)tep_db_prepare_input($fin),'products_last_modified' => 'now()');
    					tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$key . "'");
    				
    				}
				echo '1';
			}else echo '0';
			
		break;
		
		case 'products_weight': //product weight
			 if(tep_not_null($product_id_post)){
				 if (tep_not_null($value_post)){
					if(is_numeric($value_post)){
						$sql_data_array = array('products_weight' => (float)tep_db_prepare_input($value_post),'products_last_modified' => 'now()');
		    				if(tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")) echo stripslashes($value_post);
					}else echo AAS_ERROR_NONUMERIC_VALUE;
				}else echo AAS_ERROR_EMPTY_VALUE;
			}
		break;
		case 'products_status': //product status
			 if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
				 	if(strlen($value_post)==1){//check the length
						if($value_post==0 || $value_post==1){
							$sql_data_array = array('products_status' => (int)tep_db_prepare_input($value_post),'products_last_modified' => 'now()');
			    				if(tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")) echo $value_post;
						}else echo AAS_ERROR_STATUS_VALUE_MUST_BE_0_OR_1;
					}else echo AAS_ERROR_STATUS_VALUE_LENGTH_MUST_BE_ONE;	
				}else echo AAS_ERROR_EMPTY_VALUE;
			}
		break;
		
		case 'products_url':
		
			if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
							$sql_data_array = array('products_url' => tep_db_prepare_input($value_post));
			    				if(tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")){
			    				
			    				  echo $value_post;
			    				  tep_db_query("UPDATE ".TABLE_CATEGORIES." SET last_modified = now() WHERE categories_id='".(int)$product_id_post."' ");
			    				
			    				}
				}else echo AAS_ERROR_EMPTY_VALUE;
			}
		
		break;
		
    //categories sort order via input
    case 'sort_order':
			
			if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
							$sql_data_array = array('sort_order' => (int)tep_db_prepare_input($value_post),'last_modified' => 'now()');
			    				if(tep_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', "categories_id = '" . (int)$product_id_post . "'")) echo $value_post;
				}else echo AAS_ERROR_EMPTY_VALUE;
			}

		break;

		/*
			Sorting products if you have a field products_sort_order in products folder
			Check this addon: http://addons.oscommerce.com/info/8311
		*/		
		case 'products_sort_order':
			
			if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
							$sql_data_array = array('products_sort_order' => (int)tep_db_prepare_input($value_post),'products_last_modified' => 'now()');
			    				if(tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")) echo $value_post;
				}else echo AAS_ERROR_EMPTY_VALUE;
			}

		break;
		
		case 'products_tax_class_id':
			 if(tep_not_null($product_id_post)){
				$sql_data_array = array('products_tax_class_id' => (int)tep_db_prepare_input($value_post),'products_last_modified' => 'now()');
			    if(tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")) echo '1_'.AAS_AJAX_TAX_CLASS_CHANGE_SUCCESS;
			}
		break;
		case 'manufacturers_id':
			 if(tep_not_null($product_id_post)){
				$sql_data_array = array('manufacturers_id' => (int)tep_db_prepare_input($value_post),'products_last_modified' => 'now()');
   				if(tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")) echo '1_'.AAS_AJAX_MANUFACTURER_CHANGE_SUCCESS;
			}
		break;
		case 'products_date_available':
			 if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
							$sql_data_array = array('products_date_available' => tep_db_prepare_input($value_post),'products_last_modified' => 'now()');
			    				if(tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")) echo '1';
				}else echo AAS_ERROR_EMPTY_VALUE;
			}
		break;
	  case 'date_added':
			 if(tep_not_null($product_id_post)){
				 if(tep_not_null($value_post)){
          $table = (isset($_POST['table']) ? $_POST['table'] : '');
          if(tep_not_null($table)){
							if($table=='product'){
							  $sql_data_array = array('products_date_added' => tep_db_prepare_input($value_post),'products_last_modified' => 'now()');
			      		if(tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$product_id_post . "'")) echo date('Y-m-d h:i:s',strtotime($value_post));
			    		}else{
			      		$sql_data_array = array('date_added' => tep_db_prepare_input($value_post),'last_modified' => 'now()');
			      		if(tep_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', "categories_id = '" . (int)$product_id_post . "'")) echo date('Y-m-d h:i:s',strtotime($value_post));
			    		}
			    }else echo '0';
			    
				}else echo AAS_ERROR_EMPTY_VALUE;
			}
		break;
		case 'products_attributes_smart_copy':

			 if(tep_not_null($product_id_post)){
			 	$responce='';

					 if(tep_not_null($value_post)){
					 
						 $exp=explode('_@_',$value_post);
						 foreach($exp as $va){

						 	$vals=explode(';',$va);
							
							switch($vals[3]){
					  
					      case'plus': $prefix='+'; break;
					      case'minus': $prefix='-'; break;
					      default: $prefix='';
					    
					    }
					
							tep_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES . " (products_id, options_id, options_values_id,options_values_price,price_prefix) VALUES ('".(int)$product_id_post."','".$vals[0]."','".$vals[1]."','".$vals[2]."','".$prefix."')");
							
							$last_inserted_id=tep_db_insert_id();
							
							if(DOWNLOAD_ENABLED=='true'){
						
							//update products attributes downloads
							tep_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " (products_attributes_id,products_attributes_filename,products_attributes_maxdays,products_attributes_maxcount) VALUES('".$last_inserted_id."','".$vals[4]."','".$vals[5]."','".$vals[6]."') ");
						
							}
						 
						 
						 }
						 $responce='reload';
					 
					 }
					 
					 
					 echo $responce;
					 
		  }
				
		
		break;		
		case 'products_attributes':
			 if(tep_not_null($product_id_post)){
			 	$responce='ok';
				 if(tep_not_null($value_post)){
				 
					$exp=explode('_@_',$value_post);					
					
					foreach($exp as $va){
												
						$vals=explode(';',$va);
					
					  switch($vals[4]){
					  
					    case'plus': $prefix='+'; break;
					    case'minus': $prefix='-'; break;
					    default: $prefix='';
					  
					  }
					
						tep_db_query("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . " SET options_id='".$vals[1]."' , options_values_id='".$vals[2]."', options_values_price='".$vals[3]."', price_prefix='".$prefix."' WHERE products_attributes_id='".(int)$vals[0]."' AND products_id = '".(int)$product_id_post."'");
						
						if(DOWNLOAD_ENABLED=='true'){
						
							//check to see if exists first
							$resp_pad=tep_db_query("SELECT products_attributes_id FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " WHERE products_attributes_id='".(int)$vals[0]."'");
							if(tep_db_num_rows($resp_pad)==1){
							
								if(tep_not_null($vals[5])){
								
									//update products attributes downloads
									tep_db_query("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " SET products_attributes_filename='".$vals[5]."', products_attributes_maxdays='".$vals[6]."', products_attributes_maxcount='".$vals[7]."' WHERE products_attributes_id='".(int)$vals[0]."' ");
								
								}else{
									
									//delete because empty filename
									tep_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " WHERE products_attributes_id='".(int)$vals[0]."' ");
								
								}
							
							}else{
							
								if(tep_not_null($vals[5])){
								
									tep_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " (products_attributes_id,products_attributes_filename,products_attributes_maxdays,products_attributes_maxcount) VALUES('".(int)$vals[0]."','".$vals[5]."','".$vals[6]."','".$vals[7]."') ");
								
								}
							
							}
							
						
						}

					}
					$responce.='';

				}
				$vpost = (isset($_POST['value1']) ? $_POST['value1'] : '');
					 if(tep_not_null($vpost)){
					 
						 $exp=explode('_@_',$vpost);
						 foreach($exp as $va){

						 	$vals=explode(';',$va);
							
							switch($vals[3]){
					  
					      case'plus': $prefix='+'; break;
					      case'minus': $prefix='-'; break;
					      default: $prefix='';
					    
					    }
					
							tep_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES . " (products_id, options_id, options_values_id,options_values_price,price_prefix) VALUES ('".(int)$product_id_post."','".$vals[0]."','".$vals[1]."','".$vals[2]."','".$prefix."')");
							
							$last_inserted_id=tep_db_insert_id();
							
							if(DOWNLOAD_ENABLED=='true'){
						
							//update products attributes downloads
							tep_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " (products_attributes_id,products_attributes_filename,products_attributes_maxdays,products_attributes_maxcount) VALUES('".$last_inserted_id."','".$vals[4]."','".$vals[5]."','".$vals[6]."') ");
						
							}
						 
						 }
						 $responce.='_reload';
					 
					 }
					 
				echo $responce;
				
			}
		break;
		case'delete_attribute':
		
			if(tep_not_null($value_post)){
			  
			 	tep_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_attributes_id='".(int)$value_post."'");
			 	
		 		if(DOWNLOAD_ENABLED=='true'){
		 		
		 		  tep_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " WHERE products_attributes_id='".(int)$value_post."'");
		 		
		 		}

			 	echo 'reload_attributes';

			}else echo 'error';
		
		break;
		case'delete_attributes':

			if(tep_not_null($value_post)){
			  
			 	tep_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_attributes_id IN (".$value_post.") ");

				if(DOWNLOAD_ENABLED=='true'){
			 	
			 		tep_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " WHERE products_attributes_id IN (".(int)$value_post.") ");
			 		
			  }

			 	echo 'reload_attributes';

			}else echo 'error';
		
		break;
		case'set_session':
		
			$action = (isset($_POST['action']) ? $_POST['action'] : '');

			if(tep_not_null($action)){

				$_SESSION['admin']['AAS'][$action]=$value_post;
				echo '1';

			}else echo '0';
		
		break;
		default:
		
			echo stripslashes($value_post);
		
	}
}
?>
