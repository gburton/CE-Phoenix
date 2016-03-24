<?php
/*

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class cm_ahi_button_back {
    public $code = '';
    public $group = '';
    public $title = '';
    public $description = '';
    public $sort_order = 0;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_TITLE;
      $this->description = MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_STATUS == 'True');
      }
    }

    public function execute() {
      global $oscTemplate, $order;
      ob_start();
      include(DIR_WS_MODULES . 'content/' . $this->group . '/templates/' . basename(__FILE__));
      $template = ob_get_clean();
      $oscTemplate->addContent( $template, $this->group );
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_STATUS');
    }

    public function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Back Button module', 'MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_STATUS', 'True', 'Should the Back Button block be shown on the order history info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_SORT_ORDER', '70', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
    }

    public function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    public function keys() {
      $keys = array();
      $keys[] = 'MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_STATUS';
      $keys[] = 'MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_CONTENT_WIDTH';
      $keys[] = 'MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_SORT_ORDER';
      
      return $keys;
    }
  }
