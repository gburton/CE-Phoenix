<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class cm_i_upcoming_products {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_UPCOMING_PRODUCTS_TITLE;
      $this->description = MODULE_CONTENT_UPCOMING_PRODUCTS_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_UPCOMING_PRODUCTS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_UPCOMING_PRODUCTS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_UPCOMING_PRODUCTS_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $languages_id;
      
      $content_width = MODULE_CONTENT_UPCOMING_PRODUCTS_CONTENT_WIDTH;
      
      $expected_query = tep_db_query("select p.products_id, pd.products_name, products_date_available as date_expected from products p, products_description pd where to_days(products_date_available) >= to_days(now()) and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by " . MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_FIELD . " " . MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_SORT . " limit " . (int)MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY);
      
      if (tep_db_num_rows($expected_query) > 0) {
        ob_start();
        include('includes/modules/content/' . $this->group . '/templates/upcoming_products.php');
        $template = ob_get_clean(); 
        
        $oscTemplate->addContent($template, $this->group);
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_UPCOMING_PRODUCTS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable New Products Module', 'MODULE_CONTENT_UPCOMING_PRODUCTS_STATUS', 'True', 'Do you want to enable this module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_UPCOMING_PRODUCTS_CONTENT_WIDTH', '12', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Display', 'MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY', '6', 'Maximum Number of products that should show in this module?', '6', '3', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Sort Order', 'MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_SORT', 'desc', 'This is the sort order used in the output.', '1', '4', 'tep_cfg_select_option(array(\'asc\', \'desc\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Sort Field', 'MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_FIELD', 'date_expected', 'The column to sort by in the output.', '1', '5', 'tep_cfg_select_option(array(\'products_name\', \'date_expected\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_UPCOMING_PRODUCTS_SORT_ORDER', '400', 'Sort order of display. Lowest is displayed first.', '6', '5', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_UPCOMING_PRODUCTS_STATUS', 'MODULE_CONTENT_UPCOMING_PRODUCTS_CONTENT_WIDTH', 'MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY', 'MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_SORT', 'MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_FIELD', 'MODULE_CONTENT_UPCOMING_PRODUCTS_SORT_ORDER');
    }
  }