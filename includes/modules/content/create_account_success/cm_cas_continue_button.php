<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_cas_continue_button {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_CAS_CONTINUE_BUTTON_TITLE;
      $this->description = MODULE_CONTENT_CAS_CONTINUE_BUTTON_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_CAS_CONTINUE_BUTTON_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_CAS_CONTINUE_BUTTON_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_CAS_CONTINUE_BUTTON_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $navigation;
      
      $content_width = (int)MODULE_CONTENT_CAS_CONTINUE_BUTTON_CONTENT_WIDTH;
      
      if (count($navigation->snapshot) > 0) {
        $origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], [session_name()]), $navigation->snapshot['mode']);
        $navigation->clear_snapshot();
      } else {
        $origin_href = tep_href_link('index.php');
      }  
      
      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_CAS_CONTINUE_BUTTON_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable BUtton Module', 'MODULE_CONTENT_CAS_CONTINUE_BUTTON_STATUS', 'True', 'Do you want to enable this module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_CAS_CONTINUE_BUTTON_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_CAS_CONTINUE_BUTTON_SORT_ORDER', '30', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_CAS_CONTINUE_BUTTON_STATUS', 'MODULE_CONTENT_CAS_CONTINUE_BUTTON_CONTENT_WIDTH', 'MODULE_CONTENT_CAS_CONTINUE_BUTTON_SORT_ORDER');
    }
  }
  