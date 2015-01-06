<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
  Information: Specials functions called after ajax

*/
chdir('../../../../');
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

if(file_exists('ext/aas/languages/'.$language.'.php')) include 'ext/aas/languages/'.$language.'.php'; else include 'ext/aas/languages/english.php';
if(isset($sessionTimeout)){echo'aasSessionTimeout'; die;}

require('ext/aas/application_top.php');

$action = (isset($_POST['action']) ? $_POST['action'] : '');

switch($action){

	case'getCellData':
	
		$products_id = (isset($_POST['products_id']) ? tep_db_prepare_input($_POST['products_id']) : '');
		
		if(tep_not_null($products_id)){
		
			$special_pid=tep_db_query("select p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = s.products_id AND p.products_id='".(int)$products_id."' limit 1");
			
			if($special_pid && tep_db_num_rows($special_pid)>0){
			
				$sp=tep_db_fetch_array($special_pid);
				
				if($sp['status']=='1'){
						
						$ficon='icn_alert_success.png';
						$statusTitle=IMAGE_ICON_STATUS_RED_LIGHT;
						
				}else{
							
						$ficon='icn_alert_error.png';
						$statusTitle=IMAGE_ICON_STATUS_GREEN_LIGHT;
					
				}
				
				?>
				
				<span class="oldPrice"><?php echo $currencies->format($sp['products_price']); ?></span>
				<span class="specialPrice"><?php echo $currencies->format($sp['specials_new_products_price']); ?></span>
				<?php
							
						if($fieldsArray['products_price_gross']['visible']){

							$taxRate=tep_get_tax_rate($sp['products_tax_class_id']);
							echo '<span class="specialPriceGross">'.$currencies->format(tep_get_price_with_tax($sp['specials_new_products_price'],$taxRate,$currency_symbols[0])).' (Gross)</span>';
						
						}
							
				?>

				,&nbsp;<?php echo AAS_SPECIALS_TEXT_STATUS; ?><a class="radiostockajax-special" id="special-status_<?php echo $sp['specials_id']; ?>" href="#" ><?php echo tep_image(DIR_WS_ADMIN . 'ext/aas/images/'.$ficon, $statusTitle); ?></a>
				,&nbsp;<?php echo AAS_SPECIALS_TEXT_EXPIRES_AT; ?><input type="text" style="text-align:center" class="lfor specials_expires_at" id="specials_expires_at_<?php echo $sp['specials_id']; ?>" value="<?php echo substr($sp['expires_date'],0,-9); ?>" />
				<a id="specials-unexpire_<?php echo $sp['specials_id']; ?>" class="specials-unexpire" title="<?php echo AAS_SPECIALS_TEXT_NEVER_EXPIRE; ?>" style="<?php echo tep_not_null($sp['expires_date']) ? '':'visibility:hidden;'; ?>" href="#"><img style="opacity:0.3;height:20px" src="ext/aas/images/remove_white_no_round_1.png" alt="never expire"></a>
				<?php if($defaults['enableSpecials']){ ?>
				<a href="#" id="edit-selected-product-as-special_<?php echo $sp['specials_id']; ?>" class="edit-selected-product-as-special" title="<?php echo AAS_SPECIALS_TEXT_EDIT_SPECIAL; ?>"><img style="opacity:0.6;height:15px" src="ext/aas/images/glyphicons_030_pencil.png" alt="Edit"></a>
				<?php }
			
			}else echo '0';
	
		}else echo '0';
		
	break;

	case'getSpecialsTable':
	
		$specials_query = tep_db_query("select p.products_id, pd.products_name, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = s.products_id order by pd.products_name");
		
		 if(tep_db_num_rows($specials_query)>0 ){
		 
				$status_unavailable_class='';
				$counter=1;
				while ($specials = tep_db_fetch_array($specials_query)) { 
				
				if($specials['status']=='0') $status_unavailable_class=' unavailable'; else $status_unavailable_class='';

				?>
				<tr class="<?php echo ($counter & 1) && $defaults['colorEachTableRowDifferently'] ? 'odd' : 'even'; echo $status_unavailable_class ?>" id="sid_<?php echo $specials['specials_id']; ?>" >
					<td><?php echo $specials['products_name']; ?><a target="_blank" title="<?php echo AAS_SPECIALS_TEXT_VIEW_PRODUCTS_PAGE; ?>" href="<?php echo HTTP_CATALOG_SERVER.DIR_WS_CATALOG; ?>product_info.php?products_id=<?php echo $specials['products_id']; ?>"><img class="special-open-products-page" src="ext/aas/images/glyphicons_152_new_window.png" alt="" /></a></td>
					<td><span class="oldPrice"><?php echo $currencies->format($specials['products_price']); ?></span> <span class="specialPrice"><?php echo $currencies->format($specials['specials_new_products_price']); ?></span><span class="oldPrice_raw" style="display:none;"><?php echo $specials['products_price']; ?></span><span class="specialPrice_raw" style="display:none;"><?php echo $specials['specials_new_products_price']; ?></span></td>
					<td><?php echo (int)$specials['products_price'] ? number_format(100 - (($specials['specials_new_products_price'] / $specials['products_price']) * 100)) . '%' : '0%'; ?></td>
					<td>
											
					<?php if($specials['status'] == '1'){
						
							$ficon='icn_alert_success.png';
							$statusTitle=IMAGE_ICON_STATUS_RED_LIGHT;
							
						}else{
							
							$ficon='icn_alert_error.png';
							$statusTitle=IMAGE_ICON_STATUS_GREEN_LIGHT;
							
						}
						
						echo '<a class="specials_status" id="status_'.$specials['specials_id'].'" href="#" >'.tep_image(DIR_WS_ADMIN . 'ext/aas/images/'.$ficon, $statusTitle).'</a>';
					?>
					
					</td>
					<td><?php echo substr($specials['specials_date_added'],0,-9); ?></td>
					<td><?php echo substr($specials['specials_last_modified'],0,-9); ?></td>
					<td>
					
					<input type="text" style="text-align:center;cursor:pointer;" id="specials_datepicker_<?php echo (int)$specials['specials_id']; ?>" class="specials_datepicker lfor" value="<?php echo substr($specials['expires_date'],0,-9); ?>" /><a id="specials-never-expire_<?php echo (int)$specials['specials_id']; ?>" class="specials-never-expire" title="<?php echo AAS_SPECIALS_TEXT_NEVER_EXPIRE; ?>" style="<?php echo tep_not_null($specials['expires_date']) ? '':'visibility:hidden;'; ?>" href="#"><img style="opacity:0.3;height:20px" src="ext/aas/images/remove_white_no_round_1.png" alt="Close"></a>
					</td>
					<td>
					<button class="applyButton specials-edit-button" id="epid_<?php echo $specials['products_id']; ?>"><?php echo AAS_SPECIALS_TEXT_EDIT; ?></button><button class="applyButton specials-delete-button" id="dpid_<?php echo $specials['products_id']; ?>"><?php echo AAS_SPECIALS_TEXT_DELETE; ?></button>
					</td>
				</tr>
				<?php $counter++; }
		 
		 
		 }
	
	break;

	case'insert':

		$products_id = (isset($_POST['products_id']) ? tep_db_prepare_input($_POST['products_id']) : '');
		$specials_price = (isset($_POST['specials_price']) ? tep_db_prepare_input($_POST['specials_price']) : '');
		$expiry_date = (isset($_POST['expiry_date']) ? tep_db_prepare_input($_POST['expiry_date']) : '');

		if(tep_not_null($products_id)){

		    if (substr($specials_price, -1) == '%') {
		      $new_special_insert_query = tep_db_query("select products_id, products_price from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
		      $new_special_insert = tep_db_fetch_array($new_special_insert_query);

		      $products_price = $new_special_insert['products_price'];
		      $specials_price = ($products_price - (($specials_price / 100) * $products_price));
		    }

		    $expires_date = '';
		    if (tep_not_null($expiry_date)) {
		      $expires_date = substr($expiry_date, 0, 4) . substr($expiry_date, 5, 2) . substr($expiry_date, 8, 2);
		    }

		    if(tep_db_query("insert into " . TABLE_SPECIALS . " (products_id, specials_new_products_price, specials_date_added, expires_date, status) values ('" . (int)$products_id . "', '" . tep_db_input($specials_price) . "', now(), " . (tep_not_null($expires_date) ? "'" . tep_db_input($expires_date) . "'" : 'null') . ", '1')")) echo '1';
		    else echo '0';
		    
        
        }else echo '0';
	
	
	break;

	case'getProductsList':
	
	// create an array of products on special, which will be excluded from the pull down menu of products
	// (when creating a new product on special)
      $specials_array = array();
      $specials_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s where s.products_id = p.products_id");
      while ($specials = tep_db_fetch_array($specials_query)) {
        $specials_array[] = $specials['products_id'];
      }
    
	$products_id = (isset($_POST['pid']) ? tep_db_prepare_input($_POST['pid']) : '');
	
	if(tep_not_null($products_id)){
	
		    global $currencies, $languages_id;

			$exclude = $specials_array;

			$select_string = '<select name="products_id" id="specials-list-products_id" style="" >';

			$products_query = tep_db_query("select p.products_id, pd.products_name, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by products_name");
			while ($products = tep_db_fetch_array($products_query)) {
			  if (!in_array($products['products_id'], $exclude)) {
				$select_string .= '<option '.($products['products_id']==$products_id?'selected="selected"':'').' value="' . $products['products_id'] . '">' . $products['products_name'] . ' (' . $currencies->format($products['products_price']) . ')</option>';
			  }
			}

			$select_string .= '</select>';

			echo $select_string;
	
	
	}else echo '0';
	
	break;


	case'delete':
	
		$specials_id = (isset($_POST['specials_id']) ? tep_db_prepare_input($_POST['specials_id']) : '');

		if(tep_not_null($specials_id)){
		
		    if(tep_db_query("delete from " . TABLE_SPECIALS . " where specials_id = '" . (int)$specials_id . "'")) echo '1';
			else echo '0';

		}else echo '0';
		
	break;
	
	case'update':
	
		$specials_id = (isset($_POST['specials_id']) ? tep_db_prepare_input($_POST['specials_id']) : '');
		$products_price = (isset($_POST['products_price']) ? tep_db_prepare_input($_POST['products_price']) : '');
		$specials_price = (isset($_POST['specials_price']) ? tep_db_prepare_input($_POST['specials_price']) : '');

		if(tep_not_null($specials_id) && tep_not_null($products_price) && tep_not_null($specials_price)){
		
			if(substr($specials_price, -1) == '%') $specials_price = ($products_price - (($specials_price / 100) * $products_price));
			
			if(tep_db_query("update " . TABLE_SPECIALS . " set specials_new_products_price = '" . tep_db_input($specials_price) . "', specials_last_modified = now() where specials_id = '" . (int)$specials_id . "'")) echo $currencies->format($specials_price);
			else echo 'error';

		}else echo 'error';
		
	break;

	case'setNeverExpire':
	
		$specials_id = (isset($_POST['specials_id']) ? $_POST['specials_id'] : '');
		
		if(tep_not_null($specials_id)){
		
			if(tep_db_query("UPDATE ".TABLE_SPECIALS." SET expires_date=NULL, specials_last_modified=now() WHERE specials_id = '" . (int)$specials_id . "' ")) echo '1';
			else echo '0';

		}else echo '0';
		
	break;
	case'updateExpiresAt':
	
		$specials_id = (isset($_POST['specials_id']) ? $_POST['specials_id'] : '');
		$value = (isset($_POST['value']) ? $_POST['value'] : '');
		
		if(tep_not_null($specials_id) && tep_not_null($value)){
		
			$sql_data_array = array('expires_date' => tep_db_prepare_input($value),'specials_last_modified' => 'now()');
			if(tep_db_perform(TABLE_SPECIALS, $sql_data_array, 'update', "specials_id = '" . (int)$specials_id . "'")) echo '1';
			else echo '0';

		}else echo '0';
		
	break;
	case'changeStatus':
	
	$specials_id = (isset($_POST['specials_id']) ? $_POST['specials_id'] : '');
		$value = (isset($_POST['value']) ? $_POST['value'] : '');
		
		 if(tep_not_null($specials_id) && tep_not_null($value)){

				$sql_data_array = array('status' => (int)tep_db_prepare_input($value),'specials_last_modified' => 'now()');
   				if(tep_db_perform(TABLE_SPECIALS, $sql_data_array, 'update', "specials_id = '" . (int)$specials_id . "'")) echo $value;

		}else echo '2';
	
	break;
	
}

?>
