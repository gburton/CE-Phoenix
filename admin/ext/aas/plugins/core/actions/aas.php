<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
*/

chdir('../../../../../');
require('includes/application_top.php');
//sleep(3);
//echo getcwd();

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

$item_post = (isset($_POST['item']) ? $_POST['item'] : '');
$product_id_post = (isset($_POST['product_id']) ? $_POST['product_id'] : '');
$language_alias_post = (isset($_POST['language_alias']) ? $_POST['language_alias'] : '');

switch($item_post){

  case'addLinkedColumn':

    if(tep_db_query("ALTER TABLE  `products_to_categories` ADD  `linked` TINYINT( 1 ) NOT NULL DEFAULT  '0'")) echo '1';
    else echo '0';

  break;
  case'resetColumnsOrder':
  
    tep_db_query("DELETE FROM aas_settings WHERE skey='columns_sorted' AND type='".$_SESSION['admin']['id']."'");
    if(tep_db_affected_rows()){
    
      if(isset($_SESSION['admin']['AAS']['sorted_fields_array'])) unset($_SESSION['admin']['AAS']['sorted_fields_array']);
      echo '1';
    
    }else echo '0';
  
  break;
  case'reOrderColumns':
    
    $reOrderColumns = (isset($_POST['reOrderColumns']) ? $_POST['reOrderColumns'] : '');
    if(tep_not_null($reOrderColumns)){
    
      if(isset($_SESSION['admin']['AAS']['sorted_fields_array'])){//if we had previously reorder fields
      
        $jexp=explode(',',$_SESSION['admin']['AAS']['sorted_fields_array']);
        
      }else	if(isset($_SESSION['admin']['AAS']['tbl_fields'])){

		    $jexp=array();
		    foreach($_SESSION['admin']['AAS']['tbl_fields'] as $key => $val) $jexp[]=$key;

	    }else{//$_SESSION['admin']['AAS']['fieldsArray'] is always set by application_top.php
	    
        $jexp=array();
        foreach($_SESSION['admin']['AAS']['fieldsArray'] as $key => $val) $jexp[]=$key;
      
      }
  
      //BELLOW ALGORITHM NEEDS IMPROVEMENT
      
      $columns=array();
      $cnt=0;
      foreach($jexp as $value){
      
        if(in_array($value,$reOrderColumns)) $columns[]=$cnt++; else $columns[]=$value;

      }
    
      $finArray=array();
      foreach($columns as $col){
      
       if(is_int($col)) $finArray[]=$reOrderColumns[$col]; else $finArray[]=$col;
      
      }

      //sortable_fields_array is responsible for columns reordering
      $_SESSION['admin']['AAS']['sorted_fields_array']=implode(',',$finArray);
      
      //save sorted columns per admin
      tep_db_query("INSERT INTO aas_settings (skey,type,value) VALUES('columns_sorted','".$_SESSION['admin']['id']."','".$_SESSION['admin']['AAS']['sorted_fields_array']."') ON DUPLICATE KEY UPDATE value='".$_SESSION['admin']['AAS']['sorted_fields_array']."' ");
  
    }else echo '0';

  break;

  case'massColumnEdit':
  
    $pids = isset($_POST['pids']) ? $_POST['pids'] : '';
    $column = isset($_POST['column']) ? tep_db_prepare_input($_POST['column']) : '';
    $value = isset($_POST['value']) ? tep_db_prepare_input($_POST['value']) : '';
    $option = isset($_POST['option']) ? tep_db_prepare_input($_POST['option']) : '';
    
    $sel_list = isset($_POST['sel_list']) ? tep_db_prepare_input($_POST['sel_list']) : '';
    $sel_cat = isset($_POST['sel_cat']) ? tep_db_prepare_input($_POST['sel_cat']) : '';
    $sel_rec = isset($_POST['sel_rec']) ? tep_db_prepare_input($_POST['sel_rec']) : '';
    $sel_sta = isset($_POST['sel_sta']) ? tep_db_prepare_input($_POST['sel_sta']) : '';
    
    if($sel_list=='3'){
    
      $statusWhere='';
      
      if(tep_not_null($sel_sta)){
      
        if($sel_sta!='2' && ($sel_sta=='1' || $sel_sta=='0') ) $statusWhere=' and p.products_status='.$sel_sta;
      
      
      }else{ echo '0'; die; }
    
      if(!tep_not_null($sel_cat) || !tep_not_null($sel_rec) ) die('0');
    
		  if($sel_rec=='1'){//recursively
			
				$pids_query = tep_db_query("select ptc.products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc, ".TABLE_PRODUCTS." p where ptc.categories_id IN (".implode(',',tep_aas_get_category_ids($sel_cat)).") and ptc.products_id=p.products_id ".$statusWhere." order by ptc.products_id");
			
			}else{//non recursively
			
				$pids_query = tep_db_query("select ptc.products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc, ".TABLE_PRODUCTS." p where ptc.categories_id ='".$sel_cat."' and ptc.products_id=p.products_id ".$statusWhere." order by ptc.products_id");
			
			}
			
			$pids=array();
			while ($pid = tep_db_fetch_array($pids_query)) $pids[]=$pid['products_id'];
     
    }
    
    if(tep_not_null($pids) && tep_not_null($column)){
          
      switch($column){
        case'products_weight':
        case'products_quantity':
      
        if((tep_not_null($value) || $value=='0' ) && tep_not_null($option)){
          
          if($option=='='){
          
            tep_db_query("UPDATE products SET ".$column."='".$value."', products_last_modified=now() WHERE products_id IN (".implode(',',$pids).") ");
          
          }else{
          
            foreach($pids as $pid){
            
              tep_db_query("UPDATE products SET ".$column."=".$column."".$option."'".$value."', products_last_modified=now() WHERE products_id='".$pid."' ");
            
            }
          
          }
          
          echo '1-'.implode(',',$pids);
        
        }else echo '0';
        
      break;
      
      case'products_status':

        if(tep_not_null($value)){
          echo (tep_db_query("update " . TABLE_PRODUCTS . " set products_status=".(int)$value.", products_last_modified=now() where products_id IN (".implode(',',$pids).") ")) ? '1-'.implode(',',$pids) : '0';
        }else echo '0';
      
      break;
      
      case'manufacturers_id':
  
        if(tep_not_null($value)){
          echo (tep_db_query("update " . TABLE_PRODUCTS . " set ".$column."=".$value.", products_last_modified=now() where products_id IN (".implode(',',$pids).") ")) ? '1-'.implode(',',$pids) : '0';
        }else echo '0';
      
      break;
      
      case'products_tax_class_id':
      
        if(tep_not_null($value)){
          $exp=explode('_',$value);
          echo (tep_db_query("update " . TABLE_PRODUCTS . " set ".$column."=".$exp[0].", products_last_modified=now() where products_id IN (".implode(',',$pids).") ")) ? '1-'.implode(',',$pids) : '0';
        }else echo '0';
      
      break;
      
      case'products_date_added':
  
        if(tep_not_null($value)){
          echo (tep_db_query("update " . TABLE_PRODUCTS . " set ".$column."='".$value."', products_last_modified=now() where products_id IN (".implode(',',$pids).") ")) ? '1-'.implode(',',$pids) : '0';
        }else echo '0';
      
      break;

      case'products_date_available':
  
        if(tep_not_null($value)) echo (tep_db_query("update " . TABLE_PRODUCTS . " set ".$column."='".$value."', products_last_modified=now() where products_id IN (".implode(',',$pids).") ")) ? '1-'.implode(',',$pids) : '0';
        else echo (tep_db_query("update " . TABLE_PRODUCTS . " set ".$column."=NULL, products_last_modified=now() where products_id IN (".implode(',',$pids).") ")) ? '1-'.implode(',',$pids) : '0';
      
      break;
      
      case'products_price':
         
        //if( (!tep_not_null($value) && $value=='0' && tep_not_null($option) ) || (tep_not_null($value) && tep_not_null($option))){
        if(tep_not_null($value) && tep_not_null($option)){
        
          if($sel_list=='1'){
        
            $values=json_decode(stripslashes($value));
            
            $pids=array();
            foreach($values as $key => $val){
						    
					    $sql_data_array = array('products_price' => (float)tep_db_prepare_input($val),'products_last_modified' => 'now()');
     					tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$key . "'");
     					$pids[]=$key;
        				
        	  }
        	  
        	  echo '1-'.implode(',',$pids);
          
        }else{
        
          $st='';
          //calculate prices based on option
          switch($option){
          
            case'-%': $st="products_price-(products_price*".$value."/100)"; break;
            case'+%': $st="products_price+(products_price*".$value."/100)"; break;
            case'-': $st="products_price-".$value.""; break;
            case'+': $st="products_price+".$value.""; break;
            case'=': $st=$value; break;
          
          }
          if($st==''){echo '0'; die;}
          
          echo tep_db_query("update " . TABLE_PRODUCTS . " set ".$column."=".$st.", products_last_modified=now() where products_id IN (".implode(',',$pids).") ") ? '1-'.implode(',',$pids) : '0';
          
        }
        
      }else echo'0';
        
      break;
      
      case'products_price_gross':
  
        if(tep_not_null($value) && tep_not_null($option)){
          
          if($sel_list=='1'){
        
            $values=json_decode(stripslashes($value));
            
            $pids=array();
            foreach($values as $key => $val){
              
              $fin=$val->price/(($val->taxrate/100)+1);
              $fin=tep_round($fin, 4);

					    $sql_data_array = array('products_price' => (float)tep_db_prepare_input($fin),'products_last_modified' => 'now()');
     					tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$key . "'");
     					$pids[]=$key;
        				
        	  }
        	  
        	  echo '1-'.implode(',',$pids);
          
          }else{
          
            //$st=$val->price."/()products_price-(products_price*".$value."/100)";
            
            	/*
            	//store in SESSION tax classes results in order to avoid continuously call db query
            	$tax_class_array=array();
	            if(!isset($_SESSION['admin']['AAS']['cache_tax_class_array'])){
	
		            $tax_class_query=tep_db_query("SELECT tc.tax_class_id, tc.tax_class_title, tr.tax_rate FROM tax_class tc, tax_rates tr WHERE tc.tax_class_id=tr.tax_class_id");
		            if(tep_db_num_rows($tax_class_query)>0){

			            while($tax_class_row=tep_db_fetch_array($tax_class_query)) $tax_class_array[]=array('id'=>$tax_class_row['tax_class_id'],'title'=>$tax_class_row['tax_class_title'],'tax_rate'=>tep_display_tax_value($tax_class_row['tax_rate']));
			            $_SESSION['admin']['AAS']['cache_tax_class_array']=$tax_class_array;
		
		            }
		
	            }else $tax_class_array=$_SESSION['admin']['AAS']['cache_tax_class_array'];
	            
            var_dump($tax_class_array);
            */
            
              $country_id = STORE_COUNTRY;
              $zone_id = STORE_ZONE;
              
              $txrQuery=tep_db_query("select tr.tax_class_id, SUM(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za ON tr.tax_zone_id = za.geo_zone_id left join " . TABLE_GEO_ZONES . " tz ON tz.geo_zone_id = tr.tax_zone_id WHERE (za.zone_country_id IS NULL OR za.zone_country_id = '0' OR za.zone_country_id = '" . (int)$country_id . "') AND (za.zone_id IS NULL OR za.zone_id = '0' OR za.zone_id = '" . (int)$zone_id . "') GROUP BY tr.tax_class_id, tr.tax_priority");

              $txrArray=array();
              while($row=tep_db_fetch_array($txrQuery)) $txrArray[$row['tax_class_id']]=$row; //this can be cached

              $repsArray=array();

              $pids_query=tep_db_query("SELECT products_id,products_price,products_tax_class_id FROM ".TABLE_PRODUCTS." WHERE products_id IN (".implode(',',$pids).") ");
              while($products=tep_db_fetch_array($pids_query)){
              
                if(isset($txrArray[$products['products_tax_class_id']])){

                  //calculate - get current gross price                  
                  $grossValue = $products['products_price'];
                  if($txrArray[$products['products_tax_class_id']] > 0){
                  
                    $grossValue = $products['products_price'] * (($txrArray[$products['products_tax_class_id']]['tax_rate'] / 100) + 1);
                  
                  }
                  
                  //calculate prices based on option
                  $st=0;
                  switch($option){
                  
                    case'-%': $st=$grossValue-($grossValue*$value/100); break;
                    case'+%': $st=$grossValue+($grossValue*$value/100);; break;
                    case'-': $st=$grossValue-$value; break;
                    case'+': $st=$grossValue+$value; break;
                    case'=': $st=(float)$value; break;
                  
                  }
                  
                  //now we have the new grossvalue we can calculate the net price
                  if($st==0) $fin=0;
                  else $fin=$st/(($txrArray[$products['products_tax_class_id']]['tax_rate']/100)+1);
                  
                 // var_dump($fin);
                  
                  //$fin is the net price to be updated in db 
                  tep_db_query("update " . TABLE_PRODUCTS . " set products_price='".(float)$fin."', products_last_modified=now() where products_id= '".(int)$products['products_id']."' ");
                  
                  $repsArray[$products['products_id']]=array('net'=>$fin,'gross'=>$st);
                
                }
              
              }
              
              echo json_encode($repsArray);
          
          }
                  
        }else echo '0';
      
      break;
      
    }
      
    
   }else echo '0';
    
  break;

	case'reorderProducts':

		$idsArrayObj = isset($_POST['idsArrayObj']) ? tep_db_prepare_input($_POST['idsArrayObj']) : '';

		if(tep_not_null($idsArrayObj)){

			$pids=array();
		
			foreach($idsArrayObj as $key => $e ){
		
				$john=explode('_',$e);
				
				tep_db_query("UPDATE products SET products_sort_order='".$key."', products_last_modified=now() WHERE products_id=".$john[1]." ");
						
			}
			
		echo '1';
	
		}else echo '0';

	break;

	case'reorderCategories':
	
		$cats = isset($_POST['cats']) ? tep_db_prepare_input($_POST['cats']) : '';
	
		if(tep_not_null($cats)){
	
			$exp=explode(',',$cats);
			$cids=array();
		
			foreach($exp as $key => $e ){
		
				$john=explode('_',$e);
				$cids[]=$john[1];
		
			}
			
			foreach($cids as $key => $cid){
				tep_db_query("UPDATE categories SET sort_order='".($key+1)."', last_modified=now() WHERE categories_id=".$cid." ");
			}
	
			echo '1';
	
		}else echo '0';

	break;

	case'saveDcd':

		$dcd = (isset($_POST['dcd']) ? tep_db_prepare_input($_POST['dcd']) : '');
		$type = isset($_POST['type']) && tep_not_null($_POST['type']) ? tep_db_prepare_input($_POST['type']) : 'default-dcd';
		if(tep_not_null($dcd)){

		  if($type=='default-dcd'){
		
		    $aasDcd_query = tep_db_query("select * from aas_settings WHERE type='dcd' AND skey='default_columns_display' LIMIT 1");
		    if(tep_db_num_rows($aasDcd_query)>0) tep_db_query("UPDATE aas_settings SET value='".serialize($dcd)."' WHERE skey='default_columns_display' AND type='dcd' ");
		    else tep_db_query("INSERT INTO aas_settings (sgroup,skey,type,value) VALUES('dcd','default_columns_display','dcd','".serialize($dcd)."')");
			  
			  if(isset($_SESSION['admin']['AAS']['tbl_fields'])) unset($_SESSION['admin']['AAS']['tbl_fields']);
			  
			  echo json_encode('1');
		
		  }elseif($type=='admins_columns_display'){
		    
		    $aasDcd_query = tep_db_query("select * from aas_settings WHERE type='dcd' AND skey='default_columns_display_per_admin' LIMIT 1");
		    if(tep_db_num_rows($aasDcd_query)>0) tep_db_query("UPDATE aas_settings SET value='".serialize($dcd)."' WHERE skey='default_columns_display_per_admin' AND type='dcd' ");
		    else tep_db_query("INSERT INTO aas_settings (sgroup,skey,type,value) VALUES('dcd','default_columns_display_per_admin','dcd','".serialize($dcd)."')");
		  
		    if(isset($_SESSION['admin']['AAS']['tbl_fields'])) unset($_SESSION['admin']['AAS']['tbl_fields']);
		   
		    echo json_encode('1');
		  
		  }else echo json_encode('0');
		
		}else echo json_encode('0');
		
	break;

	case'getAac':

			$admins_query = tep_db_query("select id, user_name from " . TABLE_ADMINISTRATORS . " order by id ASC");
			$admins=array();
			while ($admins_row = tep_db_fetch_array($admins_query)) $admins[$admins_row['id']]=$admins_row['user_name'];
			
			$type = isset($_POST['type']) && tep_not_null($_POST['type']) ? tep_db_prepare_input($_POST['type']) : 'default';
			
			include 'ext/aas/config.php';
			
			if($type=='default-dcd'){
						
			  $aasDcd=array();
	      $aasDcd_query = tep_db_query("select * from aas_settings WHERE type='dcd' AND skey='default_columns_display' AND sgroup='dcd' LIMIT 1");
	      $aasDcd_row = tep_db_fetch_array($aasDcd_query);

	      $aasDcd=unserialize($aasDcd_row['value']);

	      $counter=0;
	      foreach($fieldsArray as $key => $column){
	      			
			      echo '<tr data-column="'.$key.'" class="'.(($counter++ & 1)? 'odd' : 'even').'"><td>'.$counter.'</td>';
			      echo '<td class="alignLeft">'.$column['theadText'].'</td>';
			      echo '<td><label><input type="checkbox" name="default_columns_display" value="'.$key.'" '.(isset($aasDcd[$key]) && $aasDcd[$key]['v']=='1'  ? 'checked="checked"' : '').' ></label></td>';
			      echo '<td><label><input type="checkbox" name="default_columns_display_lock" value="'.$key.'" '.(isset($aasDcd[$key]) && $aasDcd[$key]['l']=='1' ? 'checked="checked"' : '').' ></label></td>';
  					echo '</tr>';
	
	      }
			
			}elseif($type=='admins_columns_display'){
			
				$aasDcd=array();
	      $aasDcd_query = tep_db_query("select * from aas_settings WHERE type='dcd' AND skey='default_columns_display_per_admin' LIMIT 1");
	      $aasDcd_row = tep_db_fetch_array($aasDcd_query);

	      $aasDcd=unserialize($aasDcd_row['value']);
	      
	    ?>
				<div id="dcd-accordion">
 					
  					<?php foreach($admins as $ak => $av){ ?>
                <h3><?php echo $av; ?></h3>
                <div>
                <input type="hidden" name="dcd_hidden_adminId" value="<?php echo $ak; ?>">
          				<table class="tablesorter tbl-general">
						      <thead>
							      <tr>
								      <th><?php echo AAS_DIALOG_UPLOAD_MODULE_DIESI; ?></th>
								      <th><?php echo AAS_TEXT_COLUMN; ?></th>
								      <th><?php echo AAS_TEXT_VISIBLE; ?></th>
								      <th><?php echo AAS_TEXT_LOCK_VISIBILITY; ?></th>
								      <th><?php echo AAS_TEXT_OVERRIDE_DEFAULTS; ?></th>
							      </tr>
						      </thead>
						      <tbody>
						      <?php
						      	
						      	$counter=0;
	                  foreach($fieldsArray as $key => $column){
						          echo '<tr data-adminid="'.$ak.'" data-column="'.$key.'" class="'.(($counter++ & 1)? 'odd' : 'even').'"><td>'.$counter.'</td>';
			                echo '<td class="alignLeft">'.$column['theadText'].'</td>';
			                echo '<td><label><input type="checkbox" name="default_columns_display" value="'.$key.'" '.(isset($aasDcd[$ak][$key]['v']) && $aasDcd[$ak][$key]['v']=='1'  ? 'checked="checked"' : '').' ></label></td>';
			                echo '<td><label><input type="checkbox" name="default_columns_display_lock" value="'.$key.'" '.(isset($aasDcd[$ak][$key]['l']) && $aasDcd[$ak][$key]['l']=='1' ? 'checked="checked"' : '').' ></label></td>';			                
			                echo '<td><label><input type="checkbox" name="default_columns_display_overide" value="'.$key.'" '.(isset($aasDcd[$ak][$key]['o']) && $aasDcd[$ak][$key]['o']=='1' ? 'checked="checked"' : '').' ></label></td>';
			                echo '</tr>';
			              }
			            
					        ?>
						      </tbody>
						      </table>
                </div>
  					<?php } ?>
      </div>
	    <script>
	    $(function(){
	
        $( "#dcd-accordion" ).accordion({
          heightStyle: "content",
          collapsible: true,
          active: false
        });
	
	    });
	    </script>
      <?php
			
			}elseif($type=='fields_disable_action'){
			
			
  			$aasAac_query = tep_db_query("select value from aas_settings WHERE type='".$type."' AND skey='".$type."' AND sgroup='aac' order by id ASC limit 1 ");
		    $aasAac = tep_db_fetch_array($aasAac_query);

	      $fdaa=unserialize($aasAac['value']);
		    
	      $counter=0;
	      foreach($_SESSION['admin']['AAS']['fieldsArray'] as $faKey => $faValue){
	      
	        echo '<tr class="'.((++$counter & 1)? 'odd' : 'even').'"><td>'.$counter.'</td>';
			    echo '<td class="alignLeft">'.$faValue['theadText'].'</td>';
			    echo '<td><ul data-key="'.$faKey.'" class="aac-adminsList alignRight">';
			    foreach($admins as $ak => $av)	echo '<li><label>'.$av.'<input type="checkbox" name="'.$faKey.'" value="'.$ak.'" '.(isset($fdaa[$faKey][$ak]) && $fdaa[$faKey][$ak] ?'checked="checked"':'').' ></label></li>';
			    echo '</ul></td></tr>';
	      
	      }
		      
			
			}else{
			
		    $aasAac_query = tep_db_query("select * from aas_settings WHERE type='".$type."' AND sgroup='aac' order by id ASC");
		    $aasAac=array();
		    while ($aasAac_row = tep_db_fetch_array($aasAac_query)) $aasAac[]=$aasAac_row;

		    if(count($aasAac)>0){
			    $counter=0;
			    foreach($aasAac as $aacKey => $aacValue){

				    $aacExpAdmins=explode(',',$aacValue['value']);
				
				    if($type=='modules'){
				
					    if(file_exists('ext/aas_modules/'.$aacValue['skey'].'/languages/english.php')) include 'ext/aas_modules/'.$aacValue['skey'].'/languages/english.php';
				
				    }

				    echo '<tr class="'.((++$counter & 1)? 'odd' : 'even').'"><td>'.($aacKey+1).'</td>';
				    echo '<td class="alignLeft">'.constant($aacValue['description']).'</td>';
				    echo '<td><ul data-key="'.$aacValue['skey'].'" class="aac-adminsList alignRight">';
				    foreach($admins as $ak => $av)	echo '<li><label>'.$av.'<input type="checkbox" name="'.$aacValue['skey'].'" value="'.$ak.'" '.(in_array($ak,$aacExpAdmins) ?'checked="checked"':'').' ></label></li>';
				    echo '</ul></td></tr>';
			
			    }
		
		    }
			
			}

	break;
	
	case'saveAac':

		$aac = (isset($_POST['aac']) ? tep_db_prepare_input($_POST['aac']) : '');
		$type = isset($_POST['type']) && tep_not_null($_POST['type']) ? tep_db_prepare_input($_POST['type']) : 'default';
		if(tep_not_null($aac)){
		
		  if($type=='fields_disable_action'){
		  
		    $aacs=serialize($aac);
		    
		    tep_db_query("INSERT INTO aas_settings (value,skey,type) VALUES ('".tep_db_prepare_input($aacs)."','".tep_db_prepare_input($type)."','".tep_db_prepare_input($type)."') ON DUPLICATE KEY UPDATE value='".tep_db_prepare_input($aacs)."' ");
		  		  
		  }else{
		

			  foreach($aac as $aacK => $aacV){

				  $aacTmp=array();
				  foreach($aacV as $aacKK => $aacVV){
				
					  if($aacVV=='1') $aacTmp[]=$aacKK;
				
				  }
				
				  tep_db_query("UPDATE aas_settings SET value='".implode(',',$aacTmp)."' WHERE skey='".$aacK."' AND type='".$type."' AND sgroup='aac' ");
			
			  }
			
			}
			
			if(isset($_SESSION['admin']['AAS']['AAC'])) unset($_SESSION['admin']['AAS']['AAC']);
			
			if($type=='fields_display' && isset($_SESSION['admin']['AAS']['tbl_fields'])) unset($_SESSION['admin']['AAS']['tbl_fields']);
			
	  }else echo '0';
	
	break;

	case'uploadModule':

    $module_file = new upload('module_file');
   
    if(!file_exists(DIR_FS_ADMIN.'ext/aas_modules/tmp')) mkdir(DIR_FS_ADMIN.'ext/aas_modules/tmp',0755,true);
    if(!is_writable(DIR_FS_ADMIN.'ext/aas_modules/tmp')) chmod(DIR_FS_ADMIN.'ext/aas_modules/tmp', 0755);
   
    //store incoming zip to tmp
   
    $module_file->set_destination(DIR_FS_ADMIN.'ext/aas_modules/tmp/');

    if($module_file->parse() && $module_file->save()){

      if(file_exists(DIR_FS_ADMIN.'ext/aas_modules/tmp/'.$module_file->filename)){
      
      	if(class_exists('ZipArchive')){//if ZipArchive exists use that

			    $zip = new ZipArchive;

			    if($zip->open(DIR_FS_ADMIN.'ext/aas_modules/tmp/'.$module_file->filename) === TRUE){
				  if(!is_writable(DIR_FS_ADMIN.'ext/aas_modules'))  chmod(DIR_FS_ADMIN.'ext/aas_modules', 0755);
				    
				    //get modules folder name
				    $info = pathinfo($module_file->filename);
				    $mfname=explode('-',$info['filename']);
  			    $module_fname = isset($mfname[1]) ? $mfname[1] : $mfname[0];//$info['filename'];
				    
				    if(file_exists('ext/aas_modules/'.$module_fname)){//WE already have module folder
				    
 				      //rename old folder.
				      if(!rename('ext/aas_modules/'.$module_fname, 'ext/aas_modules/'.$module_fname.'_old')){
  				      echo 'Cannot rename old module folder [ext/aas_modules/'.$module_fname.'] into [ext/aas_modules/'.$module_fname.'_old]';
                die;
              }

				    }//else echo 'Error: ext/aas_modules/'.$module_fname.' does not exist.';
				      
	          //since we rename the old module version then extract the new version
	          $zip->extractTo(DIR_FS_ADMIN.'ext/aas_modules');
			      $zip->close();
			      
    				  if(file_exists('ext/aas_modules/'.$module_fname)){
	
	            //execute sql to make module knonw at aas_aac field

	            $admins= $_SESSION['admin']['id']!='1' ? '1,'.$_SESSION['admin']['id'] : '1';
	
	            $aas_aac_query=tep_db_query("SELECT id FROM aas_settings WHERE skey='".$module_fname."' AND type='modules' ");
	            if(tep_db_num_rows($aas_aac_query)<=0){
	
		            tep_db_query("INSERT INTO aas_settings (skey,type,description,value) VALUES ('".$module_fname."','modules','AAS_AAC_MODULES_".mb_strtoupper($module_fname)."','".$admins."')");
	
	              if(isset($_SESSION['admin']['AAS']['AAC'])) unset($_SESSION['admin']['AAS']['AAC']);
	
	            }

	            if(isset($_SESSION['admin']['AAS']['modules'])) unset($_SESSION['admin']['AAS']['modules']);
	            echo '1';
	
	            //unlink zip file from tmp
	            @unlink(DIR_FS_ADMIN.'ext/aas_modules/tmp/'.$module_file->filename);
	            
	            //delete old module folder and old contents if exists
	            if(file_exists('ext/aas_modules/'.$module_fname.'_old')){
	              recursiveRemoveDirectory(DIR_FS_ADMIN.'ext/aas_modules/'.$module_fname.'_old');
	              //@unlink(DIR_FS_ADMIN.'ext/aas_modules/'.$module_fname.'_old');
	            }

            }else{
            
              //change old module folder name to what it was before
              if(file_exists('ext/aas_modules/'.$module_fname.'_old')){
                @rename('ext/aas_modules/'.$module_fname.'_old', 'ext/aas_modules/'.$module_fname);
              }
              echo 'Module folder not found. Extract failed!';
            
            }
			    
			    }else echo 'Cannot open zip file.';

		    }else echo 'PHP Class ZipArchive not installed.';
      
      }else echo 'Module zip was not found.';
      
    }else echo 'Cannot save module zip file to tmp folder.';

	break;

	case 'setAvailableDateNull':
		
			if(tep_not_null($product_id_post)){
		
				if(tep_db_query("UPDATE ".TABLE_PRODUCTS." SET products_date_available=NULL, products_last_modified=now() WHERE products_id = '" . (int)$product_id_post . "' ")) echo '1';
				else echo '0';

			}else echo '0';
	
	break;

	case'massedit':
		
		$pid_values = (isset($_POST['pid_values']) ? $_POST['pid_values'] : '');
		$cat_values = (isset($_POST['cat_values']) ? $_POST['cat_values'] : '');
		
		$assocs=array(
		
			'products_name'=>TABLE_PRODUCTS_DESCRIPTION,
			'products_description'=>TABLE_PRODUCTS_DESCRIPTION,
			'categories_name'=>TABLE_CATEGORIES_DESCRIPTION

		);		
		
		$pid_values=json_decode(stripslashes($pid_values));
					
		foreach($pid_values as $key => $value){
		
			$products_sets=array();
			$products_description_sets=array();
		
			foreach($value as $k => $v){
			
				if(isset($assocs[$v->column])) $products_description_sets[]=$v->column."='".tep_db_input(tep_db_prepare_input($v->value))."'";
				else $products_sets[]=$v->column."='".addslashes(tep_db_prepare_input($v->value))."'";
			
			}

			if(count($products_sets)>0) tep_db_query("UPDATE ".TABLE_PRODUCTS." SET ".implode(',',$products_sets)." WHERE products_id=".(int)$key."");
			if(count($products_description_sets)>0)  tep_db_query("UPDATE ".TABLE_PRODUCTS_DESCRIPTION." SET ".implode(',',$products_description_sets)." WHERE products_id=".(int)$key." AND language_id=".(int)$languages_id." ");
		
		}
		
		$cat_values=json_decode(stripslashes($cat_values));
		
		foreach($cat_values as $key => $value){
		
			$categories_sets=array();
			$categories_description_sets=array();
		
			foreach($value as $k => $v){
			
				if(isset($assocs[$v->column])) $categories_description_sets[]=$v->column."='".tep_db_input(tep_db_prepare_input($v->value))."'";
				else $categories_sets[]=$v->column."='".tep_db_prepare_input($v->value)."'";
			
			}
								
			if(count($categories_sets)>0) tep_db_query("UPDATE ".TABLE_CATEGORIES." SET ".implode(',',$categories_sets)." WHERE categories_id=".(int)$key."");
			if(count($categories_description_sets)>0) tep_db_query("UPDATE ".TABLE_CATEGORIES_DESCRIPTION." SET ".implode(',',$categories_description_sets)." WHERE categories_id=".(int)$key." AND language_id=".(int)$languages_id." ");
		
		}

		echo '1';
	
	break;

	case'loadCats':
		 
		 $categories_id = (isset($_POST['catid']) ? $_POST['catid'] : '');
		 
		 if(tep_not_null($categories_id)){
		 
		 $catsOrderBy=isset($_SESSION['admin']['AAS']['fieldsArray']['sort_order']) && $_SESSION['admin']['AAS']['fieldsArray']['sort_order']['visible'] ? 'c.sort_order' : 'c.sort_order, cd.categories_name';
		 $ascDesc = isset($_SESSION['admin']['AAS']['ascDesc']) ? $_SESSION['admin']['AAS']['ascDesc'] : 'ASC';

		 	$categories_query = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c,  " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id='".(int)$categories_id."' and c.categories_id=cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by ".$catsOrderBy." ".$ascDesc." ");
		 	$d=array();
		 	$cnt=0;
		 	while($category=tep_db_fetch_array($categories_query)){
		 	
		 		//$d[$category['categories_id']]=$category['categories_name'];
		 		$d[]=array('cid'=>$category['categories_id'],'cname'=>$category['categories_name']);
		 	
		 	}
		 
		 	echo json_encode($d);
		 
		 }else echo '0';
	
	break;

	case'locate_product':

		//require '../../../functions/general.php';
		$array=tep_get_products_parent_categories((int)$product_id_post);
		if(count($array)<=0) echo AAS_TEXT_TOP;
		else{
		
			$john=array_reverse($array[0]);
			echo count($john)>0 ? implode('&raquo;',$john) : AAS_TEXT_TOP;
		
		}

	break;

	case'import'://beta, use at your own risk

		$fields = (isset($_POST['fields']) ? $_POST['fields'] : '');
		$values = (isset($_POST['values']) ? $_POST['values'] : '');

		if(tep_not_null($fields) && tep_not_null($values)){
	
			$assocs=array(
		
			'products_name'=>TABLE_PRODUCTS_DESCRIPTION,
			'products_description'=>TABLE_PRODUCTS_DESCRIPTION,
			'products_url'=>TABLE_PRODUCTS_DESCRIPTION,
			'products_viewed'=>TABLE_PRODUCTS_DESCRIPTION
		
			);
	
			$exclude_fields=array(
			
			'products_price_gross'=>1,
			'products_order_status'=>1,
			'manufacturers_name'=>1,
			'tax_class_title'=>1,
			'sort_order'=>1,
			
			);
			
			$fields=json_decode(stripslashes($fields));
			$values=json_decode(stripslashes($values));
	
			foreach($values as $value){
		
				$products_sets=array();
				$products_description_sets=array();
				$pid=0;
				foreach($value as $key => $td){
			
					if($key==0){
				
						$pid=$td;
						continue;
				
					}
			
					if(!isset($exclude_fields[$fields[$key]])){
			
						if(isset($assocs[$fields[$key]])) $products_description_sets[]=$fields[$key]."='".tep_db_prepare_input($td)."'";
						else $products_sets[]=$fields[$key]."='".tep_db_prepare_input($td)."'";
					
					}
				
				}
			
				//those can be rewritten using insert and on duplicate
				if(count($products_sets)>0)tep_db_query("UPDATE ".TABLE_PRODUCTS." SET ".implode(',',$products_sets)." WHERE products_id=".(int)$pid."");
				if(count($products_description_sets)>0)tep_db_query("UPDATE ".TABLE_PRODUCTS_DESCRIPTION." SET ".implode(',',$products_description_sets)." WHERE products_id=".(int)$pid." AND language_id=".(int)$languages_id." ");
		
			}
	
		echo '1';
	
		}

	break;

	case'multipleRemoveFromTempProductsList':

		$pids = (isset($_POST['pids']) ? $_POST['pids'] : '');
		if(tep_not_null($pids)){
	
			$pids=explode(',',$pids);
			if(count($pids)>0){
				foreach($pids as $pid) unset($_SESSION['admin']['AAS']['parkedProducts'][$pid]);
			}

		}
		if(isset($_SESSION['admin']['AAS']['parkedProducts']))	echo count($_SESSION['admin']['AAS']['parkedProducts']);

	break;

	case'removeFromTempProductsList':

		$pid = (isset($_POST['pid']) ? $_POST['pid'] : '');
		if(tep_not_null($pid)){
			unset($_SESSION['admin']['AAS']['parkedProducts'][$pid]);
		}
		if(isset($_SESSION['admin']['AAS']['parkedProducts']))	echo count($_SESSION['admin']['AAS']['parkedProducts']);

	break;

	case'reloadTempProductsList':
		
			require 'ext/aas/application_top.php';
		
			ob_start();
			include 'ext/aas/plugins/temp_list/index.php';
			$df = ob_get_contents();
			ob_get_clean();

			echo $df;
			
	break;

	case'updateTempProductsList':

		$pids = (isset($_POST['pids']) ? $_POST['pids'] : '');
		if(tep_not_null($pids)){
	
			$pids=explode(',',$pids);
			$time=time();
			foreach($pids as $pid) $_SESSION['admin']['AAS']['parkedProducts'][$pid]=$time;
		
			require 'ext/aas/application_top.php';
		
			ob_start();
			include 'ext/aas/plugins/temp_list/index.php';
			$df = ob_get_contents();
			ob_get_clean();

			echo $df;
			
		}

	break;

	case'delete-attributes':

		$pids = (isset($_POST['pids']) ? $_POST['pids'] : '');
		$tas = (isset($_POST['tas']) ? $_POST['tas'] : '');
		$cid = (isset($_POST['cid']) ? $_POST['cid'] : '');

		if(tep_not_null($pids) && tep_not_null($tas) && tep_not_null($cid)){
		
  		$tas_exp=explode(',',$tas);
  		if(count($tas_exp)==7){
  		
				switch($tas_exp[5]){
			
					case'1'://delete from selected pids
					case'2'://delete from selected temp pids
				
						$pids=explode(',',$pids);
		
					break;
					case'3'://delete from all products under category.
								
						if($tas_exp[6]=='1'){//recursively
						
							$pids_query = tep_db_query("select ptc.products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc where ptc.categories_id IN (".implode(',',tep_aas_get_category_ids($tas_exp[3])).") order by ptc.products_id");
						
						}else{//non recursively
						
							$pids_query = tep_db_query("select ptc.products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc where ptc.categories_id ='".$tas_exp[3]."' order by ptc.products_id");
						
						}
						
						$pids=array();
						while ($pid = tep_db_fetch_array($pids_query)) $pids[]=$pid['products_id'];

					break;

				}
  		
				if(count($pids)>0){
				
					//FIRST DELETE attributes download
					$paid_query=tep_db_query("SELECT products_attributes_id FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id IN (".implode(',',$pids).") ");

					if(tep_db_num_rows($paid_query)>0){
						$paid_array=array();
						while($paid_row=tep_db_fetch_array($paid_query)) $paid_array[]=$paid_row['products_attributes_id'];
						tep_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " WHERE products_attributes_id IN (".implode(',',$paid_array).") ");
					
					}
					
					tep_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id IN (".implode(',',$pids).") ");
					echo '1-'.implode(',',$pids);
			
				}else echo '2';
  		
  		}else echo '0';
  		
		}else echo '0';
		
	
	break;
	case'copy-attributes':

		$pids = (isset($_POST['pids']) ? $_POST['pids'] : '');
		$tas = (isset($_POST['tas']) ? $_POST['tas'] : '');
		$cid = (isset($_POST['cid']) ? $_POST['cid'] : '');

		if(tep_not_null($pids) && tep_not_null($tas) && tep_not_null($cid)){
	
	
			$tas_exp=explode(',',$tas);
			if(count($tas_exp)==7){
		
				$products_id_from=$tas_exp[1];
			
				switch($tas_exp[2]){
			
					case'1'://copy to pids
					case'2'://copy to temp pids
				
						$pids=explode(',',$pids);
		
					break;
					case'3'://copy to all products under category.
								
						//require '../../../functions/general.php';
						if($tas_exp[6]=='1'){//recursively
						
							$pids_query = tep_db_query("select ptc.products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc where ptc.categories_id IN (".implode(',',tep_aas_get_category_ids($tas_exp[3])).") order by ptc.products_id");
						
						}else{//non recursively
						
							$pids_query = tep_db_query("select ptc.products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc where ptc.categories_id ='".$tas_exp[3]."' order by ptc.products_id");
						
						}
						
						$pids=array();
						$pidsatts=array();
						while ($pid = tep_db_fetch_array($pids_query)){ $pids[]=$pid['products_id']; $pidsatts[$pid['products_id']]=0; }

					break;

				}
				
				if(count($pids)>0){
			
						$attributes = tep_db_query("select products_attributes_id,products_id, options_id, options_values_id, options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES ." where products_id='".$products_id_from."'");
						
						if(tep_db_num_rows($attributes)>0){
						
							$aas=array();
							$aas_attributes_id=array();
							while($rowaa = tep_db_fetch_array($attributes)){
								$aas[]=$rowaa;
								$aas_attributes_id[]=$rowaa['products_attributes_id'];
							}
						
							if(DOWNLOAD_ENABLED == 'true'){
							
							  $attributes_downloads = tep_db_query("select * from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD ." where products_attributes_id IN (".implode(',',$aas_attributes_id).") ");
							  $aasd=array();
							  while($rowaad = tep_db_fetch_array($attributes_downloads)){
								  $aasd[$rowaad['products_attributes_id']]=$rowaad;
							  }
							
							}
					
							//delete existing attributes
							if($tas_exp[4]=='1'){
								
								if(DOWNLOAD_ENABLED == 'true'){
								
								  //DELETE attributes download also
								  $paid_query=tep_db_query("SELECT products_attributes_id FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id IN (".implode(',',$pids).") ");

								  if(tep_db_num_rows($paid_query)>0){
									  $paid_array=array();
									  while($paid_row=tep_db_fetch_array($paid_query)) $paid_array[]=$paid_row['products_attributes_id'];
									  tep_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " WHERE products_attributes_id IN (".implode(',',$paid_array).") ");
								  }
								
								}
								
								tep_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id IN (".implode(',',$pids).") ");
								
							}
							
							foreach($aas as $aa){//loop through found attributes
			
								if($tas_exp[4]=='1' || $tas_exp[4]=='2'){//allow duplicates
				
									foreach($pids as $pid){
					
										//if($pid==$products_id_from) continue;
					
										tep_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES . " ( products_id, options_id, options_values_id, options_values_price, price_prefix ) VALUES (".$pid.", ".$aa['options_id'].", ".$aa['options_values_id'].", ".$aa['options_values_price'].", '".$aa['price_prefix']."') ");	
										
										//INSERT INTO TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD
										if(DOWNLOAD_ENABLED == 'true' && isset($aasd[$aa['products_attributes_id']])){
									
											$inserted_attribute_id=tep_db_insert_id();
										
												tep_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " ( products_attributes_id, products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount ) VALUES (".$inserted_attribute_id.", '".$aasd[$aa['products_attributes_id']]['products_attributes_filename']."', ".$aasd[$aa['products_attributes_id']]['products_attributes_maxdays'].", ".$aasd[$aa['products_attributes_id']]['products_attributes_maxcount'].") ");
																		
										}
				
									}

								}elseif($tas_exp[4]=='3'){//disallow duplicates, lots of queries, but who cares its for admin
				
									foreach($pids as $pid){
					
										//if($pid==$products_id_from) continue;
					
										 $duplicate_check_query=tep_db_query("select count(*) as total from ".TABLE_PRODUCTS_ATTRIBUTES." 
										 WHERE products_id='".$pid."' 
										 AND options_id='".$aa['options_id']."'
										 AND options_values_id='".$aa['options_values_id']."'
										 AND options_values_price='".tep_db_input($aa['options_values_price'])."'
										 AND price_prefix='".tep_db_input($aa['price_prefix'])."'
										 ");
										 
										  $duplicate_check = tep_db_fetch_array($duplicate_check_query);
										  
										  if($duplicate_check['total']<1){
										  
										  	tep_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES . " ( products_id, options_id, options_values_id, options_values_price, price_prefix ) VALUES (".$pid.", ".$aa['options_id'].", ".$aa['options_values_id'].", ".$aa['options_values_price'].", '".$aa['price_prefix']."') ");

										    	if(DOWNLOAD_ENABLED == 'true' && isset($aasd[$aa['products_attributes_id']])){
										    		
										    		$inserted_attribute_id=tep_db_insert_id();
										    		tep_db_query("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " ( products_attributes_id, products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount ) VALUES (".$inserted_attribute_id.", '".$aasd[$aa['products_attributes_id']]['products_attributes_filename']."', ".$aasd[$aa['products_attributes_id']]['products_attributes_maxdays'].", ".$aasd[$aa['products_attributes_id']]['products_attributes_maxcount'].") ");
										    		
										    	}
										  	
										  }
										 
									}

								}
			
							}
							
							//get & return products attributes count
							$products_attributes_query = tep_db_query("SELECT products_id, COUNT(products_attributes_id) FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id IN (".implode(',',$pids).") GROUP BY products_id ");
            	$products_attributes=array();
            	while($products_attributes_row = tep_db_fetch_array($products_attributes_query)) $products_attributes[]=$products_attributes_row['products_id'].'='.$products_attributes_row['COUNT(products_attributes_id)'];
	
							echo '1-'.implode(',',$products_attributes);
						
						}else echo '2';
			
		
				}else echo '3';
		
		
			}else echo '0';
	
		}
	
	break;

	case'multiple_products_manager':

		$pids = (isset($_POST['pids']) ? $_POST['pids'] : '');
		$mcl = (isset($_POST['mcl']) ? $_POST['mcl'] : '');
		$cl = (isset($_POST['cl']) ? $_POST['cl'] : '');
		$cid = (isset($_POST['cid']) ? $_POST['cid'] : '');
	
		if(tep_not_null($pids) && tep_not_null($mcl) && tep_not_null($cl) && tep_not_null($cid)){

			$pids=explode(',',$pids);
		
			if(count($pids)>0){

				switch($mcl){
		
					case'1'://move
				
						$moved=array();
						$new_parent_id=tep_db_prepare_input($cl);
						foreach($pids as $pid){
				
							  $duplicate_check_query=tep_db_query("select count(*) as total from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".tep_db_input($pid)."' and categories_id='". tep_db_input($new_parent_id)."'");
							  $duplicate_check = tep_db_fetch_array($duplicate_check_query);
							  if($duplicate_check['total']<1){
							  
							  	if(tep_db_query("update ".TABLE_PRODUCTS_TO_CATEGORIES." set categories_id ='".tep_db_input($new_parent_id)."' where products_id='".tep_db_input($pid)."' and categories_id='". $cid."'")) $moved[]=$pid;
							  }
				
						}
					
						echo implode(',',$moved);
					
					break;
					case'2'://copy
				
						$copied=array();
						$categories_id = tep_db_prepare_input($cl);
					
						foreach($pids as $products_id){
					
							$product_query = tep_db_fetch_array(tep_db_query('select * from '.TABLE_PRODUCTS.' where products_id="'.(int)$products_id.'"'));
							$product_query['products_id'] = '';
							$product_query['products_ordered'] = '0';
							
							//set new copied products status to 0
							$product_query['products_status']='0';

							tep_db_perform(TABLE_PRODUCTS, $product_query);
						
							$dup_products_id = tep_db_insert_id();
							
							$num_row_query=tep_db_query("select * from ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id=".$products_id);
							if(tep_db_num_rows($num_row_query)>0){				
								while ($atts_row = tep_db_fetch_array($num_row_query)){
							
									tep_db_query("insert into ".TABLE_PRODUCTS_ATTRIBUTES." (products_id, options_id, options_values_id, options_values_price, price_prefix) values ('".$dup_products_id."','".$atts_row['options_id']."','". $atts_row['options_values_id']."','".tep_db_input($atts_row['options_values_price'])."','".tep_db_input($atts_row['price_prefix'])."')");
									$new_attrib_id = tep_db_insert_id();
								
									$paid_query=tep_db_query("select * from ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." WHERE products_attributes_id=".$atts_row['products_attributes_id']);
									if(tep_db_num_rows($paid_query)>0){
								
										$paid_row=tep_db_fetch_array($paid_query);
										tep_db_query("insert into ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." (products_attributes_id, products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount) values ('".$new_attrib_id."','".$paid_row['products_attributes_filename']."','". $paid_row['products_attributes_maxdays']."','".$paid_row['products_attributes_maxcount']."')");
								
									}
								
								}
							}
						
							$description_query=tep_db_query("select language_id, products_name, products_description, products_url from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$products_id."'");
							while ($description = tep_db_fetch_array($description_query)) {
							tep_db_query("insert into ".TABLE_PRODUCTS_DESCRIPTION." (products_id, language_id, products_name, products_description, products_url, products_viewed) values ('".$dup_products_id."', '".$description['language_id'] . "', '" . tep_db_input($description['products_name']) . "', '" . tep_db_input($description['products_description']) . "', '" . tep_db_input($description['products_url']) . "', '0')");
							}
							tep_db_query("insert into ".TABLE_PRODUCTS_TO_CATEGORIES." (products_id, categories_id) values ('". $dup_products_id."', '".$categories_id."')" );
						
							$copied[]=$products_id;
						
						}
					
						echo implode(',',$copied);
				
					break;
					case'3'://link
					
						$ptc_columns_query=tep_db_query("Show columns from ".TABLE_PRODUCTS_TO_CATEGORIES." like 'linked' ");
            if(tep_db_num_rows($ptc_columns_query)>0){
						
						  $categories_id = tep_db_prepare_input($cl);

						  $values=array();

						  foreach ($pids as $products_id){

						    if($categories_id != $cid) $values[]="('".tep_db_input($products_id)."', '".tep_db_input($categories_id)."', '1')";
						     
						  }
						
						  if(count($values)>0){
												
						    tep_db_query("insert ignore into ".TABLE_PRODUCTS_TO_CATEGORIES." (products_id, categories_id, linked) values ".implode(',',$values)." "); 
						
							  echo tep_db_affected_rows() ? 1 : 0;
							
						  }else echo 3;
						
						}else echo 4;
		
					break;
		
		
				}
			
				if (USE_CACHE == 'true') {tep_reset_cache_block('categories'); tep_reset_cache_block('also_purchased');}
		
			}

		}


	break;

	case 'deleteProducts':

		$pids = (isset($_POST['pids']) ? $_POST['pids'] : '');

		if(tep_not_null($pids)){

			$paid_resp=tep_db_query("SELECT products_attributes_id FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id IN (".implode(',',array_keys($pids)).") ");

			if(tep_db_num_rows($paid_resp)>0){
				$paid_array=array();
				while($paid_row=tep_db_fetch_array($paid_resp)) $paid_array[]=$paid_row['products_attributes_id'];
				tep_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " WHERE products_attributes_id IN (".implode(',',$paid_array).") ");
			}
			
			//product attributes are deleted from tep_remove_product() function
			$pidakia=array();
			foreach($pids as $key => $pid){
		
				$pidakia[]=$key;

				tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$key . "' and categories_id = '" . (int)$pid . "'");
				
				//delete also linked products
				tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$key . "'");
		
				$product_categories_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$key . "'");
				$product_categories = tep_db_fetch_array($product_categories_query);

				if ($product_categories['total'] == '0') { tep_remove_product($key); }

			}

			if (USE_CACHE == 'true') {tep_reset_cache_block('categories'); tep_reset_cache_block('also_purchased'); }

			echo '1';

		}

	break;

	case 'changeProductsStatus':

		$pids = (isset($_POST['pids']) ? $_POST['pids'] : '');
		$changeTo = (isset($_POST['changeTo']) ? $_POST['changeTo'] : '');

		if(tep_not_null($pids) && tep_not_null($changeTo)){

			echo (tep_db_query("update " . TABLE_PRODUCTS . " set products_status=".$changeTo." where products_id IN (".$pids.") ")) ? '1' : '0';

		}else echo '0';

	break;

	case 'description': //product description
		 if(tep_not_null($product_id_post)){

			$products_query = tep_db_query("select pd.products_description from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = '".(int)$product_id_post."' LIMIT 1");  
			$product_description = tep_db_fetch_array($products_query);
			echo $product_description['products_description'];
		}
	break;
	
	case 'removeLinkedProduct':
	
		$cid = (isset($_POST['cid']) ? $_POST['cid'] : '');
		
		if(tep_not_null($product_id_post) && tep_not_null($cid)){

			tep_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = '".(int)$product_id_post."' AND categories_id = '" . (int)$cid . "'");
			echo tep_db_affected_rows();

		}else echo 0;
	
	break;
	
	case'attributes-fetch-all-option-values':
			
			$lid = (isset($_POST['lid']) ? $_POST['lid'] : '');
			if(!tep_not_null($lid)) $lid=$languages_id;
		
			$values = tep_db_query("SELECT pov.* FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov	WHERE pov.language_id ='" . $lid . "' ORDER BY pov.products_options_values_name");
			while($values_values = tep_db_fetch_array($values)) echo '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
	
	break;
	
	case'attributes-toogle-button-pressed':
	
		$option_id = (isset($_POST['option_id']) ? $_POST['option_id'] : '');
		if(tep_not_null($option_id)){
		
			$lid = (isset($_POST['lid']) ? $_POST['lid'] : '');
			if(!tep_not_null($lid)) $lid=$languages_id;
		
			$values = tep_db_query("SELECT pov.* 
			FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov, ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." povpo
			WHERE pov.language_id ='" . $lid . "' AND povpo.products_options_values_id=pov.products_options_values_id AND povpo.products_options_id=".$option_id."
			ORDER BY pov.products_options_values_name");
			
			while($values_values = tep_db_fetch_array($values)) echo '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
		
		}else echo '0';
	
	break;

	case 'mass_edit_option_prices':
	
		$value_post = (isset($_POST['value']) ? $_POST['value'] : '');
		$applyTo = (isset($_POST['applyTo']) ? $_POST['applyTo'] : '');
	
		 if(tep_not_null($value_post) && tep_not_null($applyTo)){
		
				$values=json_decode(stripslashes($value_post));
				
				foreach($values as $key => $val){
	    					
    					tep_db_query("UPDATE ".TABLE_PRODUCTS_ATTRIBUTES." SET options_values_price='".(float)tep_db_prepare_input($val)."' WHERE products_attributes_id='".(int)$key."' ");
    				
    			}
				echo '1';
			}else echo '0';
	
	
	break;

  case'attributes-get-all-option-values':
  
			$lid = (isset($_POST['lid']) ? $_POST['lid'] : '');
			if(!tep_not_null($lid)) $lid=$languages_id;
		
			$values = tep_db_query("SELECT pov.* 
			FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov, ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." povpo
			WHERE pov.language_id ='" . $lid . "' AND povpo.products_options_values_id=pov.products_options_values_id 
			ORDER BY pov.products_options_values_name ASC");
			
			while($values_values = tep_db_fetch_array($values)) echo '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
  
  break;

	case'attributes-quick-load-assigned':
	
		$option_id = (isset($_POST['option_id']) ? $_POST['option_id'] : '');
		if(tep_not_null($option_id)){
		
		  $lid = (isset($_POST['lid']) ? $_POST['lid'] : '');
		  if(!tep_not_null($lid)) $lid=$languages_id;
			
			if(isset($_SESSION['admin']['AAS']['cache']['attributes_all'][$lid]) && isset($_SESSION['admin']['AAS']['cache']['attributes_all'][$lid][$option_id])){
			
			    foreach($_SESSION['admin']['AAS']['cache']['attributes_all'][$lid][$option_id] as $values_values) echo '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
			
			}else{//just in case
			
			  $values = tep_db_query("SELECT pov.* 
			  FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov, ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." povpo
			  WHERE pov.language_id ='" . $lid . "' AND povpo.products_options_values_id=pov.products_options_values_id AND povpo.products_options_id=".$option_id."
			  ORDER BY pov.products_options_values_name ASC");
			
			  if(tep_db_num_rows($values)<=0) echo '<option selected="selected" value="0"></option>';
			  else while($values_values = tep_db_fetch_array($values)) echo '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
			
			}
			
		}else echo '0';
	
	break;
	
	case'attributes-clever-copy':
	
		$lid = (isset($_POST['lid']) ? $_POST['lid'] : '');
  	if(!tep_not_null($lid)) $lid=$languages_id;
  	
  	$paid = (isset($_POST['paid']) ? $_POST['paid'] : '');
	  if(tep_not_null($lid)){
	  
		  $ascDesc = (isset($_POST['ascDesc']) ? $_POST['ascDesc'] : 'asc');
		  $orderBy_value = (isset($_POST['orderBy']) ? $_POST['orderBy'] : '');
	
		  switch($orderBy_value){
	
			  case'2': $orderBy='po.products_options_name'; break;
			  case'3': $orderBy='pa.options_values_price'; break;
			  default: $orderBy='pa.options_id, pa.options_values_price';
	
		  }
		  
		  //get selected attribute based on paid
		  $attribute = tep_db_query("select pa.*, po.products_options_name from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS . " po WHERE pa.products_attributes_id=".$paid." AND po.language_id='".$lid."' AND pa.options_id=po.products_options_id limit 1");
		
			$attribute_value = tep_db_fetch_array($attribute);
			
			//var_dump($attribute_value);
			
			if(isset($_SESSION['admin']['AAS']['cache']['attributes_all'][$lid])){
		  
  		  $attributes_all=$_SESSION['admin']['AAS']['cache']['attributes_all'][$lid];
		  
		  }else{
		  
		  	$attributes_all_by_poid_query = tep_db_query("SELECT po.products_options_id,po.products_options_name,pov.products_options_values_name, pov.products_options_values_id
			  FROM " . TABLE_PRODUCTS_OPTIONS . " po, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov, ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." povpo
			  WHERE po.language_id ='" . $lid . "' AND pov.language_id ='" . $lid . "' 
			  AND povpo.products_options_id=po.products_options_id
			  AND povpo.products_options_values_id=pov.products_options_values_id 
			  ORDER BY pov.products_options_values_name ".$ascDesc);
			  $attributes_all=array();
			  while($attributes_all_by_poid = tep_db_fetch_array($attributes_all_by_poid_query)) $attributes_all[$attributes_all_by_poid['products_options_id']][]=$attributes_all_by_poid;

        $_SESSION['admin']['AAS']['cache']['attributes_all'][$lid]=$attributes_all;
		  
		  }
		  
		  if(DOWNLOAD_ENABLED == 'true'){
	
		    $download_query = tep_db_query("select products_attributes_id,products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " where products_attributes_id = '".$paid."' limit 1");
				$download_values = tep_db_fetch_array($download_query);
  
		  }

?>

<h4><?php echo AAS_DIALOG_ATTRIBUTES_SMART_COPY_CHOOSEN_ATTRIBUTE; ?></h4>
<div class="margin-10-auto"><div>
<table class="tbl-general tablesorter" style="width:100%">
<thead>
<tr>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_NAME; ?></th>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_VALUE; ?></th>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_PRICE_PREFIX; ?></th>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_PRICE; ?></th>
  <?php if (DOWNLOAD_ENABLED == 'true') { ?>
  <th class="dataTableHeadingContent"><?php echo AAS_DIALOG_ATTRIBUTES_DOWNLOADABLE_PRODUCTS; ?><br/><span class="downloadable_products_subheaders"><?php echo AAS_DIALOG_ATTRIBUTES_DOWNLOADABLE_PRODUCTS_SUBHEADERS; ?></span></th>
  <?php } ?>
</tr>
</thead>
<tbody>
<tr>
  <td><?php echo $attribute_value['products_options_name']; ?></td>
  <td><?php foreach($attributes_all[$attribute_value['options_id']] as $aav){  
      if($aav['products_options_values_id']==$attribute_value['options_values_id']){ echo $aav['products_options_values_name']; break; }
  }?></td>
  <td><?php echo $attribute_value['price_prefix']; ?></td>
  <td><?php echo $attribute_value['options_values_price']; ?></td>
  <?php if (DOWNLOAD_ENABLED == 'true') { ?>
  <td><?php if(isset($download_values['products_attributes_filename'])){
  echo $download_values['products_attributes_filename'].' | '.$download_values['products_attributes_maxdays'].' | '.$download_values['products_attributes_maxcount'];
  }else echo '---';?>
  </td>
  <?php } ?>
</tr>
</tbody>
</table>

<div class="margin-20-auto"><div>

<div id="attributes_smart_copy_accordion">

  <h3><?php echo AAS_DIALOG_ATTRIBUTES_SMART_COPY_ALREADY_EXIST; ?></h3>
  <div>

<?php

	  //get selected attribute based on paid
	  $attributes_other = tep_db_query("select pa.*, po.products_options_name from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS . " po WHERE pa.products_attributes_id!=".$paid." AND pa.options_id=".$attribute_value['options_id']."  AND pa.products_id=".$attribute_value['products_id']." AND po.language_id='".$lid."' AND pa.options_id=po.products_options_id");
	
    $other_attributes_array=array();
		while($attributes_o = tep_db_fetch_array($attributes_other)) $other_attributes_array[$attributes_o['products_attributes_id']]=$attributes_o;
		
		if(DOWNLOAD_ENABLED == 'true'){

      $akoaa=array_keys($other_attributes_array);
      $other_downloads=array();      
      if(count($akoaa)>0){
		  
		    $other_downloads_query = tep_db_query("select products_attributes_id,products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " where products_attributes_id IN (".implode(',',$akoaa).")");
      
		    while($other_downloads_values = tep_db_fetch_array($other_downloads_query)){
  		    $other_downloads[$other_downloads_values['products_attributes_id']]=$other_downloads_values;		  
		    }
		  
		  }
  
		}
		
?>

<table class="tbl-general tablesorter" style="width:100%">
<thead>
<tr>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_NAME; ?></th>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_VALUE; ?></th>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_PRICE_PREFIX; ?></th>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_PRICE; ?></th>
  <?php if (DOWNLOAD_ENABLED == 'true') { ?>
  <th class="dataTableHeadingContent"><?php echo AAS_DIALOG_ATTRIBUTES_DOWNLOADABLE_PRODUCTS; ?><br/><span class="downloadable_products_subheaders"><?php echo AAS_DIALOG_ATTRIBUTES_DOWNLOADABLE_PRODUCTS_SUBHEADERS; ?></span></th>
  <?php } ?>
</tr>
</thead>
<tbody>
<?php foreach($other_attributes_array as $oakey => $oak) { ?>
<tr>
  <td><?php echo $oak['products_options_name']; ?></td>
  <td><?php foreach($attributes_all[$oak['options_id']] as $aav){  
      if($aav['products_options_values_id']==$oak['options_values_id']){ echo $aav['products_options_values_name']; break; }
  }?></td>
  <td><?php echo $oak['price_prefix']; ?></td>
  <td><?php echo $oak['options_values_price']; ?></td>
  <?php if (DOWNLOAD_ENABLED == 'true') { ?>
  <td><?php if(isset($other_downloads[$oakey]) && isset($other_downloads[$oakey]['products_attributes_filename'])){
  echo $other_downloads[$oakey]['products_attributes_filename'].' | '.$other_downloads[$oakey]['products_attributes_maxdays'].' | '.$other_downloads[$oakey]['products_attributes_maxcount'];
  }else echo '---';?>
  </td>
  <?php } ?>
</tr>
<?php } ?>
</tbody>
</table>

  </div>
  <h3><?php echo AAS_DIALOG_ATTRIBUTES_SMART_COPY_AVAILABLE_FOR_ADDING; ?></h3>
  <div>
    <?php
    
			$attributes_values_array=array();	
			$attributes = tep_db_query("select pa.* from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS . " po WHERE pa.options_id=".$attribute_value['options_id']." AND po.language_id='".$lid."' AND pa.options_id=po.products_options_id AND products_id=".$attribute_value['products_id']." order by ".$orderBy." ".$ascDesc);
			
			if(tep_db_num_rows($attributes)<=0) die('All option values are No availble');					
			while($attributes_values = tep_db_fetch_array($attributes)) $attributes_values_array[$attributes_values['options_values_id']][]=$attributes_values;
    
    ?>  
<table id="attributes-available-to-add-table" class="tbl-general tablesorter" style="width:100%">
<thead>
<tr>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_NAME; ?></th>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_VALUE; ?></th>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_PRICE_PREFIX; ?></th>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_PRICE; ?></th>
  <?php if (DOWNLOAD_ENABLED == 'true') { ?>
  <th class="dataTableHeadingContent"><?php echo AAS_DIALOG_ATTRIBUTES_DOWNLOADABLE_PRODUCTS; ?><br/><span class="downloadable_products_subheaders"><?php echo AAS_DIALOG_ATTRIBUTES_DOWNLOADABLE_PRODUCTS_SUBHEADERS; ?></span></th>
  <?php } ?>
  <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_ACTION; ?></th>
</tr>
</thead>
<tbody>
<?php 

			foreach($attributes_all[$attribute_value['options_id']] as $aa){
			
			  if(isset($attributes_values_array[$aa['products_options_values_id']])) continue;
			  
			  ?>
			  
			  <tr>
			  <td>
			    <input type="hidden" value="<?php echo $attribute_value['options_id']; ?>" name="products_options_id" class="products_options_id">
			    <input type="hidden" value="<?php echo $aa['products_options_values_id']; ?>" name="products_options_values_id" class="products_options_values_id">
			    <?php echo $aa['products_options_name']; ?>
			  </td>
			  <td><?php echo $aa['products_options_values_name']; ?></td>
			  <td>
			      <select name="price_prefix" class="price_prefix">
					  <option value="+" <?php echo $attribute_value['price_prefix']==='+' ? 'selected="selected"' : ''; ?>><?php echo AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_PLUS; ?></option>
					  <option value="-" <?php echo $attribute_value['price_prefix']==='-' ? 'selected="selected"' : ''; ?>><?php echo AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_MINUS; ?></option>
					  <option value="" <?php echo $attribute_value['price_prefix']==='' ? 'selected="selected"' : ''; ?>><?php echo AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_BLANK; ?></option>
					  </select>
			  </td>
        <td><input type="text" name="value_price" class="value_price lfor" value="<?php echo $attribute_value['options_values_price']; ?>" size="8"></td>
        
        <?php if (DOWNLOAD_ENABLED == 'true') { ?>
        
				   <td>
				   <button class="applyButton downloadableButton">&#8226;</button>
				   <input type="text" placeholder="Filename" name="downloadable_filename" class="downloadable_filename lfor" style="text-align:left;" value="<?php echo $download_values['products_attributes_filename']; ?>" >
				   <input type="number" min="0" placeholder="Expiry Days" name="downloadable_maxdays" class="downloadable_maxdays lfor" style="text-align:left;" value="<?php echo tep_not_null($download_values['products_attributes_maxdays'])?$download_values['products_attributes_maxdays']:DOWNLOAD_MAX_DAYS; ?>">
				   <input type="number" min="0"  placeholder="Maximum download count" name="downloadable_maxcount" class="downloadable_maxcount lfor" style="text-align:left;" value="<?php echo tep_not_null($download_values['products_attributes_maxcount'])?$download_values['products_attributes_maxcount']:DOWNLOAD_MAX_COUNT; ?>">
				   </td>
				   				   
					<?php } ?>
				
					<td><button class="applyButton exclude_attribute_button_attributesbutton" ><?php echo AAS_OPT_ACTION_EXCLUDE; ?></button></td>
			  
			<?php
			}

?>
</tbody>
</table>
    
  </div>

</div>

<div class="margin-20-auto"><div>

<?php
  
	  }
	
	break;
	
	case 'attributes':
		
		$lid = (isset($_POST['lid']) ? $_POST['lid'] : '');
		if(!tep_not_null($lid)) $lid=$languages_id;

    $visualizer = (isset($_POST['visualizer']) ? $_POST['visualizer'] : '');

		if(tep_not_null($product_id_post)){
		
			$ascDesc = (isset($_POST['ascDesc']) ? $_POST['ascDesc'] : 'asc');
			$orderBy_value = (isset($_POST['orderBy']) ? $_POST['orderBy'] : '');
		
			switch($orderBy_value){
		
				case'2': $orderBy='po.products_options_name'; break;
				case'3': $orderBy='pa.options_values_price'; break;
				default: $orderBy='pa.options_id, pa.options_values_price';
		
			}
			 
			$attributes_values_array=array();
				
			$attributes = tep_db_query("select pa.* from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS . " po WHERE pa.products_id=".$product_id_post." AND po.language_id='".$lid."' AND pa.options_id=po.products_options_id order by ".$orderBy." ".$ascDesc);
		
			while($attributes_values = tep_db_fetch_array($attributes)) $attributes_values_array[]=$attributes_values;
			 
			$attributes_values_options_id=array();
			foreach($attributes_values_array as $attributes_values) $attributes_values_options_id[$attributes_values['options_id']]=1;
			 		 
			$options_array=array();
			$options = tep_db_query("select * from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $lid . "' order by products_options_name ".$ascDesc);
			while($options_values = tep_db_fetch_array($options)) $options_array[]=$options_values;
			 
			$values_array=array();
			$values = tep_db_query("select * from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where language_id ='" . $lid . "' order by products_options_values_name ".$ascDesc);
			while($values_values = tep_db_fetch_array($values)) $values_array[]=$values_values;

			if (DOWNLOAD_ENABLED == 'true') {
		
				$tempAttIds=array();
				foreach($attributes_values_array as $att) $tempAttIds[]=$att['products_attributes_id'];
		
				$download_query_raw ="select products_attributes_id,products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " ";
				if(count($tempAttIds)>0) $download_query_raw.=" where products_attributes_id  IN (" .implode(',',$tempAttIds) . ")";

		    $download_query = tep_db_query($download_query_raw);

				$downloads_array=array();
				while($downloads_values = tep_db_fetch_array($download_query)) $downloads_array[$downloads_values['products_attributes_id']]=$downloads_values;
				  		                                  
		  }
		  
		  if(isset($_SESSION['admin']['AAS']['cache']['attributes_all'][$lid])){
		 
  		  $attributes_all=$_SESSION['admin']['AAS']['cache']['attributes_all'][$lid];
		  
		  }else{
		  
		  	$attributes_all_by_poid_query = tep_db_query("SELECT po.products_options_id,po.products_options_name,pov.products_options_values_name, pov.products_options_values_id
			  FROM " . TABLE_PRODUCTS_OPTIONS . " po, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov, ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." povpo
			  WHERE po.language_id ='" . $lid . "' AND pov.language_id ='" . $lid . "' 
			  AND povpo.products_options_id=po.products_options_id
			  AND povpo.products_options_values_id=pov.products_options_values_id 
			  ORDER BY pov.products_options_values_name ".$ascDesc);
			  $attributes_all=array();
			  while($attributes_all_by_poid = tep_db_fetch_array($attributes_all_by_poid_query)) $attributes_all[$attributes_all_by_poid['products_options_id']][]=$attributes_all_by_poid;

        $_SESSION['admin']['AAS']['cache']['attributes_all'][$lid]=$attributes_all;
		  
		  }
		  
		  if(tep_not_null($visualizer)){ //VISUALIZER
		  
		    //get products data
		    $pidData= tep_db_query("select pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE p.products_id=".$product_id_post." AND p.products_id=pd.products_id AND pd.language_id='".$lid."' LIMIT 1");
		    $product = tep_db_fetch_array($pidData);
		  
		    $opts=array();
		    foreach($attributes_values_array as $attributes_values) $opts[$attributes_values['options_id']][$attributes_values['options_values_id']]=$attributes_values;
		    
		    ?>
		<ul id="org" style="display:none">
      <li><?php echo $product['products_name']; ?>
       <ul>
<?php foreach($opts as $ok=>$opt){
        
         echo '<li>';

         foreach($options_array as $options_values){
						
						if($ok == $options_values['products_options_id']){
						  echo $options_values['products_options_name'];
						  echo '<ul>';
						  foreach($opt as $okey => $o){

						    foreach($attributes_all[$options_values['products_options_id']] as $values_values){
						    
						      if($okey==$values_values['products_options_values_id']){
						       echo '<li>'.$values_values['products_options_values_name'] . '<hr>'.$o['price_prefix'].' '.$o['options_values_price'];						      
						        if(DOWNLOAD_ENABLED == 'true' && isset($downloads_array[$o['products_attributes_id']]['products_attributes_filename']) && $downloads_array[$o['products_attributes_id']]['products_attributes_filename']!='' ){
          				    echo '<br>&nbsp;[&nbsp;';
          				    echo $downloads_array[$o['products_attributes_id']]['products_attributes_filename'];
          				    echo '&nbsp;,&nbsp;';
          				    echo isset($downloads_array[$o['products_attributes_id']]['products_attributes_maxdays']) ? $downloads_array[$o['products_attributes_id']]['products_attributes_maxdays'] : DOWNLOAD_MAX_DAYS;
          				    echo '&nbsp;,&nbsp;';
          				    echo isset($downloads_array[$o['products_attributes_id']]['products_attributes_maxcount']) ? $downloads_array[$o['products_attributes_id']]['products_attributes_maxcount'] : DOWNLOAD_MAX_COUNT;
          				    echo '&nbsp;]&nbsp;';
				            }
						       echo '</li>';
						      }

						    }
						  
						  }
						  echo '</ul>';

             // break;						
					}
						
				}
          
        echo '</li>';

     }
?>
       </ul>
     </li>
   </ul>  
<?php
		  
		    die; //die here so not to display the rest
		  
		  }
		  
			if(tep_db_num_rows($attributes)>0){ ?>
			
			<select id="product_attributes_ascDesc" class="product_attributes_ascDesc" onchange="sort_product_attributes(this)">
				<option <?php echo $ascDesc=='asc' ? 'selected="selected"':''; ?> value="asc"><?php echo AAS_TEXT_ASC; ?></option>
				<option <?php echo $ascDesc=='desc' ? 'selected="selected"':''; ?> value="desc"><?php echo AAS_TEXT_DESC; ?></option>
			</select>

			<select id="product_attributes_orderBy" class="product_attributes_orderBy" onchange="sort_product_attributes(this)">
				 <option <?php echo $orderBy_value=='1' ? 'selected="selected"':''; ?> value="1"><?php echo AAS_DIALOG_ATTRIBUTES_ORDERED_BY_OPTION_ID_PRICE; ?></option>
				 <option <?php echo $orderBy_value=='2' ? 'selected="selected"':''; ?> value="2"><?php echo AAS_DIALOG_ATTRIBUTES_ORDERED_BY_OPTION_NAME; ?></option>
				 <option <?php echo $orderBy_value=='3' ? 'selected="selected"':''; ?> value="3"><?php echo AAS_DIALOG_ATTRIBUTES_ORDERED_BY_OPTION_PRICE; ?></option>
			</select>
			
      <a id="reloadProductsAttributesTrigger" class="attstoolicon" data-title="<?php echo AAS_DIALOG_ATTRIBUTES_ICON_TITLE_RELOAD_ATTRIBUTES_LIST; ?>" data-pid="<?php echo $product_id_post; ?>" href="#"><img src="ext/aas/images/glyphicons_081_refresh.png" alt="" ></a>
			<a id="attributesManagerTrigger" class="attstoolicon" data-title="<?php echo AAS_DIALOG_ATTRIBUTES_ICON_TITLE_ATTRIBUTES_MANAGER; ?>" data-pid="<?php echo $product_id_post; ?>" href="#"><img src="ext/aas/images/glyphicons_048_dislikes.png" alt="" ></a>
			<a id="visualizeAttributesTrigger" class="attstoolicon" data-title="<?php echo AAS_DIALOG_ATTRIBUTES_ICON_TITLE_PRODUCTS_ATTRIBUTES_VISUALIZER; ?>" data-pid="<?php echo $product_id_post; ?>" href="#"><img src="ext/aas/images/glyphicons_056_projector.png" alt="" ></a>


			 <fieldset id="attributes_mass_edit_option_prices">
			 <legend id="attributes_mass_edit_option_prices_legend"><div class="attributes_mass_edit_option_prices_toggle"></div>&nbsp;<?php echo AAS_TEXT_DISCOUNT_EDIT_OPTION_PRICES; ?></legend>
			 <div id="attributes_mass_edit_option_prices_form">
			 
					<input type="number" min="0" value="0" id="discount-field-option-prices" class="lfor discount-field-option-prices" placeholder="Amount" style="font-size:10px">
					<select id="select-discount-option-prices" style="display:inline-block;font-size:10px;"><option value="=">=</option><option value="-%">-%</option><option value="+%">+%</option><option value="-">-</option><option value="+">+</option></select>
					<select id="attributes_selectMenus_options-option-prices" name="options_id" style="display:inline-block;font-size:10px;">
					<option selected="selected" value="0"><?php echo AAS_TEXT_APPLY_TO_ALL_OPTION_PRICES; ?></option>
          <option value="-1"><?php echo AAS_TEXT_APPLY_TO_SELECTED_ATTRIBUTES_OPTION_PRICES; ?></option>
					<?php foreach($options_array as $options_values){ 
								
					if(isset($attributes_values_options_id[$options_values['products_options_id']])) echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '">' . $options_values['products_options_name'] . '</option>';
				
					} ?>
					</select>
				
					<button id="dicount-option-prices-button" class="applyButton" onclick="massEditOptionPrices()" style="font-size:10px"><?php echo AAS_APPLY; ?></button>

			 </div>
			 </fieldset>

      <button id="attributes-delete-selected" class="applyButton"><?php echo AAS_DIALOG_ATTRIBUTES_DELETE_SELECTED; ?></button>			 
			 
			<?php } ?>

			  <table id="attributes-table" class="tbl-general tablesorter" style="width:100%">
			  <thead>
			  <tr>
				   <th class="dataTableHeadingContent"><input type="checkbox" class="attributes_checkbox_all_selector">&nbsp;<?php echo AAS_HEADING_OPT_DIESI; ?></th>
				   <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_NAME; ?></th>
				   <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_VALUE; ?></th>
				   <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_PRICE_PREFIX; ?></th>
				   <th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_PRICE; ?></th>
				   
				   <?php if (DOWNLOAD_ENABLED == 'true') { ?>
				   <th class="dataTableHeadingContent"><?php echo AAS_DIALOG_ATTRIBUTES_DOWNLOADABLE_PRODUCTS; ?><br/><span class="downloadable_products_subheaders"><?php echo AAS_DIALOG_ATTRIBUTES_DOWNLOADABLE_PRODUCTS_SUBHEADERS; ?></span></th>
					<?php } ?>
					<th class="dataTableHeadingContent"><?php echo AAS_HEADING_OPT_ACTION; ?></th>
			   </tr>
			   </thead>
			   <tbody>
			  <?php 
			  $css=0;
			  foreach($attributes_values_array as $attributes_values){ 
			  
			  $avoid=0;
			  $option_names_html='';
			  foreach($options_array as $options_values){
						$selected_pon='';
						if($attributes_values['options_id'] == $options_values['products_options_id']){
						
						  $avoid=$options_values['products_options_id'];
  						$selected_pon='selected="selected"';
						
						}
						$option_names_html.='<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '" '.$selected_pon.'>' . $options_values['products_options_name'] . '</option>'; 
			  }
			  
			  ?> 
				<tr id="aid_<?php echo $attributes_values['products_attributes_id']; ?>" class="atts_group_by_option_name_<?php echo $avoid; ?>" data-initbgcolor="">
				  <td><input type="checkbox" class="attributes_checkbox_selector" value="<?php echo $attributes_values['products_attributes_id']; ?>">&nbsp;<?php echo ++$css;  ?>&nbsp;</td>
					<td>
						<input type="hidden" class="hidden_products_attributes_id" value="<?php echo $attributes_values['products_attributes_id']; ?>" />
						<input type="hidden" class="hidden_products_id" value="<?php echo $product_id_post; ?>" />

						<select class="attributes_selectMenus_options" name="options_id" onchange="ats_option_name_change(this)">           
						<?php echo $option_names_html; ?>
						</select>
						
					</td>
					<td>
						<?php if($avoid>0){ ?>
						<select class="attributes_selectMenus_values" name="values_id">
						<?php
              
              if(isset($attributes_all[$avoid])){//in case we have an attribute with option name but not option value
						    
						    foreach($attributes_all[$avoid] as $values_values) echo '<option name="' . $values_values['products_options_values_name'] . '" '.($attributes_values['options_values_id'] == $values_values['products_options_values_id'] ? 'selected="selected"' : '').' value="' . $values_values['products_options_values_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
						  
						  }else{
						  
						    echo '<option selected="selected" value="0"></option>';
						  
						  }
						
						?>
						</select>
						<?php }else{ //just in case ?>

						<select class="attributes_selectMenus_values" name="values_id">
						<?php foreach($values_array as $values_values) echo '<option name="' . $values_values['products_options_values_name'] . '" '.($attributes_values['options_values_id'] == $values_values['products_options_values_id'] ? 'selected="selected"' : '').' value="' . $values_values['products_options_values_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
						?>
						</select>						
						
						<?php } ?>

					</td>
					<td>

					  <select name="price_prefix" class="price_prefix">
					  <option value="+" <?php echo $attributes_values['price_prefix']==='+' ? 'selected="selected"' : ''; ?>><?php echo AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_PLUS; ?></option>
					  <option value="-" <?php echo $attributes_values['price_prefix']==='-' ? 'selected="selected"' : ''; ?>><?php echo AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_MINUS; ?></option>
					  <option value="" <?php echo $attributes_values['price_prefix']==='' ? 'selected="selected"' : ''; ?>><?php echo AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_BLANK; ?></option>
					  </select>
					
					</td>
					<td class="td_<?php echo $attributes_values['options_id']; ?>">&nbsp;<input type="text" name="value_price" id="value_price_<?php echo $attributes_values['products_attributes_id']; ?>" class="value_price lfor" value="<?php echo $attributes_values['options_values_price']; ?>" size="8">&nbsp;</td>
													
					<?php if (DOWNLOAD_ENABLED == 'true') { ?>
				   <td>&nbsp;
				   <button class="applyButton downloadableButton">&#8226;</button>
				   <input type="text" placeholder="Filename" name="downloadable_filename" class="downloadable_filename lfor" style="text-align:left;" value="<?php echo isset($downloads_array[$attributes_values['products_attributes_id']]['products_attributes_filename']) ? $downloads_array[$attributes_values['products_attributes_id']]['products_attributes_filename'] : ''; ?>" >
				   <input type="number" min="0" placeholder="Expiry Days" name="downloadable_maxdays" class="downloadable_maxdays lfor" style="text-align:left;" value="<?php echo isset($downloads_array[$attributes_values['products_attributes_id']]['products_attributes_maxdays']) ? $downloads_array[$attributes_values['products_attributes_id']]['products_attributes_maxdays'] : DOWNLOAD_MAX_DAYS; ?>">
				   <input type="number" min="0"  placeholder="Maximum download count" name="downloadable_maxcount" class="downloadable_maxcount lfor" style="text-align:left;" value="<?php echo isset($downloads_array[$attributes_values['products_attributes_id']]['products_attributes_maxcount']) ? $downloads_array[$attributes_values['products_attributes_id']]['products_attributes_maxcount'] : DOWNLOAD_MAX_COUNT; ?>">
				   &nbsp;</td>
					<?php } ?>
				
					<td><button class="applyButton attributesCleverCopyTrigger"><?php echo AAS_OPT_ACTION_SMART_COPY; ?></button>&nbsp;<button class="delete_attribute_button attributesbutton" id="attributes_item_delete_<?php echo $attributes_values['products_attributes_id']; ?>" ><?php echo AAS_OPT_ACTION_DELETE; ?></button></td>
				
				</tr>

			<?php } ?>
			 </tbody>
			</table>

		 <?php
		 
		 }

	break;
	
	case 'attributes-add-new':

		$lid = (isset($_POST['lid']) ? $_POST['lid'] : '');
		if(!tep_not_null($lid)) $lid=$languages_id;
		
		  if(isset($_SESSION['admin']['AAS']['cache']['attributes_all'][$lid])){
		  
  		  $attributes_all=$_SESSION['admin']['AAS']['cache']['attributes_all'][$lid];
		  
		  }else{
		  
		  	$attributes_all_by_poid_query = tep_db_query("SELECT po.products_options_id,po.products_options_name,pov.products_options_values_name, pov.products_options_values_id
			  FROM " . TABLE_PRODUCTS_OPTIONS . " po, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov, ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." povpo
			  WHERE po.language_id ='" . $lid . "' AND pov.language_id ='" . $lid . "' 
			  AND povpo.products_options_id=po.products_options_id
			  AND povpo.products_options_values_id=pov.products_options_values_id 
			  ORDER BY pov.products_options_values_name ASC");
			  $attributes_all=array();
			  while($attributes_all_by_poid = tep_db_fetch_array($attributes_all_by_poid_query)) $attributes_all[$attributes_all_by_poid['products_options_id']][]=$attributes_all_by_poid;

        $_SESSION['admin']['AAS']['cache']['attributes_all'][$lid]=$attributes_all;
		  
		  }

		if(tep_not_null($product_id_post)){ ?>
			<tr style="background:#F5D2CB;">
				
				<td></td>
				<td>
					<select class="attributes_selectMenus_options_new" id="attributes_selectMenus_options_<?php echo $product_id_post; ?>" name="options_id" onchange="ats_option_name_change(this)">           
					<?php
			      $options = tep_db_query("select * from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $lid . "' order by products_options_name");
			      $OptionNameId=0;
			      $selectedOptionNameId=0;
			      while($options_values = tep_db_fetch_array($options)){ 
			      
			        if(++$OptionNameId==1){

					      $selectedOptionNameId=$options_values['products_options_id'];
			          echo '<option selected="selected" name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '">' . $options_values['products_options_name'] . '</option>';
			        
			        }else echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '">' . $options_values['products_options_name'] . '</option>';
			      
			      }
					?>
					</select>
				</td>
				<td>
					
					<?php if($selectedOptionNameId>0){ ?>
					
  					<select class="attributes_selectMenus_values_new" id="attributes_selectMenus_values_<?php echo $product_id_post; ?>" name="values_id">
						<?php
              
              if(isset($attributes_all[$selectedOptionNameId])){//in case we have an attribute with option name but not option value
						    
						    foreach($attributes_all[$selectedOptionNameId] as $values_values) echo '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
						  
						  }else{
						  
						    echo '<option selected="selected" value="0"></option>';
						  
						  }
						
						?>
						</select>
					
					<?php }else{ ?>
					
					<select class="attributes_selectMenus_values_new" id="attributes_selectMenus_values_<?php echo $product_id_post; ?>" name="values_id">
					<?php 
					      $values = tep_db_query("select * from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where language_id ='" . $lid . "' order by products_options_values_name");
					      while($values_values = tep_db_fetch_array($values))echo '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
					?>
					</select>
					
					<?php } ?>
					
				</td>
				
				<td>
					  <select name="price_prefix_new" class="price_prefix_new">
					  <option value="+" selected="selected"><?php echo AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_PLUS; ?></option>
					  <option value="-"><?php echo AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_MINUS; ?></option>
					  <option value=""><?php echo AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_BLANK; ?></option>
					  </select>
				</td>
				
				<td>&nbsp;<input type="text" name="value_price" class="value_price_new lfor" value="0" size="8">&nbsp;</td>
								
				<?php if (DOWNLOAD_ENABLED == 'true') { ?>
			   <td>&nbsp;
			   <button class="applyButton downloadableButton">&#8226;</button>
			   <input type="text" placeholder="Filename" name="new_downloadable_filename" class="new_downloadable_filename lfor" value="" >
			   <input type="number" min="0" placeholder="Expiry Days" name="new_downloadable_maxdays" class="new_downloadable_maxdays lfor" value="<?php echo DOWNLOAD_MAX_DAYS; ?>">
			   <input type="number" min="0" placeholder="Maximum download count" name="new_downloadable_maxcount" class="new_downloadable_maxcount lfor" value="<?php echo DOWNLOAD_MAX_COUNT; ?>">
			   &nbsp;</td>
				<?php } ?>
				<td>&nbsp;<button class="remove_attribute_button attributesbutton"><?php echo AAS_OPT_ACTION_REMOVE; ?></button>&nbsp;</td>
			</tr>

		<?php
		}
	break;

	case'fetchOtherLanguage':

		if(tep_not_null($product_id_post)){

			$lid = (isset($_POST['lid']) ? $_POST['lid'] : '');
			$languages = tep_get_languages();
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++){
				if ($languages[$i]['id'] == $lid){

					$selected_language=$languages[$i];
					break;
				}
			}

			$products_query = tep_db_query("select pd.products_description from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$selected_language['id'] . "' and p.products_id = '".(int)$product_id_post."' LIMIT 1");  
			$product_description = tep_db_fetch_array($products_query);

		?>	
			<fieldset id="overlay-fieldset-<?php echo $lid; ?>">
				<legend><?php echo AAS_TEXT_LANGUAGE; ?><img src="<?php echo DIR_WS_CATALOG_LANGUAGES . $selected_language['directory'] . '/images/' . $selected_language['image']; ?>" alt="<?php echo $selected_language['name']; ?>" title="<?php echo $selected_language['name']; ?>">&nbsp;<?php echo $selected_language['name']; ?>
  				
          &nbsp;&nbsp;<button class="applyButton descriptionbuttonaki" onclick="changeEditor('description_editor_<?php echo $lid; ?>')"><?php echo AAS_TEXT_TOGGLE_EDITOR; ?></button>
          &nbsp;<button class="applyButton descriptionbuttonaki" onclick="submitDescriptionChanges('<?php echo $product_id_post; ?>','<?php echo $lid; ?>')"><?php echo AAS_BUTTON_TEXT_SUBMIT_CHANGES; ?></button>
          &nbsp;<button class="applyButton descriptionbuttonaki" onclick="reloadDescriptionPreview('iframias_<?php echo $lid; ?>')"><?php echo AAS_BUTTON_TEXT_RELOAD_PREVIEW; ?></button>
				
					<a href="<?php echo HTTP_CATALOG_SERVER.DIR_WS_CATALOG; ?>product_info.php?products_id=<?php echo $product_id_post; ?>&language=<?php echo $selected_language['code']; ?>" class="view_products_page product_description_action" data-title="View in new window/tab"><img src="<?php echo DIR_WS_ADMIN . 'ext/aas/images/glyphicons_152_new_window.png'; ?>"></a>
			
			<a href="#" id="edit_products_attributes" data-pid="<?php echo $product_id_post; ?>" data-lid="<?php echo $selected_language['id']; ?>" data-productname="" class="edit_products_attributes product_description_action" data-title="<?php echo AAS_VIEW_EDIT_ATTRIBUTES; ?>"><img src="<?php echo DIR_WS_ADMIN.'ext/aas/images/glyphicons_048_dislikes.png'; ?>"></a>
					
				</legend>

					<div class="leftPanel">
						<textarea id="description_editor_<?php echo $lid; ?>" data-editor="<?php echo $defaults['productsDescriptionEditor']; ?>" class="description_editor"><?php echo $product_description['products_description'];?></textarea>
					</div>
					<div class="rightPanel">

						<iframe id="iframias_<?php echo $lid; ?>" class="iframia"></iframe>
						<input type="hidden" id="previewid" value="<?php echo isset($defaults['productDescriptionUniqueIdWrapper']) && $defaults['productDescriptionUniqueIdWrapper']!='' ? $defaults['productDescriptionUniqueIdWrapper'] : 'tbl';  ?>" />
						<input type="hidden" id="overlay_pid_<?php echo $lid; ?>" name="pid" value="<?php echo $product_id_post; ?>" />
						<input type="hidden" class="overlay_pid" value="<?php echo $product_id_post; ?>" />
						<input type="hidden" class="overlay_language_id" value="<?php echo $lid; ?>" />
					</div>

			</fieldset>

		<?php	
		}else echo '0';

	break;
	
	case'getTimestamp':
	
	  echo $time;
	
	break;

}

?>
