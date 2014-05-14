<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class bm_breadcrumb {
    var $code = 'bm_breadcrumb';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function bm_breadcrumb() {
      $this->title = MODULE_BOXES_BREADCRUMB_TITLE;
      $this->description = MODULE_BOXES_BREADCRUMB_DESCRIPTION;

      if ( defined('MODULE_BOXES_BREADCRUMB_STATUS') ) {
        $this->sort_order = MODULE_BOXES_BREADCRUMB_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_BREADCRUMB_STATUS == 'True');

        $this->group = 'boxes_header';
      }
    }

    function execute() {
      global $request_type, $oscTemplate, $breadcrumb;

      $data = '<div class="clearfix"></div><div class="col-sm-12">' . $breadcrumb->trail() . '</div><div class="clearfix"></div>';

      $oscTemplate->addBlock($data, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_BREADCRUMB_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Breadcrumb Module', 'MODULE_BOXES_BREADCRUMB_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_BREADCRUMB_SORT_ORDER', '10020', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_BREADCRUMB_STATUS', 'MODULE_BOXES_BREADCRUMB_SORT_ORDER');
    }
  }

