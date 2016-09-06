<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class cm_i_customer_greeting {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_CUSTOMER_GREETING_TITLE;
      $this->description = MODULE_CONTENT_CUSTOMER_GREETING_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_CUSTOMER_GREETING_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_CUSTOMER_GREETING_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_CUSTOMER_GREETING_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $customer_id, $customer_first_name;
      
      $content_width = MODULE_CONTENT_CUSTOMER_GREETING_CONTENT_WIDTH;
      
      if (tep_session_is_registered('customer_first_name') && tep_session_is_registered('customer_id')) {
        $customer_greeting = sprintf(MODULE_CONTENT_CUSTOMER_GREETING_PERSONAL, tep_output_string_protected($customer_first_name), tep_href_link('products_new.php'));
      } else {
        $customer_greeting = sprintf(MODULE_CONTENT_CUSTOMER_GREETING_GUEST, tep_href_link('login.php', '', 'SSL'), tep_href_link('create_account.php', '', 'SSL'));
      }
      
      ob_start();
      include('includes/modules/content/' . $this->group . '/templates/customer_greeting.php');
      $template = ob_get_clean(); 
        
      $oscTemplate->addContent($template, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_CUSTOMER_GREETING_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Featured Products Module', 'MODULE_CONTENT_CUSTOMER_GREETING_STATUS', 'True', 'Do you want to enable this module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_CUSTOMER_GREETING_CONTENT_WIDTH', '12', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_CUSTOMER_GREETING_SORT_ORDER', '100', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_CUSTOMER_GREETING_STATUS', 'MODULE_CONTENT_CUSTOMER_GREETING_CONTENT_WIDTH', 'MODULE_CONTENT_CUSTOMER_GREETING_SORT_ORDER');
    }
  }