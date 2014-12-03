<?php
/*
 $Id: functions.php v5.0 07/19/2007 djmonkey1 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/


  // Function to change quotes to HTML equivalents for form inputs
  function oe_html_quotes($string) {
    return str_replace("'", "&#39;", $string);
	
  }

  ///originally written by Josh Dechant for the MOECTOE suite
  ///tweaked by djmonkey1 for Order Editor 2.7 and up   
  function oe_js_zone_list($country, $form, $field, $id, $id2) {
    $countries_query = tep_db_query("select distinct zone_country_id from " . TABLE_ZONES . " order by zone_country_id");
    $num_country = 1;
    $output_string = '';
    while ($countries = tep_db_fetch_array($countries_query)) {
      if ($num_country == 1) {
        $output_string .= '  if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      } else {
        $output_string .= '  } else if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      }

      $states_query = tep_db_query("select zone_name, zone_id from " . TABLE_ZONES . " where zone_country_id = '" . $countries['zone_country_id'] . "' order by zone_name");

      $num_state = 1;
      while ($states = tep_db_fetch_array($states_query)) {
        if ($num_state == '1') $output_string .= '    ' . $form . '[' . $field . '].options[0] = new Option("' . PLEASE_SELECT . '", "");' . "\n";
        $output_string .= '    ' . $form . '[' . $field . '].options[' . $num_state . '] = new Option("' . $states['zone_name'] . '", "' . $states['zone_id'] . '");' . "\n";
        $num_state++;
      }
      $output_string .= '    setStateVisibility(' . $id . ', "hidden", ' . $id2 . ');' . "\n";
      $num_country++;
    }
    $output_string .= '  } else {' . "\n" .
                      '    ' . $form . '[' . $field . '].options[0] = new Option("' . ENTRY_TYPE_BELOW . '", "");' . "\n" .
                      '    setStateVisibility(' . $id . ', "visible", ' . $id2 . ');' . "\n" . 
                      '  }' . "\n";

    return $output_string;
  }
 

    // Return the tax description for a zone / class
	//kept the tep_ prefix here as the Low Order Fee module uses this function 
  function tep_get_tax_description($class_id, $country_id, $zone_id) {
    $tax_query = tep_db_query("select tax_description from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' order by tr.tax_priority");
    if (tep_db_num_rows($tax_query)) {
      $tax_description = '';
      while ($tax = tep_db_fetch_array($tax_query)) {
        $tax_description .= $tax['tax_description'] . ' + ';
      }
      $tax_description = substr($tax_description, 0, -3);

      return $tax_description;
    } else {
      return ENTRY_TAX;
    }
  }
  
  
  function oe_get_country_id($country_name) {
    $country_id_query = tep_db_query("select countries_id from " . TABLE_COUNTRIES . " where countries_name = '" . $country_name . "'");
    if (!tep_db_num_rows($country_id_query)) {
      return false;
    }
    else {
      $country_id_row = tep_db_fetch_array($country_id_query);
      return $country_id_row['countries_id'];
    }
  }
  
  function oe_get_country_iso_code_2($country_id) {
    $country_iso_query = tep_db_query("select countries_iso_code_2 from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");
    if (!tep_db_num_rows($country_iso_query)) {
      return false;
    } else {
      $country_iso_row = tep_db_fetch_array($country_iso_query);
      return $country_iso_row['countries_iso_code_2'];
    }
  }

  function oe_get_country_iso_code_3($country_id) {
    $country_iso_query = tep_db_query("select countries_iso_code_3 from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");
    if (!tep_db_num_rows($country_iso_query)) {
      return false;
    } else {
      $country_iso_row = tep_db_fetch_array($country_iso_query);
      return $country_iso_row['countries_iso_code_3'];
    }
  }

  function oe_get_zone_id($country_id, $zone_name) {
    $zone_id_query = tep_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country_id . "' and (zone_name = '" . $zone_name . "' OR zone_code = '" . $zone_name . "')");
    if (!tep_db_num_rows($zone_id_query)) {
      return false;
    }
    else {
      $zone_id_row = tep_db_fetch_array($zone_id_query);
      return $zone_id_row['zone_id'];
    }
  }
  
 
  //Used to workaround problems associated with apostrophes, double quotes, and line breaks 
  function oe_html_no_quote($string) {
  $string=str_replace('&#39;', '', $string);
  $string=str_replace("'", "", $string);
  $string=str_replace('"', '', $string);
  $string=preg_replace("/\\r\\n|\\n|\\r/", "<BR>", $string); 
  return $string;
	
  }


 // Output a selection field - alias function for oe_draw_checkbox_field() and oe_draw_radio_field()
 //I had to draw up custom functions in order to pass parameters with checkbox fields, maybe radio fields too someday
  function oe_draw_selection_field($name, $type, $value = '', $checked = false, $compare = '', $parameters = '') {
    $selection = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

    if (tep_not_null($value)) $selection .= ' value="' . tep_output_string($value) . '"';
	
	if (tep_not_null($parameters)) $selection .=  ' ' . $parameters;


    if ( ($checked == true) || (isset($GLOBALS[$name]) && is_string($GLOBALS[$name]) && ($GLOBALS[$name] == 'on')) || (isset($value) && isset($GLOBALS[$name]) && (stripslashes($GLOBALS[$name]) == $value)) || (tep_not_null($value) && tep_not_null($compare) && ($value == $compare)) ) {
      $selection .= ' CHECKED';
    }

    $selection .= '>';

    return $selection;
  }

////
// Output a form checkbox field
  function oe_draw_checkbox_field($name, $value = '', $checked = false, $compare = '', $parameters = '') {
    return oe_draw_selection_field($name, 'checkbox', $value, $checked, $compare, $parameters);
  }

////
// Output a form radio field
  function oe_draw_oe_radio_field($name, $value = '', $checked = false, $compare = '', $parameters = '') {
    return or_draw_selection_field($name, 'radio', $value, $checked, $compare, $parameters);
  }

////

  function oe_get_subcategories(&$subcategories_array, $parent_id = 0) {
    $subcategories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$parent_id . "'");
    while ($subcategories = tep_db_fetch_array($subcategories_query)) {
      $subcategories_array[sizeof($subcategories_array)] = $subcategories['categories_id'];
      if ($subcategories['categories_id'] != $parent_id) {
        oe_get_subcategories($subcategories_array, $subcategories['categories_id']);
      }
    }
  }

  function oe_select_ot_options($key_value, $key = 'ORDER_EDITOR_ALLOWED_OT_MODULES') {
    //$required_ot_totals = oe_required_ot();
    $installed_ot_modules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);
    $return_string = '';
    reset($installed_ot_modules);
    $i=0;
    while (list(, $value) = each($installed_ot_modules)) {
      $ot_class = substr($value, 0, strrpos($value, '.'));
      //if (in_array($ot_class, $required_ot_totals)) continue;
      
      $name = (($key) ? 'configuration[' . $key . '][' . $i . ']' : 'configuration_value');
      $return_string .= '<br><input type="checkbox" name="' . $name . '" value="' . $ot_class . '"';
      $key_values = explode(", ", $key_value);
      if (in_array($ot_class, $key_values)) $return_string .= ' checked="checked"';
      $return_string .= '> ' . $ot_class;
      $i++;
    }  
  
    return $return_string;
  }
  
  function oe_generate_search_SQL($keywords_array, $fields_array, $search_type='OR') {
    $search_string = '';
    $end_trim = 0;
    
    switch ($search_type) {
      case 'OR': 
      case 'AND':
        $end_trim = strlen($search_type)+2;
        foreach($fields_array as $field) {
          foreach($keywords_array as $keyword) {
            $search_string .= "$field like '%$keyword%' $search_type ";
          }
        }
        break;
    
      case '(OR)AND':
        $end_trim = 5;
        foreach($fields_array as $field) {
          $search_string .= '(';
          foreach($keywords_array as $keyword) {
            $search_string .= "$field like '%$keyword%' OR ";
          }          
          $search_string = substr($search_string, 0, -4);          
          $search_string .= ') AND ';
          $end_trim = 5;
        }
        break;

      case '()OR':
        $end_trim = 4;
        foreach($fields_array as $field) {
          $search_string .= '(';
          
          if ($keywords_array) {
            foreach($keywords_array as $keyword) {
              $search_string .= "$field like '%$keyword%' OR ";
            }
          } else {
            $search_string .= $field;
          }
          
          $search_string .= ') OR ';
        }
        break;

    }   
    
    return substr($search_string, 0, -$end_trim);
  }
  
  function oe_clean_SQL_keywords($keywords_array) {
    $new_keywords_array = array();

    foreach($keywords_array as $keyword) {
      $keyword = ereg_replace("(,)|('s)", "", $keyword);
      $new_keywords_array[] = $keyword;
    }
    
    return $new_keywords_array;
  }
  
  function oe_iconv($string) {
    if (function_exists('iconv')) {
       return iconv('UTF-8', CHARSET . '//TRANSLIT', $string);
        } else {
      return $string;
    }
  }
?>