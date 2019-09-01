<?php
/*
  $Id: qtprodoctor.php
  $Loc: catalog/admin/
      
  2017 QTPro 6.3 BS
  by @raiwa 
  info@oscaddons.com
  www.oscaddons.com
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce
     
  Released under the GNU General Public License
  
  Based on prior works released under the GNU General Public License:
  QT Pro prior versions
  Ralph Day, October 2004
  Tom Wojcik aka TomThumb 2004/07/03 based on work by Michael Coffman aka coffman
  FREEZEHELL - 08/11/2003 freezehell@hotmail.com Copyright (c) 2003 IBWO
  Joseph Shain, January 2003
  Modifications made:
  11/2004 - Add input validation
            clean up register globals off problems
            use table name constant for products_stock instead of hard coded table name
  03/2005 - Change $_SERVER to $HTTP_SERVER_VARS for compatibility with older php versions
        
*******************************************************************************************
  
      QT Pro Stock Add/Update
  
      This is a page to that is linked from the osCommerce admin categories page when an
      item is selected.  It displays a products attributes stock and allows it to be updated.

*******************************************************************************************

  $Id: stock.php,v 1.00 2003/08/11 14:40:27 IBWO Exp $

  Enhancement module for osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  
  Credit goes to original QTPRO developer.
  Attributes Inventory - FREEZEHELL - 08/11/2003 freezehell@hotmail.com
  Copyright (c) 2003 IBWO

*/
  require('includes/application_top.php');

 	$product_investigation = (isset($HTTP_GET_VARS['pID']))? qtpro_doctor_investigate_product($HTTP_GET_VARS['pID']) : null;
	$qtpro_sick_count = qtpro_sick_product_count();
	if ($qtpro_sick_count != 0) {
	  $messageStack->add(sprintf(constant('MODULE_CONTENT_QTPRO_ADMIN_WARNING_' . strtoupper($language)), $qtpro_sick_count, tep_href_link('qtprodoctor.php')), 'error');
  }

  require('includes/template_top.php');

  if (!defined('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_STATUS')) {
    echo '<div class="secWarning">' . QTPRO_OPTIONS_WARNING . '<br>
          <a href="modules_content.php?module=cm_pi_qtpro_options&action=install">' . QTPRO_OPTIONS_INSTALL_NOW . '</a></div>';
  }
  if ( !defined('MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS') || (defined('MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS') && MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS != 'True') ) {
    echo '<div class="secWarning">' . QTPRO_HT_WARNING . '<br>
                                <a href="modules.php?set=header_tags&module=ht_qtpro_stock_check&action=install">' . QTPRO_HT_INSTALL_NOW . '</a></div>';
  }

  $VARS = null;
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $VARS = $_GET;
  } else {
    $VARS = $_POST;
  }
  if ( isset($VARS['action']) && $VARS['action'] == 'Add' ) {
    $inputok = true;
    if (!(is_numeric($VARS['product_id']) and ($VARS['product_id']==(int)$VARS['product_id']))) $inputok = false;
    foreach($VARS as $v1 => $v2) {
      if (preg_match('/^option(\d+)$/',$v1,$m1)) {
        if (is_numeric($v2) and ($v2==(int)$v2)) $val_array[] = $m1[1].'-'.$v2;
        else $inputok = false;
      }
    }
    if (!(is_numeric($VARS['quantity']) and ($VARS['quantity']==(int)$VARS['quantity']))) $inputok = false;

    if (($inputok)) {
      sort($val_array, SORT_NUMERIC);
      $val = join(',', $val_array);      
      $q = tep_db_query("select products_stock_id as stock_id from products_stock where products_id=" . (int)$VARS['product_id'] . " and products_stock_attributes='" . $val . "' order by products_stock_attributes");
      if (tep_db_num_rows($q)>0) {
        $stock_item = tep_db_fetch_array($q);
        $stock_id = $stock_item['stock_id'];
        if ($VARS['quantity'] = intval($VARS['quantity'])) {
          tep_db_query("update products_stock set products_stock_quantity=" . (int)$VARS['quantity'] . " where products_stock_id=$stock_id");
        } else {
          tep_db_query("delete from products_stock where products_stock_id=" . $stock_id);
        }
      } else {
        tep_db_query("insert into products_stock values ('0','" . (int)$VARS['product_id'] . "', '" . $val . "', '" . (int)$VARS['quantity'] . "')");
      }
      $q = tep_db_query("select sum(products_stock_quantity) as summa from products_stock where products_id=" . (int)$VARS['product_id'] . " and products_stock_quantity > 0");
      $list = tep_db_fetch_array($q);
      $summa = (empty($list['summa'])) ? 0 : $list['summa'];
      tep_db_query("update products set products_quantity=" . $summa . " where products_id=" . (int)$VARS['product_id']);
      if (($summa<1) && (STOCK_ALLOW_CHECKOUT == 'false')) {
        tep_db_query("update products set products_status='0' where products_id=" . (int)$VARS['product_id']);
      }
    }
  }
  if ( isset($VARS['action']) && $VARS['action'] == 'Update' ) {
    tep_db_query("update products set products_quantity=" . (int)$VARS['quantity'] . " where products_id=" . (int)$VARS['product_id']);
    if (($VARS['quantity']<1) && (STOCK_ALLOW_CHECKOUT == 'false')) {
      tep_db_query("update products set products_status='0' where products_id=" . (int)$VARS['product_id']);
    }
  }
  if ( isset($VARS['action']) && $VARS['action'] == 'Apply to all') {

  }
  $flag = null;
  $q = tep_db_query("select products_name,products_options_name as _option,products_attributes.options_id as _option_id,products_options_values_name as _value,products_attributes.options_values_id as _value_id from 
                  products_description, products_attributes,products_options,products_options_values where 
                  products_attributes.products_id = products_description.products_id and 
                  products_attributes.products_id = " . (int)$VARS['product_id'] . " and 
                  products_attributes.options_id = products_options.products_options_id and 
                  products_attributes.options_values_id = products_options_values.products_options_values_id and 
                  products_description.language_id = " . (int)$languages_id . " and 
                  products_options_values.language_id = " . (int)$languages_id . " and products_options.products_options_track_stock = 1 and 
                  products_options.language_id = " . (int)$languages_id . " order by products_attributes.options_id, products_attributes.options_values_id");

 $db_quantity = null;
 if (tep_db_num_rows($q)>0) {
    $flag = 1;
    while($list = tep_db_fetch_array($q)) {
      $options[$list['_option_id']][] = array($list['_value'], $list['_value_id']);
      $option_names[$list['_option_id']] = $list['_option'];
      $product_name = $list['products_name'];
    }
  } else {
    $q = tep_db_query("select products_quantity, products_name from products p, products_description pd where pd.products_id=" . (int)$VARS['product_id'] . " and p.products_id=" . (int)$VARS['product_id']);
    $list = tep_db_fetch_array($q);
    $db_quantity = $list['products_quantity'];
    $product_name = stripslashes($list['products_name']);
  }
  
  $product_investigation = qtpro_doctor_investigate_product($VARS['product_id']);
  
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE . ': ' . $product_name . '<td></td><br><br>'; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table>
		</td>
      </tr>
      <tr>
        <td><form action="<?php echo $PHP_SELF;?>" method="get">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
<?php
  $title_num = 1;
  if ($flag) {
    foreach($options as $k => $v) {
      echo '<td class="dataTableHeadingContent">&nbsp;&nbsp;' . $option_names[$k] . '</td>';
      $title[$title_num] = $k;
    }
    echo '<td class="dataTableHeadingContent"><span class="smalltext">' . TABLE_HEADING_QUANTITY . '</span></td><td width="100%">&nbsp;</td>';
    echo '</tr>';
    //sorting below goes by name rather than products_stock_attributes. Much easier to have it all sorted alphabetically
    $q = tep_db_query("select ps.products_stock_id, ps.products_id, ps.products_stock_attributes, ps.products_stock_quantity, pov.products_options_values_id, pov.language_id, pov.products_options_values_name from products_stock ps, products_options_values pov where ps.products_id=" . $VARS['product_id'] . " and pov.products_options_values_id = substring_index(ps.products_stock_attributes, '-', -1) order by pov.products_options_values_name asc");
    $test_string = null;
    while($rec = tep_db_fetch_array($q)) {
      $val_array = explode(',', $rec['products_stock_attributes']);
      if (strpos($test_string, $rec['products_stock_attributes']) === false) {
        echo '<tr>';
        foreach($val_array as $val) {
          if (preg_match('/^(\d+)-(\d+)$/',$val,$m1)) {
            echo '<td class="smalltext">&nbsp;&nbsp;&nbsp;' . tep_values_name($m1['2']) . '</td>';
          } else {
            echo '<td>&nbsp;</td>';
          }
        }
        for($i = 0;$i<sizeof($options)-sizeof($val_array);$i++) {
          echo '<td>&nbsp;</td>';
        }
        echo '<td class="smalltext">&nbsp;&nbsp;&nbsp;&nbsp;' . $rec['products_stock_quantity'] . '</td><td>&nbsp;</td></tr>';
        $test_string .= $rec['products_stock_attributes'] . ';';
      }
    }
    echo '<tr>';
    reset($options);
    $i = 0;
    foreach($options as $k => $v) {
      echo '<td class="dataTableHeadingRow"><select name="option' . $k . '">';
      foreach($v as $v1) {
        echo '<option value="' . $v1['1'] . '">' . $v1['0'];
      }
      echo '</select></td>';
      $i++;
    }
  } else {
    $i = 1;
    echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_QUANTITY . '</td>';
  }
  echo '<td class="dataTableHeadingRow"><input type="text" name="quantity" size="4" value="' . $db_quantity . '"><input type="hidden" name="product_id" value="' . $VARS['product_id'] . '"></td><td width="100%" class="dataTableHeadingRow">&nbsp;<input type="submit" name="action" value="' . (($db_quantity)? BUTTON_UPDATE : BUTTON_ADD ) . '">&nbsp;</td><td width="100%" class="dataTableHeadingRow">&nbsp;</td>';
?>
              </tr>
            </table></td>
          </tr>
        </table>
        </form></td>
      </tr>
<tr><td>

<br>

<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="dataTableHeadingRow">
<table width="100%" class="boxText" border="0" cellspacing="3" cellpadding="6"> 
<tr valign="top">
	<td class="dataTableHeadingContent" width="400"><?php echo TABLE_HEADING_QTPO_DOCTOR;?></td>
	<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LINKS;?></td>
	
</tr>

<tr valign="top">
	<td class="menuBoxHeading" width="400">
	<span style="color: black;">
	<?php 
		echo qtpro_doctor_formulate_product_investigation($product_investigation, 'detailed');
  	?>
	</span>
	</td>
	
    <td class="menuBoxHeading">
	<?php 
	
	
	echo '<br><ul><li><a href="' . tep_href_link('categories.php', 'pID=' . $VARS['product_id'] . '&action=new_product') . '" class="menuBoxContentLink">' . TEXT_LINK_EDIT_PRODUCT . '</a></li>';
	echo '<li><a href="' . tep_href_link('stats_low_stock_attrib.php', '', 'NONSSL') . '" class="menuBoxContentLink">' . TEXT_LINK_LOW_STOCK_REPORT . '</a><br></li>';
	
	//class="menuBoxHeading columnLeft
	//We shall now generate links back to the product in the admin/categories.php page.
	//The same product can exist in differend categories.
	  
	  //Generate both the text (in $path_array) and the parameter (in $cpath_string_array)
	  $raw_path_array = tep_generate_category_path($VARS['product_id'], 'product');
	  $path_array = array();
	  $cpath_string_array = array();
	  foreach($raw_path_array as $raw_path){
	    $path_in_progress = '';
		$cpath_string_in_progress = '';
	  	foreach($raw_path as $raw_path_piece){
	      $path_in_progress .= $raw_path_piece['text'].' >> ';
		  $cpath_string_in_progress .= $raw_path_piece['id'].'_';
	    }
		$path_array[] = substr($path_in_progress, 0, -4);
		$cpath_string_array[] = substr($cpath_string_in_progress, 0, -1);
	  }
	  
	  if (sizeof($raw_path_array)>0) {


		$curr_pos = 0;
		foreach($path_array as $neverusedvariable) {
		  echo '<li><a href="' . tep_href_link('categories.php', 'pID=' . $VARS['product_id'] . '&cPath=' . $cpath_string_array[$curr_pos] , 'NONSSL') . '" class="menuBoxContentLink">' . TEXT_LINK_GO_TO_PRODUCT . $path_array[$curr_pos] . '</a></li>';
		  $curr_pos++;
		}
	 } else {
	 	echo '<span style="color: #FF1111;">' . WARNING_NO_PRODUCT . '</span>';
	 }
	 
	echo '</ul>';
	 
	?>
	</td>

  </tr>
</table></table>



</td></tr>
    </table>
    
    
        <?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
