<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_options {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function cm_pi_options() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_PRODUCT_INFO_OPTIONS_TITLE;
      $this->description = MODULE_CONTENT_PRODUCT_INFO_OPTIONS_DESCRIPTION;

      if ( defined('MODULE_CONTENT_PRODUCT_INFO_OPTIONS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_PRODUCT_INFO_OPTIONS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_PRODUCT_INFO_OPTIONS_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $HTTP_GET_VARS, $languages_id, $cart, $currencies;
      
      $content_width   = (int)MODULE_CONTENT_PRODUCT_INFO_OPTIONS_CONTENT_WIDTH;
      $product_options = NULL;
      
      $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$HTTP_GET_VARS['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "'");
      $products_attributes = tep_db_fetch_array($products_attributes_query);
      
      if ($products_attributes['total'] > 0) {
        
        $product_options .= '<h4>' . MODULE_CONTENT_PRODUCT_INFO_OPTIONS . '</h4>';
        $product_options .= '<p>';
        
        $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$HTTP_GET_VARS['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "' order by popt.products_options_name");
        while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
            $products_options_array = array();
            $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and pa.options_id = '" . (int)$products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$languages_id . "'");
            while ($products_options = tep_db_fetch_array($products_options_query)) {
                $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
                if ($products_options['options_values_price'] != '0') {
                    $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
                }
            }

            if (is_string($HTTP_GET_VARS['products_id']) && isset($cart->contents[$HTTP_GET_VARS['products_id']]['attributes'][$products_options_name['products_options_id']])) {
                $selected_attribute = $cart->contents[$HTTP_GET_VARS['products_id']]['attributes'][$products_options_name['products_options_id']];
            } else {
                $selected_attribute = false;
            }
            
            $product_options .= '<strong>' . $products_options_name['products_options_name'] . ':</strong><br />' . tep_draw_pull_down_menu('id[' . $products_options_name['products_options_id'] . ']', $products_options_array, $selected_attribute, 'style="width: 200px;"') . '<br />';
            
        }
        
        $product_options .= '</p>';
        
        ob_start();
        include(DIR_WS_MODULES . 'content/' . $this->group . '/templates/options.php');
        $template = ob_get_clean();

        $oscTemplate->addContent($template, $this->group);
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_PRODUCT_INFO_OPTIONS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Product Options Module', 'MODULE_CONTENT_PRODUCT_INFO_OPTIONS_STATUS', 'True', 'Should the product options block be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_PRODUCT_INFO_OPTIONS_CONTENT_WIDTH', '8', 'What width container should the content be shown in?', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Align-Float', 'MODULE_CONTENT_PRODUCT_INFO_OPTIONS_CONTENT_ALIGN', 'text-left', 'How should the content be aligned or float?', '6', '1', 'tep_cfg_select_option(array(\'text-left\', \'text-center\', \'text-right\', \'pull-left\', \'pull-right\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Vertical Margin', 'MODULE_CONTENT_PRODUCT_INFO_OPTIONS_CONTENT_VERT_MARGIN', '', 'Top and Bottom Margin added to the module? none, VerticalMargin=10px', '6', '1', 'tep_cfg_select_option(array(\'\', \'VerticalMargin\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Horizontal Margin', 'MODULE_CONTENT_PRODUCT_INFO_OPTIONS_CONTENT_HORIZ_MARGIN', '', 'Left and Right Margin added to the module? none, HorizontalMargin=10px', '6', '1', 'tep_cfg_select_option(array(\'\', \'HorizontalMargin\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_PRODUCT_INFO_OPTIONS_SORT_ORDER', '500', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_PRODUCT_INFO_OPTIONS_STATUS', 'MODULE_CONTENT_PRODUCT_INFO_OPTIONS_CONTENT_WIDTH', 'MODULE_CONTENT_PRODUCT_INFO_OPTIONS_CONTENT_ALIGN', 'MODULE_CONTENT_PRODUCT_INFO_OPTIONS_CONTENT_VERT_MARGIN', 'MODULE_CONTENT_PRODUCT_INFO_OPTIONS_CONTENT_HORIZ_MARGIN', 'MODULE_CONTENT_PRODUCT_INFO_OPTIONS_SORT_ORDER');
    }
  }

