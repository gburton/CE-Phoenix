<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class pi_buy_button {
    var $code = 'pi_buy_button';
    var $group = 'pi_modules_c';
    var $title;
    var $description;
    var $content_width;
    var $sort_order;
    var $api_version;
    var $enabled = false;

    function __construct() {
      $this->group = basename(dirname(__FILE__));

      $this->title = PI_BUY_TITLE;
      $this->description = PI_BUY_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';
      $this->description .= '<div class="secInfo">' . $this->display_layout() . '</div>';

      if ( defined('PI_BUY_STATUS') ) {
        $this->group = 'pi_modules_' . strtolower(PI_BUY_GROUP);
        $this->sort_order = PI_BUY_SORT_ORDER;
        $this->content_width = (int)PI_BUY_CONTENT_WIDTH;
        $this->enabled = (PI_BUY_STATUS == 'True');
      }
    }

    function getOutput() {
      global $oscTemplate, $product_info, $languages_id;
      
      $products_attributes_query = tep_db_query("select count(*) as total from products_options popt, products_attributes patrib where patrib.products_id='" . (int)$_GET['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "'");
      $products_attributes = tep_db_fetch_array($products_attributes_query);
      
      $content_width = $this->content_width;

      $tpl_data = ['group' => $this->group, 'file' => __FILE__];
      include 'includes/modules/block_template.php';
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('PI_BUY_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Buy Button', 'PI_BUY_STATUS', 'True', 'Should this module be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Module Display', 'PI_BUY_GROUP', 'C', 'Where should this module display on the product info page?', '6', '2', 'tep_cfg_select_option(array(\'A\', \'B\', \'C\', \'D\', \'E\', \'F\', \'G\', \'H\', \'I\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'PI_BUY_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'PI_BUY_SORT_ORDER', '320', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('PI_BUY_STATUS', 'PI_BUY_GROUP', 'PI_BUY_CONTENT_WIDTH', 'PI_BUY_SORT_ORDER');
    }
    
    function display_layout() {
      include_once(DIR_FS_CATALOG . 'includes/modules/content/product_info/cm_pi_modular.php');
       
      return call_user_func(array('cm_pi_modular', 'display_layout'));
    }
    
  }
  