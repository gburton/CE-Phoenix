<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
  Information: attributes manager functions to be called via ajax
  
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

if(file_exists('ext/aas/languages/'.$language.'.php')) include 'ext/aas/languages/'.$language . '.php'; else include 'ext/aas/languages/english.php';

require('ext/aas/functions/general.php');

$item_post = (isset($_POST['item']) ? $_POST['item'] : '');
$value_post = (isset($_POST['value']) ? $_POST['value'] : '');
$values_post = (isset($_POST['values']) ? $_POST['values'] : '');
$language_alias_post = (isset($_POST['language_alias']) ? $_POST['language_alias'] : '');

switch($item_post){

	case'delete-product-option':

		if(tep_not_null($value_post)){

			$q=tep_db_query("SELECT products_attributes_id FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE options_id='".(int)$value_post."' ");
			if(tep_db_num_rows($q)>0){
		
				$paids=array();
				while($r=tep_db_fetch_array($q)) $paids[]=$r['products_attributes_id'];

				if(count($paids)>0) tep_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." WHERE products_attributes_id IN (".implode(',',$paids).") ");
			
			}

			tep_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE options_id='".(int)$value_post."' ");
			tep_db_query("DELETE FROM ".TABLE_PRODUCTS_OPTIONS." WHERE products_options_id='".(int)$value_post."' ");
			
			if(isset($_SESSION['admin']['AAS']['cache']['attributes_all'])) unset($_SESSION['admin']['AAS']['cache']['attributes_all']);
			
			echo '1';
		
		}

	break;

	case'delete_product_options':

		if(tep_not_null($value_post)){

			$products = tep_db_query("select p.products_id, pd.products_name, pov.products_options_values_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov, " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pov.language_id = '" . (int)$languages_id . "' and pd.language_id = '" . (int)$languages_id . "' and pa.products_id = p.products_id and pa.options_id='" . (int)$value_post . "' and pov.products_options_values_id = pa.options_values_id order by pd.products_name");
		
			if(tep_db_num_rows($products)>0){
		
			echo '<p class="parag">'.AAS_TEXT_AM_WARNING_OPTION_HAS_PRODUCTS_AND_VALUES.'</p>';
		
			echo '<table id="tbl-attributes-delete" class="tbl-attributes tablesorter">';
			echo '<thead><tr><th>'.AAS_TEXT_AM_PRODUCT_NAME.'</th><th>'.AAS_TEXT_AM_OPTION_VALUE.'</th></tr></thead>';
			while($prow=tep_db_fetch_array($products)){
		
				echo '<tr><td class="previewPage">'.$prow['products_name'].' <a target="_blank" class="view_product_class" title="'.AAS_VIEW_PRODUCTS_PAGE.'" href="'.tep_aas_link('front','product_info.php','products_id='.$prow['products_id']).'"><img src="ext/aas/images/glyphicons_152_new_window_op.png" alt=""></a></td><td>'.$prow['products_options_values_name'].'</td></tr>';
		
			}
			echo '</table>';
		
			if(isset($_SESSION['admin']['AAS']['cache']['attributes_all'])) unset($_SESSION['admin']['AAS']['cache']['attributes_all']);
		
			}else{
		
				echo '<p class="parag">'.AAS_TEXT_AM_WARNING_HAS_NO_PRODUCTS_AND_VALUES.'<p>';
		
			}
		
		}else echo '0';

	break;

	case'insert-products-options-names':

		if(tep_not_null($values_post) ){
	
			$values_array=json_decode(stripslashes($values_post));
			if(count($values_array)>0){
		
				$result = tep_db_query("select MAX(products_options_id) from ".TABLE_PRODUCTS_OPTIONS." ");
				$data = tep_db_fetch_array($result);
				$poid=$data['MAX(products_options_id)']+1;

				$insert_values=array();
				$empty=false;
				foreach($values_array as $value){
			
					if(!tep_not_null($value->value)){ $empty=true; break; }
					$insert_values[]="('".$poid."','".(int)$value->language_id."','".tep_db_input(tep_db_prepare_input($value->value))."')";

				}
			
				if($empty) echo '2';
				else{
					if(tep_db_query("INSERT INTO ".TABLE_PRODUCTS_OPTIONS." (products_options_id,language_id,products_options_name) VALUES ".implode(',',$insert_values)." ")){
						//echo '1';
						?>
					
						<tr id="optid_<?php echo $poid; ?>"><td><?php echo $poid; ?></td><td id="option_name_<?php echo $poid; ?>"><?php echo $values_array[0]->value; ?></td><td><button class="applyButton edit-option-name" id="poid_<?php echo $poid; ?>" style="padding:5px;"><?php echo AAS_TEXT_AM_EDIT; ?></button><button class="applyButton delete-option-name" id="doid_<?php echo $poid; ?>" style="padding:5px;"><?php echo AAS_TEXT_AM_DELETE; ?></button></td></tr>
					
						<?php
						
  					if(isset($_SESSION['admin']['AAS']['cache']['attributes_all'])) unset($_SESSION['admin']['AAS']['cache']['attributes_all']);
						
					}else echo '0';
				
				}
		
			}else echo '0';
		
		}else echo '0';

	break;

	case'save-product-options':

		if(tep_not_null($value_post) && tep_not_null($values_post) ){
	
			$values_array=json_decode(stripslashes($values_post));
			if(count($values_array)>0){

				foreach($values_array as $value){
		
					tep_db_query("update " . TABLE_PRODUCTS_OPTIONS . " set products_options_name='".tep_db_input(tep_db_prepare_input($value->value))."' where language_id='".(int)$value->language_id."' and products_options_id='".$value_post."'");

				}
				
  			if(isset($_SESSION['admin']['AAS']['cache']['attributes_all'])) unset($_SESSION['admin']['AAS']['cache']['attributes_all']);
  			
  			echo '1';
					
			}else echo '0';
		
		}else echo '0';


	break;

	case'get_product_options_name_insert':

	$languages = tep_get_languages();

		 echo '<div style="text-align:right" id="insert-product-options">';
		 foreach($languages as $lang){
		 
		 	echo '<label style="line-height:30px;">'.tep_image(DIR_WS_CATALOG_LANGUAGES . $lang['directory'] . '/images/' . $lang['image'], $lang['name']).' '.$lang['name'].' / '.$lang['code'].' : <input class="lfor input_lfor_extra" type="text" value="" id="lng_'.$lang['id'].'" ></label>';
		 	echo'<div class="clear" style="margin:10px 0;"></div>';
		 
		 }
		 echo '</div>';


	break;

	case'get_product_options':


	if(tep_not_null($value_post)){

		 $option_names_query = tep_db_query("select po.products_options_name, po.language_id, l.name,l.code,l.image,l.directory FROM " . TABLE_PRODUCTS_OPTIONS . " po, ".TABLE_LANGUAGES." l where po.products_options_id = '" . $value_post . "' and l.languages_id=po.language_id ");

		 echo '<div style="text-align:right" id="edit-product-options">';
		 while($option_names=tep_db_fetch_array($option_names_query)){
		 
		 	echo '<label style="line-height:30px;">'.tep_image(DIR_WS_CATALOG_LANGUAGES . $option_names['directory'] . '/images/' . $option_names['image'], $option_names['name']).' '.$option_names['name'].' / '.$option_names['code'].' : <input class="lfor input_lfor_extra" type="text" value="'.$option_names['products_options_name'].'" id="lng_'.$option_names['language_id'].'" ></label>';
		 	echo'<div class="clear" style="margin:10px 0;"></div>';
		 
		 }
		 echo '</div>';
	  
		}
	
		 

	break;

	case'save-product-options-values':

		$extra_value_post = (isset($_POST['extra_value']) ? $_POST['extra_value'] : '');

		if(tep_not_null($value_post) && tep_not_null($values_post) && tep_not_null($extra_value_post) ){

			$values_array=json_decode(stripslashes($values_post));
			if(count($values_array)>0){

				foreach($values_array as $value){
		
					tep_db_query("update " . TABLE_PRODUCTS_OPTIONS_VALUES . " set products_options_values_name='".tep_db_input(tep_db_prepare_input($value->value))."' where products_options_values_id = '" . tep_db_prepare_input($value_post) . "' AND language_id='".(int)$value->language_id."' ");

				}
			
				tep_db_query("update " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " set products_options_id = '" . tep_db_prepare_input($extra_value_post) . "'  where products_options_values_id = '" . tep_db_prepare_input($value_post) . "'");
				
				if(isset($_SESSION['admin']['AAS']['cache']['attributes_all'])) unset($_SESSION['admin']['AAS']['cache']['attributes_all']);
			
			echo '1';
		
			}else echo '0';
		
		}else echo '0';


	break;

	case'insert-products-options-values':

		$poid = (isset($_POST['poid']) ? $_POST['poid'] : '');
		$poname = (isset($_POST['poname']) ? $_POST['poname'] : '');

		if(tep_not_null($values_post) && tep_not_null($poid) && tep_not_null($poname) ){
	
			$values_array=json_decode(stripslashes($values_post));
			if(count($values_array)>0){
		
				$result = tep_db_query("select max(products_options_values_id) + 1 as next_id from ".TABLE_PRODUCTS_OPTIONS_VALUES." ");
				$data = tep_db_fetch_array($result);
				$povid=$data['next_id'];

				$insert_values=array();
				$empty=false;
				foreach($values_array as $value){
			
					if(!tep_not_null($value->value)){ $empty=true; break; }
					$insert_values[]="('".$povid."','".(int)$value->language_id."','".tep_db_input(tep_db_prepare_input($value->value))."')";

				}
			
				if($empty) echo '2';
				else{
					if(tep_db_query("INSERT INTO ".TABLE_PRODUCTS_OPTIONS_VALUES." (products_options_values_id,language_id,products_options_values_name) VALUES ".implode(',',$insert_values)." ")){

						 tep_db_query("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " (products_options_id, products_options_values_id) values ('" . (int)$poid . "', '" . (int)$povid . "')");
						?>
					
						<tr id="tr_povid_<?php echo $povid; ?>">
								<td><?php echo $povid; ?></td>
								<td class="v_poid" data-id="v_poid_<?php echo $poid; ?>"><?php echo stripslashes($poname); ?></td>
								<td id="option_value_<?php echo $povid; ?>"><?php echo $values_array[0]->value; ?></td>
								<td><button class="applyButton edit-option-value" id="v_povid_<?php echo $povid; ?>" style="padding:5px;"><?php echo AAS_TEXT_AM_EDIT; ?></button><button class="applyButton delete-option-value" id="v_dovid_<?php echo $povid; ?>" style="padding:5px;"><?php echo AAS_TEXT_AM_DELETE; ?></button></td>
						</tr>
					
						<?php
						
						if(isset($_SESSION['admin']['AAS']['cache']['attributes_all'])) unset($_SESSION['admin']['AAS']['cache']['attributes_all']);
									
					}else echo '0';
				
				}
		
			}else echo '0';
		
		}else echo '0';

	break;

	case'get_product_options_value_insert':

	$languages = tep_get_languages();

	   echo '<div style="text-align:right" id="insert-product-options-values"><label style="line-height:30px;">'.AAS_TEXT_AM_OPTION_NAME.' : </label><select id="product_option_select_value" name="option_id">';
	      	$options = tep_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . (int)$languages_id . "' order by products_options_name");
	   
		while ($options_values = tep_db_fetch_array($options)) echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '"  >'.$options_values['products_options_name'].'</option>'; 
		echo'</select><div class="clear" style="margin:10px 0;"></div>';
		 foreach($languages as $lang){
		 
		 	echo '<label style="line-height:30px;">'.tep_image(DIR_WS_CATALOG_LANGUAGES . $lang['directory'] . '/images/' . $lang['image'], $lang['name']).' '.$lang['name'].' / '.$lang['code'].' : <input class="lfor" style="display:inline-block;vertical-align:top;border:1px solid #B6B7CB;padding:3px;" type="text" value="" id="lng_'.$lang['id'].'" ></label>';
		 	echo'<div class="clear" style="margin:10px 0;"></div>';
		 
		 }
		 echo '</div>';


	break;


	case'get_product_options_values':

		$extra_value_post = (isset($_POST['extra_value']) ? $_POST['extra_value'] : '');

		if(tep_not_null($value_post) && tep_not_null($extra_value_post) ){

			echo '<div style="text-align:right" id="edit-product-options-values"><label style="line-height:30px;">'.AAS_TEXT_AM_OPTION_NAME.' : </label><select id="product_option_select" name="option_id">';
			$options = tep_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . (int)$languages_id . "' order by products_options_name");
			while ($options_values = tep_db_fetch_array($options)) 
				echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '" '.($extra_value_post == $options_values['products_options_id']? 'selected="selected"':'').' >'.$options_values['products_options_name'].'</option>'; 
			echo'</select><div class="clear" style="margin:10px 0;"></div>';
	       
			$option_values_names_query = tep_db_query("select pov.products_options_values_name,pov.language_id, l.name,l.code,l.image,l.directory from " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov, ".TABLE_LANGUAGES." l  where pov.products_options_values_id = '" . (int)$value_post . "' and l.languages_id=pov.language_id ");
			  
			 while($option_values_names=tep_db_fetch_array($option_values_names_query)){
			 
			 	echo '<label style="line-height:30px;">'.tep_image(DIR_WS_CATALOG_LANGUAGES . $option_values_names['directory'] . '/images/' . $option_values_names['image'], $option_values_names['name']).' '.$option_values_names['name'].' / '.$option_values_names['code'].' : <input class="lfor input_lfor_extra" type="text" value="'.$option_values_names['products_options_values_name'].'" id="lng_'.$option_values_names['language_id'].'" ></label>';
			 	echo'<div class="clear" style="margin:10px 0;"></div>';
			 
			 }
			 echo '</div>';


		}
	
	break;

	case'delete-product-option-value':

		if(tep_not_null($value_post)){
	
		  $value_id = tep_db_prepare_input($value_post);
	
			$q=tep_db_query("SELECT products_attributes_id FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE options_values_id='".(int)$value_id."' ");
			if(tep_db_num_rows($q)>0){
		
				$paids=array();
				while($r=tep_db_fetch_array($q)) $paids[]=$r['products_attributes_id'];

				if(count($paids)>0) tep_db_query("DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." WHERE products_attributes_id IN (".implode(',',$paids).") ");
			
			}

			tep_db_query("delete from ".TABLE_PRODUCTS_ATTRIBUTES." WHERE options_values_id='".(int)$value_id."' ");
			tep_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$value_id . "'");
			tep_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_values_id = '" . (int)$value_id . "'");
      
      if(isset($_SESSION['admin']['AAS']['cache']['attributes_all'])) unset($_SESSION['admin']['AAS']['cache']['attributes_all']);
			
			echo '1';
		
		}

	break;

	case'delete_product_values':

		if(tep_not_null($value_post)){
	
	
		   	 $values = tep_db_query("select products_options_values_id, products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$value_post . "' and language_id = '" . (int)$languages_id . "'");
	   		 $values_values = tep_db_fetch_array($values);
		
			$products = tep_db_query("select p.products_id, pd.products_name, po.products_options_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS . " po, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "' and po.language_id = '" . (int)$languages_id . "' and pa.products_id = p.products_id and pa.options_values_id='" . (int)$value_post . "' and po.products_options_id = pa.options_id order by pd.products_name");
		
			if(tep_db_num_rows($products)>0){
		
				echo '<p class="parag">'.AAS_TEXT_AM_WARNING_OPTION_VALUE_HAS_PRODUCTS_AND_VALUES.'</p>';
		
				echo '<table id="tbl-attributes-delete" class="tbl-attributes tablesorter">';
				echo '<thead><tr><th>'.AAS_TEXT_AM_PRODUCT_NAME.'</th><th>'.AAS_TEXT_AM_OPTION_NAME.'</th></tr></thead>';
				while($prow=tep_db_fetch_array($products)) echo '<tr><td class="previewPage">'.$prow['products_name'].' <a target="_blank" class="view_product_class" title="'.AAS_VIEW_PRODUCTS_PAGE.'" href="'.tep_aas_link('front','product_info.php','products_id='.$prow['products_id']).'"><img src="ext/aas/images/glyphicons_152_new_window_op.png" alt=""></a></td><td>'.$prow['products_options_name'].'</td></tr>';
				
				//echo '<tr><td>'.$prow['products_name'].'</td><td>'.$prow['products_options_name'],'</td></tr>';
				
				echo '</table>';
		
			}else{
		
				echo '<p class="parag">'.AAS_TEXT_AM_WARNING_OPTION_VALUE_SAFE_TO_DELETE.'<p>';
		
			}
		
		}else echo '0';

	break;
	default:
	$options_values_query = tep_db_query("select po.*  from " . TABLE_PRODUCTS_OPTIONS . " po where po.language_id = '" . (int)$languages_id . "' order by po.products_options_id");
  $values = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pov2po.products_options_id from " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov left join " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po on pov.products_options_values_id = pov2po.products_options_values_id where pov.language_id = '" . (int)$languages_id . "' order by pov.products_options_values_id");
	?>	
		<ul>
			<li><a class="active" data-rel="tab1" href="#"><?php echo AAS_TEXT_AM_PRODUCTS_OPTIONS; ?></a></li>
			<li><a data-rel="tab2" href="#"><?php echo AAS_TEXT_AM_PRODUCTS_OPTIONS_VALUES; ?></a></li>
		</ul>
		
		<div class="clear"></div>
		
		<div class="tabDetails">
			<div id="tab1" class="tabContents">
			  <div>
						
				  <table id="tbl-attributes-options" class="tbl-attributes tablesorter">
				  <thead>
				  <tr><th style="width:50px;"><?php echo AAS_TEXT_AM_ID; ?></th><th><?php echo AAS_TEXT_AM_OPTION_NAME; ?></th><th style="width:150px;"><?php echo AAS_TEXT_AM_ACTION; ?></th></tr>
				  </thead>
				  <tbody>
				
				  <?php while($options_values=tep_db_fetch_array($options_values_query)){ ?>
				  <tr id="optid_<?php echo $options_values['products_options_id']; ?>"><td><?php echo $options_values['products_options_id']; ?></td><td id="option_name_<?php echo $options_values['products_options_id']; ?>"><?php echo $options_values['products_options_name']; ?></td><td><button class="applyButton edit-option-name" id="poid_<?php echo $options_values['products_options_id']; ?>" style="padding:5px;"><?php echo AAS_TEXT_AM_EDIT; ?></button><button class="applyButton delete-option-name" id="doid_<?php echo $options_values['products_options_id']; ?>" style="padding:5px;"><?php echo AAS_TEXT_AM_DELETE; ?></button></td></tr>
				  <?php } ?>
				
				  </tbody>
				  </table>
			  </div>	
			</div>
			
			<div id="tab2" class="tabContents">
			  <div>
			  
				  <table id="tbl-attributes-options-values" class="tbl-attributes tablesorter">
				  <thead>
				  <tr><th style="width:50px;"><?php echo AAS_TEXT_AM_ID; ?></th><th><?php echo AAS_TEXT_AM_OPTION_NAME; ?></th><th><?php echo AAS_TEXT_AM_OPTION_VALUE; ?></th><th style="width:150px;"><?php echo AAS_TEXT_AM_ACTION; ?></th></tr>
				  </thead>
				  <tbody>
				
				  <?php while($values_values = tep_db_fetch_array($values)){
					  $options_name = tep_options_name($values_values['products_options_id']);
					  $values_name = $values_values['products_options_values_name']; ?>
				  <tr id="tr_povid_<?php echo $values_values['products_options_values_id']; ?>">
					  <td><?php echo $values_values['products_options_values_id']; ?></td>
					  <td class="v_poid" data-id="v_poid_<?php echo $values_values['products_options_id']; ?>"><?php echo $options_name; ?></td>
					  <td id="option_value_<?php echo $values_values['products_options_values_id']; ?>"><?php echo $values_name; ?></td>
					  <td><button class="applyButton edit-option-value" id="v_povid_<?php echo $values_values['products_options_values_id']; ?>" style="padding:5px;"><?php echo AAS_TEXT_AM_EDIT; ?></button><button class="applyButton delete-option-value" id="v_dovid_<?php echo $values_values['products_options_values_id']; ?>" style="padding:5px;"><?php echo AAS_TEXT_AM_DELETE; ?></button></td>
				  </tr>
				  <?php } ?>

				  </tbody>				
				  </table>
			  </div>
			</div>
								
		</div>
	
	<?php

}

?>
