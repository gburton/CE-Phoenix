<?php
/*
  Copyright (c) 2018, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

  class cm_pi_options_attributes {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_PI_OA_TITLE;
      $this->description = MODULE_CONTENT_PI_OA_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_PI_OA_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_PI_OA_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_PI_OA_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $languages_id, $currencies, $product_info;
      
      $content_width = (int)MODULE_CONTENT_PI_OA_CONTENT_WIDTH;
      $options_output = null;
        
      $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from products_options popt, products_attributes patrib where patrib.products_id='" . (int)$_GET['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "' order by popt.products_options_name");

      if (tep_db_num_rows($products_options_name_query)) {
        while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
          $products_options_array = array();
          
          $fr_input = $fr_required = null;
          if (MODULE_CONTENT_PI_OA_ENFORCE == 'True') {
            $fr_input    = FORM_REQUIRED_INPUT;
            $fr_required = 'required aria-required="true" '; 
          }
          if (MODULE_CONTENT_PI_OA_HELPER == 'True') {
            $products_options_array[] = array('id' => '', 'text' => MODULE_CONTENT_PI_OA_ENFORCE_SELECTION);            
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
          $options_output .=   '<label for="input_' . $products_options_name['products_options_id'] . '" class="control-label col-sm-3 text-right">' . $products_options_name['products_options_name'] . '</label>' . PHP_EOL; 
          $options_output .=   '<div class="col-sm-9">' . PHP_EOL;
          $options_output .=     tep_draw_pull_down_menu('id[' . $products_options_name['products_options_id'] . ']', $products_options_array, $selected_attribute, $fr_required . 'id="input_' . $products_options_name['products_options_id'] . '"') . PHP_EOL;
          $options_output .=     $fr_input;
          $options_output .=   '</div>' . PHP_EOL;
          $options_output .= '</div>' . PHP_EOL;    
        }
      
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
      return defined('MODULE_CONTENT_PI_OA_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Options & Attributes', 'MODULE_CONTENT_PI_OA_STATUS', 'True', 'Should this module be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_PI_OA_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Add Helper Text', 'MODULE_CONTENT_PI_OA_HELPER', 'True', 'Should first option in dropdown be Helper Text?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enforce Selection', 'MODULE_CONTENT_PI_OA_ENFORCE', 'True', 'Should customer be forced to select option(s)?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_PI_OA_SORT_ORDER', '80', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_PI_OA_STATUS', 'MODULE_CONTENT_PI_OA_CONTENT_WIDTH', 'MODULE_CONTENT_PI_OA_HELPER', 'MODULE_CONTENT_PI_OA_ENFORCE', 'MODULE_CONTENT_PI_OA_SORT_ORDER');
    }
  }
  
