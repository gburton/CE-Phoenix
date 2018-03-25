<?php
/*
  $Id:

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  class cm_ip_description {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_IP_DESCRIPTION_TITLE;
      $this->description = MODULE_CONTENT_IP_DESCRIPTION_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_IP_DESCRIPTION_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_IP_DESCRIPTION_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_IP_DESCRIPTION_STATUS == 'True');
      }      
    }

    function execute() {
      global $oscTemplate, $current_category_id, $heading, $description, $languages_id;  

      $content_width = MODULE_CONTENT_IP_DESCRIPTION_CONTENT_WIDTH;      
      
      if (! tep_not_null($heading) ) {
				if (isset($_GET['manufacturers_id']) && !empty($_GET['manufacturers_id'])) {
					$focusq = tep_db_query("select m.manufacturers_image, m.manufacturers_name as name, mi.manufacturers_description as description from manufacturers m, manufacturers_info mi where m.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' and m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$languages_id . "'");
					$focus = tep_db_fetch_array($focusq);
	        $description = $focus['description'];
				} elseif ($current_category_id) {
					$focusq = tep_db_query("select c.categories_image, cd.categories_name as name, cd.categories_description as description from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
					$focus = tep_db_fetch_array($focusq);
	        $description = $focus['description'];
				}
			}
			
			if (tep_not_null($description)) {
				ob_start();
        require('includes/modules/content/' . $this->group . '/templates/description.php');
        $template = ob_get_clean();       

        $oscTemplate->addContent($template, $this->group);
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_IP_DESCRIPTION_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Category Description Module', 'MODULE_CONTENT_IP_DESCRIPTION_STATUS', 'True', 'Should this module be enabled?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_IP_DESCRIPTION_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_IP_DESCRIPTION_SORT_ORDER', '100', 'Sort order of display. Lowest is displayed first.', '6', '2', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_IP_DESCRIPTION_STATUS', 'MODULE_CONTENT_IP_DESCRIPTION_CONTENT_WIDTH', 'MODULE_CONTENT_IP_DESCRIPTION_SORT_ORDER');
    }    
  }