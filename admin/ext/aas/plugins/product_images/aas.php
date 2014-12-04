<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

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

	case'deleteLargeImage':
				
		$products_image_id = (isset($_POST['pimgid']) ? $_POST['pimgid'] : '');
		
		if(tep_not_null($products_image_id)){
		
			$pimg_query=tep_db_query("SELECT image FROM ".TABLE_PRODUCTS_IMAGES." WHERE id='".(int)$products_image_id."' LIMIT 1 ");
			
			if(tep_db_num_rows($pimg_query)>0){
			
				$pimg_row=tep_db_fetch_array($pimg_query);
				
				if(tep_db_query("DELETE FROM ".TABLE_PRODUCTS_IMAGES." WHERE id='".(int)$products_image_id."' ")){
				
					//unlink image
					if(file_exists(DIR_FS_CATALOG_IMAGES.$pimg_row['image'])){
						@unlink(DIR_FS_CATALOG_IMAGES.$pimg_row['image']);
					}

				}else echo '0';
			
			}else echo '0';
		
		}else echo '0';
	
	break;
	case'uploadSmallImageAwtd':
	
		$products_id = (isset($_POST['pid']) ? $_POST['pid'] : '');
		$path = (isset($_POST['images_path']) ? $_POST['images_path'] : '');	
		$awtd = (isset($_POST['awtd']) ? $_POST['awtd'] : '');
		
		if($path!='') $path=$path.'/';
		if(tep_not_null($products_id)){
		
			if(tep_not_null($awtd)){
		  
		    $oldImageName = (isset($_POST['oldImageName']) ? $_POST['oldImageName'] : '');
		    $newImageName = (isset($_POST['newImageName']) ? $_POST['newImageName'] : '');
		  
		    
		  
		  		if($awtd=='0' && tep_not_null($oldImageName)){
		  		
		  		  $pif=$path.$oldImageName;
		  		
		  		  @rename(DIR_FS_ADMIN.'ext/aas/plugins/product_images/tmp/'.$oldImageName, DIR_FS_CATALOG_IMAGES.$pif);
		  		
		  		}elseif($awtd=='1'){
		  		
	  				$a = mt_rand(100000,999999);
				    $pif=$path.'p_'.$a.'_'.$oldImageName;
				    @rename(DIR_FS_ADMIN.'ext/aas/plugins/product_images/tmp/'.$oldImageName, DIR_FS_CATALOG_IMAGES.$pif);
		  		
		  		}elseif($awtd=='2'){
		  		
		  		  $pif=$path.$newImageName;
				  
				    @rename(DIR_FS_ADMIN.'ext/aas/plugins/product_images/tmp/'.$oldImageName, DIR_FS_CATALOG_IMAGES.$pif);

				  }
				  
				  $sql_data_array = array('products_image'=>tep_db_prepare_input($pif), 'products_last_modified' => 'now()');
					tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");
					echo $pif;
		  
		  
		  }
		
		}
	
	break;
	case'uploadSmallImage':
	
		$products_id = (isset($_POST['pid']) ? $_POST['pid'] : '');
		$path = (isset($_POST['images_path']) ? $_POST['images_path'] : '');
		$odf = (isset($_POST['on_duplicate_filename']) ? (int)$_POST['on_duplicate_filename'] : 0);
		
		
		if($path!='') $path=$path.'/';

		$status_change='0_@_';
		if(tep_not_null($products_id)){	
		
			$products_image = new upload('products_image');
			$products_image->set_destination(DIR_FS_CATALOG_IMAGES.$path);
		
			if($products_image->parse()){
			
				$pif=$path.$products_image->filename;
						
				  //if image exists with same filename then append random number
				  if(file_exists(DIR_FS_CATALOG_IMAGES.$pif)){
				  			    
				    if($odf==3) die('abort');
				    if($odf==2){
				    
				      //store image to tmp

				      $products_image->set_destination('ext/aas/plugins/product_images/tmp');
  				    //$products_image->set_filename($products_image->filename);
              $products_image->save();
				      die('ask_what_to_do');
				    
				    }				  
			      if($odf==1){
			      
					    $a = mt_rand(100000,999999);
					    $pif=$path.'p_'.$a.'_'.$products_image->filename;
					    $products_image->set_filename('p_'.$a.'_'.$products_image->filename);
					    
				    }
					 
				  }
				  
				
				
				if($products_image->save()){
				
					$sql_data_array = array('products_image'=>tep_db_prepare_input($pif), 'products_last_modified' => 'now()');
					tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");
					$status_change=$pif.'_@_';
				
				}else $status_change='0_@_';
			
			
			}else $status_change='0_@_';
	
			if($messageStack->size > 0) echo $status_change.$messageStack->output();

		}else echo $status_change.AAS_TEXT_NO_PRODUCT_ID_FOUND;
	
	break;
	
	case'updateHTMLContent':
	
			$imgpids = (isset($_POST['imgpids']) ? $_POST['imgpids'] : '');
			$htmlcontent = (isset($_POST['htmlcontent']) ? $_POST['htmlcontent'] : '');
			
			if(tep_not_null($imgpids)){
			
				tep_db_query("UPDATE " . TABLE_PRODUCTS_IMAGES . " SET htmlcontent='".tep_db_input($htmlcontent)."' WHERE id='" . tep_db_input($imgpids) . "' ");
				echo '1';
			
			}else echo '0';
	
	break;
		
	case'sortOrderLargeImages':
	
		$imgpids = (isset($_POST['imgpids']) ? $_POST['imgpids'] : '');
		if(tep_not_null($imgpids)){
		
			foreach($imgpids as $key => $imgpid){
			
				if($imgpid!=''){
				
					$imgpidExp=explode('_l_',$imgpid);
			
					tep_db_query("UPDATE " . TABLE_PRODUCTS_IMAGES . " SET sort_order=".($key+1)." WHERE id='" . tep_db_input($imgpidExp[1]) . "'");
			
				}
			
			}
		
		}else echo '0';
	
	break;
	
	case'getLargeImages':
	
		$products_id = (isset($_POST['pid']) ? $_POST['pid'] : '');
		
		if(tep_not_null($products_id)){
		
			//store the folders list once so you can use it more than one time (cache mechanism)
			$list=listFolders(realpath(DIR_FS_CATALOG_IMAGES),'product_images-image_folder','product_images-image_folder',false);

			$product_images_query = tep_db_query("select id, image, htmlcontent, sort_order from " . TABLE_PRODUCTS_IMAGES . " where products_id = '" . (int)$products_id . "' order by sort_order");
      $plicnt=0;
      while ($product_images = tep_db_fetch_array($product_images_query)) {
      $plicnt++;
      ?>
       
       <fieldset id="product_images-large_image-list_l_<?php echo $product_images['id']; ?>" class="product_images-large_image-list" data-status="old" data-pimgid="<?php echo $product_images['id']; ?>">
						<legend class="product_images-close_large_image"><img class="product_images-large-image-wrapper-remove-btn" src="ext/aas/images/remove-15x15.png" alt="Remove Large Image"></legend>
						<div class="product_images_handle"></div>
						<fieldset class="product_images-dragndrop-noinline product_images-large_image-drag-n-drop-wrapper" id="product_images-large_image-drag-n-drop-wrapper_<?php echo $plicnt; ?>">
							<h2><?php echo AAS_PRODUCT_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE; ?></h2><?php echo AAS_PRODUCT_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE_OR; ?> <input type="file" accept="image/*" name="products_large_image" class="product_images-large_image-file_select_input" id="product_images-large_image-file_select_input_<?php echo $plicnt; ?>">
						</fieldset>					
						<fieldset class="product_images-large_image-htmlcontent-wrapper">
						<legend><input type="button" class="applyButton updateHTMLContent" value="<?php echo AAS_PRODUCT_IMAGES_UPDATE_HTML_CONTENT; ?>" style="display:block;" /></legend>
							<textarea class="lfor product_images-large_image-htmlcontent" placeholder="<?php echo AAS_PRODUCT_IMAGES_HTML_CONTENT; ?>" id="product_images-large_image-htmlcontent_<?php echo $plicnt; ?>"><?php echo $product_images['htmlcontent']; ?></textarea>
						</fieldset>

						<fieldset class="product_images-image_preview" style="display:block;">
							<legend><?php echo AAS_PRODUCT_IMAGES_LARGE_IMAGE_PREVIEW; ?></legend>
							<div class="product_images-large_image-display" id="product_images-large_image-display_<?php echo $plicnt; ?>">
							
								<img src="<?php echo DIR_WS_CATALOG_IMAGES.$product_images['image']; ?>" alt="product large image" class="product_images-large_image_img" >
							
							</div>
						</fieldset>
						<fieldset class="product_images-image_change_save_path" id="product_images-image_change_save_path_<?php echo $plicnt; ?>">
							<legend><?php echo AAS_PRODUCT_IMAGES_CHANGE_NEW_IMAGE_LOCATION; ?>&nbsp;<img class="product_images-image_change_save_path_toggle" src="ext/aas/images/circle_plus-15x15.png" alt="toggle folders list"></legend>
							
								<ol class="product_images-large_images_folders">
								<?php
								if(!tep_is_writable(DIR_FS_CATALOG_IMAGES)) echo '<li><label style="color:lightGray"><input disabled="disabled" type="radio" name="product_images-image_folder_'.$plicnt.'" value="'.DIR_FS_CATALOG_IMAGES.'">'.AAS_PRODUCT_IMAGES_IMAGES.'<span class="no-writable">'.AAS_PRODUCT_IMAGES_NO_WRITABLE.'</span></label>';
							else echo '<li><label><input type="radio" name="product_images-image_folder_'.$plicnt.'" value="" class="product_images-image_folder" checked="checked" >'.substr(DIR_FS_CATALOG_IMAGES, strlen(DIR_FS_CATALOG)).'</label>';
							//add increment value to radio name
							echo str_replace('name="product_images-image_folder"','name="product_images-image_folder_'.$plicnt.'"',$list);
							?>
							</ol>
							
						</fieldset>
			</fieldset>
       
       
       <?php

      }
		
		
		}else echo '0';
		
	break;
	case'uploadLargeImage':
	
		$products_id = (isset($_POST['pid']) ? $_POST['pid'] : '');
		$products_image_id = (isset($_POST['imgId']) ? $_POST['imgId'] : '');
		$path = (isset($_POST['images_path']) ? $_POST['images_path'] : '');
		
		$htmlcontent = (isset($_POST['htmlcontent']) ? urldecode($_POST['htmlcontent']) : '');
		$sort_order = (isset($_POST['sort_order']) ? $_POST['sort_order'] : '');
		
		if(tep_not_null($products_id)){
					
			if($path!='') $path=$path.'/';
			$products_image = new upload('products_large_image');
			$products_image->set_destination(DIR_FS_CATALOG_IMAGES.$path);
		
			if($products_image->parse()){
			
					$pif=$path.$products_image->filename;
					$status_change='0_@_';
					
					//if image exists with same filename then append random number
					if(file_exists(DIR_FS_CATALOG_IMAGES.$pif)){
			
						$a = mt_rand(100000,999999);
						$pif=$path.'l_'.$a.'_'.$products_image->filename;
						$products_image->set_filename('l_'.$a.'_'.$products_image->filename);

					}
					
					if($products_image->save()){
					
						if(tep_not_null($products_image_id)){ //existing large Image

							$sql_data_array = array('image'=>tep_db_prepare_input($pif), 'sort_order' => tep_db_input($sort_order), 'htmlcontent'=>tep_db_input($htmlcontent));
							tep_db_perform(TABLE_PRODUCTS_IMAGES, $sql_data_array, 'update', "id= '" . (int)$products_image_id . "'");
							$lastInsertedId=0;
							
						}else{ //new large Image

		
							tep_db_query("insert into " . TABLE_PRODUCTS_IMAGES . " (products_id, image, htmlcontent, sort_order) values ('" . (int)$products_id . "', '" . tep_db_input($pif) . "', '" . tep_db_input($htmlcontent) . "', '" . tep_db_input($sort_order) . "')");
							$lastInsertedId=tep_db_insert_id();
		
						}
						
						$status_change=$pif.'_@_'.$lastInsertedId.'_@_';					
					
					}else $status_change='0_@_0_@_';
			
			
			}else $status_change='0_@_0_@_';
		
			if($messageStack->size > 0) echo $status_change.$messageStack->output();	
		
		}
		
	break;

	
}

?>
