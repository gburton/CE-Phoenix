<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  class cm_ip_title {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_IP_TITLE_TITLE;
      $this->description = MODULE_CONTENT_IP_TITLE_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_IP_TITLE_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_IP_TITLE_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_IP_TITLE_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $osC_Manufacturer, $osC_Category, $current_category_id, $description, $languages_id;
      
      $content_width = MODULE_CONTENT_IP_TITLE_CONTENT_WIDTH;
     
			$heading = '';
			if (isset($_GET['manufacturers_id']) && !empty($_GET['manufacturers_id'])) {		
		        $osC_Manufacturer = new osC_Manufacturer(($_GET['manufacturers_id']));
				
				$heading = $osC_Manufacturer->getTitle();
				$description = $osC_Manufacturer->getDescription();

			} elseif ($current_category_id) {
				
				$heading = $osC_Category->getTitle();
				$description = $osC_Category->getDescription();
			}
      
      ob_start();
      include('includes/modules/content/' . $this->group . '/templates/title.php');
      $template = ob_get_clean(); 
      
      $oscTemplate->addContent($template, $this->group);

    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_IP_TITLE_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Title Module', 'MODULE_CONTENT_IP_TITLE_STATUS', 'True', 'Do you want to enable this module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_IP_TITLE_CONTENT_WIDTH', '12', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_IP_TITLE_SORT_ORDER', '50', 'Sort order of display. Lowest is displayed first.', '6', '5', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_IP_TITLE_STATUS', 'MODULE_CONTENT_IP_TITLE_CONTENT_WIDTH', 'MODULE_CONTENT_IP_TITLE_SORT_ORDER');
    }
  }
  