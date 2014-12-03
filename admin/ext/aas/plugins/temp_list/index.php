<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/

defined('AAS') or die;
?>
<div class="leftSidePanel-wrapper">
<?php echo AAS_TEXT_TEMP_TITLE; ?>

  <div class="temp-list-count-products">
    <span id="num_of_temp_products_list"><?php echo $parked_products_count>0 ? $parked_products_count : 0; ?></span> <?php echo AAS_TEXT_TEMP_PRODUCTS; ?>
  </div>
<?php if($parked_products_count>0){ ?>
    <select class="temp-list-select-action" onchange="tempListActionSelect(this)">
      <option value="0"><?php echo AAS_TEXT_TEMP_SELECT_ACTION; ?></option>
      <option value="1"><?php echo AAS_TEXT_TEMP_SELECT_ALL; ?></option>
      <option value="2"><?php echo AAS_TEXT_TEMP_UNSELECT_ALL; ?></option>
      <option value="3"><?php echo AAS_TEXT_TEMP_REMOVE_SELECT; ?></option>
      <option value="4"><?php echo AAS_TEXT_TEMP_EXPORT_LIST; ?></option>
    </select>
<?php } ?>
</div>
<div class="leftSidePanel-list">
  <?php if($parked_products_count>0){

   while($pproducts=tep_db_fetch_array($parked_products_query)){ ?>
  <div class="savedProducts_data">
    <div class="savedProducts_name">
      <div class="truncate">
        <label><input type="checkbox" name="saveProducts[]" class="checkedTempProducts" value="<?php echo $pproducts['products_id']; ?>"><?php echo $pproducts['products_name']; ?></label>
      </div>
      <div class="clear"></div>
      <div class="toggle-savedProducts-info"></div>
      <div class="remove-savedProduct" id="remove-<?php echo $pproducts['products_id']; ?>"></div>
      <a target="_blank" href="<?php echo HTTP_CATALOG_SERVER.DIR_WS_CATALOG; ?>product_info.php?products_id=<?php echo $pproducts['products_id']; ?>" class=""><img style="width:15px;opacity:0.6;" src="ext/aas/images/glyphicons_152_new_window.png" alt=""></a>
      <a data-title="<img src='ext/aas/images/loading.gif'> " href="#" style="cursor:default" id="temp-location-link-<?php echo $pproducts['products_id']; ?>" class="temp-list-location-link"><img style="width:15px;opacity:0.6;" src="ext/aas/images/glyphicons_233_direction.png" alt=""></a>
    </div>
    <ul class="savedProducts-ul" id="temp_list_elem_<?php echo $pproducts['products_id']; ?>">
<?php foreach($fieldsArray as $key => $value){

    if($value['visible'] ){ ?>
    <li>
<?php switch($key){

      case'products_date_available':
        $datetime=substr($pproducts[$key],0,-9);

        echo $value['theadText'].': '.$datetime;

      break;
      case 'products_description':

        if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]){

          echo 'Edit disabled';

        }else echo $value['theadText'].': <button data-productname="'.$pproducts['products_name'].'" id="triggar_'.(int)$pproducts['products_id'].'" title="'.AAS_VIEW_EDIT_DESCRIPTION.'" class="descriptionbutton">'.AAS_VIEW_EDIT.'</button>';
      break;
      case 'products_ordered':

        echo $value['theadText'].': '.$pproducts[$key];
      break;
      case'products_status':

        if($pproducts[$key]){

          $ficon='icn_alert_success.png';

        }else{

          $ficon='icn_alert_error.png';

        }

        echo $value['theadText'].': <img src="ext/aas/images/'.$ficon.'" alt="">';

      break;
      case'attributes':

        if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]){

          echo 'Edit disabled';

        }else echo $value['theadText'].' :<button data-productname="'.$pproducts['products_name'].'" id="attributes_triggar_'.(int)$pproducts['products_id'].'" title="'.AAS_VIEW_EDIT_ATTRIBUTES.'" class="attributesbutton">'.AAS_VIEW_EDIT.'</button>';

      break;
      case'tax_class_title':
      
        echo $value['theadText'].': ';

        foreach($tax_class_array as $tca){
          
          if($tca['id']==$pproducts['products_tax_class_id']){
             echo $tca['title'].' '.$tca['tax_rate'].'%';
            break;
          }

        }

      break;
      case'manufacturers_name':

         echo $value['theadText'].': ';
         
         foreach($manufacturers_array as $man) {

          if($man['id']==$pproducts['manufacturers_id']){
            echo $man['name'];
            break;
          }

         }

      break;
      case'products_image':

        echo tep_info_image($pproducts[$key], $pproducts['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);

      break;
      case'products_price_gross':

        echo $value['theadText'].': '.$currencies->display_price($pproducts['products_price'], tep_get_tax_rate($pproducts['products_tax_class_id']));

      break;
      case'products_order_status':

        $numItems = count($orders_status_array);
        foreach($orders_status_array as $key => $osa) echo $osa.': '.(isset($products_quantity_by_orders[$pproducts['products_id']][$key])?$products_quantity_by_orders[$pproducts['products_id']][$key]:'0').($key<$numItems? ' / ': '');

      break;
      case'products_linked':
      case'special':
        echo $value['theadText'];
      break;
      
      case'sort_order':
      
        if(isset($pproducts['products_'.$key])) echo $value['theadText'].': '.$pproducts[$key];
        else echo $value['theadText'].': ---';
      
      break;
      
      case'id':
      case'date_added':
      case'last_modified':
      
        echo $value['theadText'].': '.$pproducts['products_'.$key];
      
      break;
      default:

        echo $value['theadText'].': '.$pproducts[$key];

    } ?></li>
  <?php }
  } ?>
    </ul>
  </div>
  <?php } ?>
  <?php } ?>
</div>
