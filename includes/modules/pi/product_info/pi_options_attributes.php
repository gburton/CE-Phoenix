<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class pi_options_attributes {
    var $code = 'pi_options_attributes';
    var $group = 'pi_modules_c';
    var $title;
    var $description;
    var $content_width;
    var $sort_order;
    var $api_version;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = PI_OA_TITLE;
      $this->description = PI_OA_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';
      $this->description .= '<div class="secInfo">' . $this->display_layout() . '</div>';

      if ( defined('PI_OA_STATUS') ) {
        $this->group = 'pi_modules_' . strtolower(PI_OA_GROUP);
        $this->sort_order = PI_OA_SORT_ORDER;
        $this->content_width = (int)PI_OA_CONTENT_WIDTH;
        $this->enabled = (PI_OA_STATUS == 'True');
      }
    }

    function getOutput() {
      global $oscTemplate, $languages_id, $currencies, $product_info, $cart;
      
      $content_width = $this->content_width;
      
      $options_output = null;
        
      $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from products_options popt, products_attributes patrib where patrib.products_id='" . (int)$_GET['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "' order by popt.products_options_name");

      if (tep_db_num_rows($products_options_name_query)) {
        while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
          $products_options_array = array();
          
          $fr_input = $fr_required = null;
          if (PI_OA_ENFORCE == 'True') {
            $fr_input    = FORM_REQUIRED_INPUT;
            $fr_required = 'required aria-required="true" '; 
          }
          if (PI_OA_HELPER == 'True') {
            $products_options_array[] = array('id' => '', 'text' => PI_OA_ENFORCE_SELECTION);            
          }
          
          $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from products_attributes pa, products_options_values pov where pa.products_id = '" . (int)$_GET['products_id'] . "' and pa.options_id = '" . (int)$products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$languages_id . "'");
          while ($products_options = tep_db_fetch_array($products_options_query)) {
            $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
            if ($products_options['options_values_price'] != '0') {
              $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
            }
          }

          if (is_string($_GET['products_id']) && isset($cart->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']])) {
            $selected_attribute = $cart->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']];
          } else {
            $selected_attribute = false;
          }
          
          $options_output .= '<div class="form-group row">' . PHP_EOL;
            $options_output .= '<label for="input_' . $products_options_name['products_options_id'] . '" class="col-form-label col-sm-3 text-left text-sm-right">' . $products_options_name['products_options_name'] . '</label>' . PHP_EOL;
            $options_output .= '<div class="col-sm-9">' . PHP_EOL;
              $options_output .= tep_draw_pull_down_menu('id[' . $products_options_name['products_options_id'] . ']', $products_options_array, $selected_attribute, $fr_required . 'id="input_' . $products_options_name['products_options_id'] . '"') . PHP_EOL;
              $options_output .= $fr_input;
            $options_output .= '</div>' . PHP_EOL;
          $options_output .= '</div>' . PHP_EOL;
        }
      
        $tpl_data = ['group' => $this->group, 'file' => __FILE__];
        include 'includes/modules/block_template.php';
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('PI_OA_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Options & Attributes', 'PI_OA_STATUS', 'True', 'Should this module be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Module Display', 'PI_OA_GROUP', 'C', 'Where should this module display on the product info page?', '6', '2', 'tep_cfg_select_option(array(\'A\', \'B\', \'C\', \'D\', \'E\', \'F\', \'G\', \'H\', \'I\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'PI_OA_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Add Helper Text', 'PI_OA_HELPER', 'True', 'Should first option in dropdown be Helper Text?', '6', '4', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enforce Selection', 'PI_OA_ENFORCE', 'True', 'Should customer be forced to select option(s)?', '6', '5', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'PI_OA_SORT_ORDER', '310', 'Sort order of display. Lowest is displayed first.', '6', '6', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('PI_OA_STATUS', 'PI_OA_GROUP', 'PI_OA_CONTENT_WIDTH', 'PI_OA_HELPER', 'PI_OA_ENFORCE', 'PI_OA_SORT_ORDER');
    }
    
    function display_layout() {
      include_once(DIR_FS_CATALOG . 'includes/modules/content/product_info/cm_pi_modular.php');
       
      return call_user_func(array('cm_pi_modular', 'display_layout'));
    }
    
  }
  
