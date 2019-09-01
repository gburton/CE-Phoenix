<?php
/*
      QT Pro Version 6.3
  
      stats_low_stock_attrib.php
  
      Contribution extension to:
        osCommerce, Open Source E-Commerce Solutions
        http://www.oscommerce.com
     
      Copyright (c) 2004 Ralph Day
      Released under the GNU General Public License
  
      Based on prior works released under the GNU General Public License:
        QT Pro prior versions
          Ralph Day, October 2004
          Tom Wojcik aka TomThumb 2004/07/03 based on work by Michael Coffman aka coffman
          FREEZEHELL - 08/11/2003 freezehell@hotmail.com Copyright (c) 2003 IBWO
          Joseph Shain, January 2003
        osCommerce MS2
          Copyright (c) 2003 osCommerce
          
      Modifications made:
        11/2004 - Clean up to not replicate for all languages
                  Handle multiple attributes per product
                  Ignore attributes that stock isn't tracked for
                  Remove unused code
        
*******************************************************************************************
  
      QT Pro Low Stock Report
  
      This report lists all products and products attributes that have stock less than
      the reorder level configured in the osCommerce admin site

      
*******************************************************************************************

  $Id: stats_products.php,v 1.22 2002/03/07 20:30:00 harley_vb Exp $
  (v 1.3 by Tom Wojcik aka TomThumb 2004/07/03)
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
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

  require('includes/classes/currencies.php');
  $currencies = new currencies();
?>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
          <td class="menuboxheading" align="center"><?php echo strftime(DATE_FORMAT_LONG); ?></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
      
        <tr>
          <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td class="formAreaTitle"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
          <td class="formAreaTitle"><?php echo TABLE_HEADING_MODEL; ?></td>
          <td class="formAreaTitle"><?php echo TABLE_HEADING_QUANTITY; ?></td>
          <td class="formAreaTitle" align="right"><?php echo TABLE_HEADING_PRICE; ?>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4"><hr></td>
			  </tr>
<?php
        $products_query_raw = "select p.products_id, pd.products_name, p.products_model, p.products_quantity,p.products_price, l.name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_LANGUAGES . " l where p.products_id = pd.products_id and p.products_id = pd.products_id and l.languages_id = pd.language_id and pd.language_id = '" . (int)$languages_id . "' order by pd.products_name ASC";
  
        $products_query = tep_db_query($products_query_raw);
        while ($products = tep_db_fetch_array($products_query)) {
          $products_id = $products['products_id'];

          // check for product or attributes below reorder level
          $products_stock_query=tep_db_query("SELECT products_stock_attributes, products_stock_quantity 
                                              FROM products_stock 
                                              WHERE products_id=" . $products['products_id'] ." 
                                              ORDER BY products_stock_attributes");
          $products_stock_rows=tep_db_num_rows($products_stock_query);
          // Highlight products with low stock
          if ( $products['products_quantity'] > STOCK_REORDER_LEVEL ){
            $trclass="dataTableRow";
					} else { 
					  $trclass="OutofStock";
					} 
						 
          $products_quantity= $products['products_quantity'];
          $products_price=($products_stock_rows > 0) ? '&nbsp;' : $currencies->format($products['products_price']);
          //  Add Attributes
               
          if ($products_stock_rows > 0) {
            $products_options_name_query = tep_db_query("SELECT distinct popt.products_options_id, popt.products_options_name 
                                                         FROM products_options popt, products_attributes patrib 
                                                         WHERE patrib.products_id='" . $products['products_id'] . "' 
                                                         AND patrib.options_id = popt.products_options_id 
                                                         AND popt.products_options_track_stock = '1' 
                                                         AND popt.language_id = '" . (int)$languages_id . "' 
                                                         ORDER BY popt.products_options_id");												   
?>
          <td colspan="4"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1.2'); ?></td>

					  <tr class="dataTableRow">
              <td class="dataTableContent" cellpadding="2"><?php echo '<a href="' . tep_href_link('stock.php', 'product_id=' . $products['products_id']) . '">' . $products['products_name'] .'</a>'; ?>&nbsp;</td>
              <td class="dataTableContent" cellpadding="2"><?php echo $products['products_model']; ?></td>
              <td class="dataTableContent" cellpadding="2"><?php echo $products_quantity; ?></td>
              <td class="dataTableContent" align="right" cellpadding="2"><?php echo $products_price; ?>&nbsp;</td>
              <td><br></td>
            </tr>
			  
            <tr class="dataTableRow">
              <td class="main">
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr class="dataTableRowSelected">
<?php
                    // build headng line with option names
                    $products_options_count = 0;
                    while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
                      echo "                    <td class=\"smalltext\">&nbsp;<u>" . $products_options_name['products_options_name'] . "</u></td>\n";
                      $products_options_count++;
                    }
?>
                  </tr>
<?php
                  // buld array of attributes price delta
                  $attributes_price = array();
                  $products_attributes_query = tep_db_query("SELECT pa.options_id, pa.options_values_id, pa.options_values_price, pa.price_prefix 
                                                             FROM products_attributes pa 
                                                             WHERE pa.products_id = '" . $products['products_id'] . "'"); 
                  while ($products_attributes_values = tep_db_fetch_array($products_attributes_query)) {
                    $option_price = $products_attributes_values['options_values_price'];
                    if ($products_attributes_values['price_prefix'] == "-") $option_price= -1*$option_price;
                    $attributes_price[$products_attributes_values['options_id']][$products_attributes_values['options_values_id']] = $option_price;
                  }
        
                  // now display the attribute value names, table the html for quantity & price to get everything
                  // to line up right
                  $model_html_table="                <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\n";
                  $model_html_table.="                  <tr class=\"dataTableRowSelected\"><td class=\"smalltext\" colspan=\"" . $products_options_count . "\">&nbsp;</td></tr>\n";
                  $quantity_html_table="               <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\n";
                  $quantity_html_table.="                  <tr class=\"dataTableRowSelected\"><td class=\"smalltext\" colspan=\"" . $products_options_count . "\">&nbsp;</td></tr>\n";
                  $price_html_table="                <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\n";
                  $price_html_table.="                  <tr class=\"dataTableRowSelected\"><td class=\"smalltext\" colspan=\"" . $products_options_count . "\">&nbsp;</td></tr>\n";
                  while($products_stock_values=tep_db_fetch_array($products_stock_query)) {
                    $attributes=explode(",",$products_stock_values['products_stock_attributes']);
                    echo "                  <tr class=\"dataTableRowSelected\">\n"; 
              
                    $model_html_table.="                  <tr class=\"dataTableRowSelected\">\n";
                    $quantity_html_table.="                  <tr class=\"dataTableRowSelected\">\n";
                    $price_html_table.="                  <tr class=\"dataTableRowSelected\">\n";
                    $total_price=$products['products_price'];
              
                    // Highlight products out of stock
                   if (($products_stock_values['products_stock_quantity']) > STOCK_REORDER_LEVEL){
                     $trclassstock="dataTableContent";
                   } else {
                     $trclassstock="OutofStockAttrib";
                   }
                    
                   foreach($attributes as $attribute) {
                     $attr=explode("-",$attribute);
                     echo "<td class=\" " . $trclassstock . " \" >&nbsp;&nbsp;".tep_values_name($attr[1])."</td>\n";
                     $total_price+=$attributes_price[$attr[0]][$attr[1]];
                   }
                   $total_price=$currencies->format($total_price);
                   echo "                  </tr>\n";
        
                   $model_html_table.="<td class=\"" . $trclassstock . " \">&nbsp;</td>\n";
                   $model_html_table.="</tr>\n";
                   $quantity_html_table.="<td class=\"" . $trclassstock . " \">" . $products_stock_values['products_stock_quantity'] . "</td>\n";
                   $quantity_html_table.="</tr>\n";
                   $price_html_table.="<td align=\"right\" class=\" " . $trclassstock . " \">" . $total_price . "&nbsp;</td>\n";
                   $price_html_table.="</tr>\n";
                 }
                 echo "                </table>\n";
                 echo "              </td>\n";
                 $model_html_table.="                </table>\n";
                 $quantity_html_table.="                </table>\n";
                 $price_html_table.="                </table>\n";
                 echo "              <td class=smalltext>" . $model_html_table . "</td>\n";
                 echo "              <td class=smalltext>" . $quantity_html_table . "</td>\n";
                 echo "              <td class=smalltext>" . $price_html_table . "</td>\n";
                 echo "            </tr>\n"; 
              } else {
?>
                <td colspan="4"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1.2'); ?></td>
                  <tr class="<?php echo $trclass; ?>">
                    <td class="dataTableContent"><?php echo '<a href="' . tep_href_link('stock.php', 'product_id=' . $products['products_id']) . '">' . $products['products_name'] .'</a>'; ?>&nbsp;</td>
                    <td class="dataTableContent"><?php echo $products['products_model']; ?></td>
                    <td class="dataTableContent"><?php echo $products_quantity; ?></td>
                    <td class="dataTableContent" align="right"><?php echo $products_price; ?>&nbsp;</td>
                    <td><br></td>
                  </tr>
<?php 
              }
              ////////////////////////// End Attributes
          
            }
   
?>
            <tr>
              <td colspan="4"><?php echo tep_draw_separator(); ?></td>
            </tr>
          </table></td>
        </tr>
      </table></td>
    </tr>
  </table>
    
<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
