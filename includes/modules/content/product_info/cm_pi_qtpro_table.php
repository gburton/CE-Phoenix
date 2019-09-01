<?php
/*
  $Id$

  2018 QTPro 5.6.1 BS
  by @raiwa 
  info@oscaddons.com
  www.oscaddons.com

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_qtpro_table {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_TITLE;
      $this->description = MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_DESCRIPTION;
      if (!defined('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_STATUS')) {
        $this->description .=   '<div class="secWarning">' . MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_WARNING . '<br>
                                <a href="modules_content.php?module=cm_pi_qtpro_options&action=install">' . MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_INSTALL_NOW . '</a></div>';
      }
      if ( !defined('MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS') || (defined('MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS') && MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS != 'True') ) {
        $this->description .=   '<div class="secWarning">' . MODULE_CONTENT_PRODUCT_INFO_QTPRO_HT_WARNING . '<br>
                                <a href="modules.php?set=header_tags&module=ht_qtpro_stock_check&action=install">' . MODULE_CONTENT_PRODUCT_INFO_QTPRO_HT_INSTALL_NOW . '</a></div>';
      }
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $product_info, $languages_id, $currencies;
      
      $content_width = (int)MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_CONTENT_WIDTH;
    
      /////   MISSION CODENAME: "GET INFORMATION" STARTS HERE   /////
      //Get the products_price and products_tax_class_id
      $products_facts_query = "select IF(s.status, s.specials_new_products_price, p.products_price) as products_price, p.products_tax_class_id from products p left join specials s on p.products_id = s.products_id where p.products_id = '" . (int)$_GET['products_id'] . "'";
      $products_facts = tep_db_fetch_array(tep_db_query($products_facts_query)); 
    
      // Get the stocklevels
      $products_stock_query=tep_db_query("SELECT products_stock_attributes, products_stock_quantity 
                                          FROM products_stock 
                                          WHERE products_id = '" . (int)$_GET['products_id'] . "' 
                                          ORDER BY products_stock_attributes");
    
      // get the option names
      $products_options_name_query = tep_db_query("SELECT distinct popt.products_options_id, popt.products_options_name 
                                                   FROM products_options popt, products_attributes patrib 
                                                   WHERE patrib.products_id='" . (int)$_GET['products_id'] . "' 
                                                   AND patrib.options_id = popt.products_options_id 
                                                   AND popt.products_options_track_stock = '1' 
                                                   AND popt.language_id = '" . (int)$languages_id . "' 
                                                   ORDER BY popt.products_options_id");			
    
      // build array of attributes price delta
      $attributes_price = array();
      $products_attributes_query = tep_db_query("SELECT pa.options_id, pa.options_values_id, pa.options_values_price, pa.price_prefix 
                                                 FROM products_attributes pa 
                                                 WHERE pa.products_id = '" . (int)$_GET['products_id'] . "'"); 
      while ($products_attributes_values = tep_db_fetch_array($products_attributes_query)) {
        $option_price = $products_attributes_values['options_values_price'];
        if ($products_attributes_values['price_prefix'] == "-") $option_price= -1*$option_price;
          $attributes_price[$products_attributes_values['options_id']][$products_attributes_values['options_values_id']] = $option_price;
      }									   
      /////   MISSION CODENAME: "GET INFORMATION" ENDS HERE   /////
    
    
      //OK! time to generate the html table
      //$html_ev_out will be displayed at the end of the script if $rowscounter > 0
      $rowscounter = 0;
      $html_ev_out = '<table class="table table-striped table-bordered table-condensed">
                        <thead>
                          <tr>';
    
      // build heading line with option names
      while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
        $html_ev_out .= '     <th class="text-center">' . $products_options_name['products_options_name'] . '</th>';
      }
      $html_ev_out .= '       <th class="text-center">'. MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_PRICE .'</th>';	
      $html_ev_out .= '       <th class="text-center">'. MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_STOCK .'</th>';
      $html_ev_out .= '     </tr>';
      $html_ev_out .= '   </thead>';
    
    
      // now create the rows! Each row will display the quantity for one combination of attributes.
      while ($products_stock_values=tep_db_fetch_array($products_stock_query)) {
        if ($products_stock_values['products_stock_quantity'] > 0) {
          //We only want to display rows for combinations we have on stock...
          //For example the quantity can be 0 or even negative if oversold.
          $rowscounter += 1; 
          $attributes = explode(',', $products_stock_values['products_stock_attributes']);
          $html_ev_out .= '<tr>'; 
        
        
          $total_price = $products_facts['products_price'];			
          foreach ($attributes as $attribute) {
            $attr = explode('-', $attribute);
            $html_ev_out .= '<td class="text-center">' . tep_values_name($attr[1]) . '</td>';
            $total_price  +=  $attributes_price[$attr[0]][$attr[1]];
          }
          $total_price = $currencies->display_price($total_price, tep_get_tax_rate($products_facts['products_tax_class_id']));
          //$total_price=$currencies->format($total_price);
        
          $html_ev_out .= '<td class="text-center">' . $total_price . '</td>';
          $html_ev_out .= '<td class="text-center">' . $products_stock_values['products_stock_quantity'] . '</td>';
        }
      }
    
    
      $html_ev_out .= '</tr></table>'; //Table is finished!
      
      if ($rowscounter > 0) { //Only display the table if it contains anything =)
        
        ob_start();
      	include('includes/modules/content/' . $this->group . '/templates/tpl_' . basename(__FILE__));
        $template = ob_get_clean();

        $oscTemplate->addContent($template, $this->group);
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable QT Pro Stock Table Module', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_STATUS', 'True', 'Should this module be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_CONTENT_WIDTH', '6', 'What width container should the content be shown in?', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_SORT_ORDER', '85', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_STATUS', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_CONTENT_WIDTH', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_TABLE_SORT_ORDER');
    }
  }

  /*Copied from admin functions*/
  if(!function_exists('tep_values_name')) {
    function tep_values_name($values_id) {
      global $languages_id;
  
      $values = tep_db_query("select products_options_values_name from products_options_values where products_options_values_id = '" . (int)$values_id . "' and language_id = '" . (int)$languages_id . "'");
      $values_values = tep_db_fetch_array($values);
  
      return $values_values['products_options_values_name'];
    }
  }