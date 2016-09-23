<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_gtin {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_PRODUCT_INFO_GTIN_TITLE;
      $this->description = MODULE_CONTENT_PRODUCT_INFO_GTIN_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_PRODUCT_INFO_GTIN_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_PRODUCT_INFO_GTIN_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_PRODUCT_INFO_GTIN_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $product_info;
      
      $content_width = (int)MODULE_CONTENT_PRODUCT_INFO_GTIN_CONTENT_WIDTH;

      if (tep_not_null($product_info['products_gtin'])) {
        $gtin = substr($product_info['products_gtin'], 0-MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH);
        
        ob_start();
        include('includes/modules/content/' . $this->group . '/templates/gtin.php');
        $template = ob_get_clean();

        $oscTemplate->addContent($template, $this->group);
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_PRODUCT_INFO_GTIN_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable GTIN Module', 'MODULE_CONTENT_PRODUCT_INFO_GTIN_STATUS', 'True', 'Should this module be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_PRODUCT_INFO_GTIN_CONTENT_WIDTH', '6', 'What width container should the content be shown in?', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Length of GTIN', 'MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH', '13', 'Length of GTIN. 14 (Industry Standard), 13 (eg ISBN codes and EAN UCC-13), 12 (UPC), 8 (EAN UCC-8)', '6', '0', 'tep_cfg_select_option(array(\'14\', \'13\', \'12\', \'8\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_PRODUCT_INFO_GTIN_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_PRODUCT_INFO_GTIN_STATUS', 'MODULE_CONTENT_PRODUCT_INFO_GTIN_CONTENT_WIDTH', 'MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH', 'MODULE_CONTENT_PRODUCT_INFO_GTIN_SORT_ORDER');
    }
  }
  