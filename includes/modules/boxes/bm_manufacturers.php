<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class bm_manufacturers {
    var $code = 'bm_manufacturers';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_BOXES_MANUFACTURERS_TITLE;
      $this->description = MODULE_BOXES_MANUFACTURERS_DESCRIPTION;

      if ( defined('MODULE_BOXES_MANUFACTURERS_STATUS') ) {
        $this->sort_order = MODULE_BOXES_MANUFACTURERS_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_MANUFACTURERS_STATUS == 'True');

        $this->group = ((MODULE_BOXES_MANUFACTURERS_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    function getData() {
      global $request_type, $oscTemplate;

      $data = '';

      $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from manufacturers order by manufacturers_name");
      if ($number_of_rows = tep_db_num_rows($manufacturers_query)) {
        if ($number_of_rows <= MODULE_BOXES_MANUFACTURERS_MAX_LIST) {
// Display a list
          $manufacturers_list = '<div class="list-group list-group-flush">';
          while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
            $manufacturers_name = $manufacturers['manufacturers_name'];
            if (isset($_GET['manufacturers_id']) && ($_GET['manufacturers_id'] == $manufacturers['manufacturers_id'])) $manufacturers_name = '<strong>' . $manufacturers['manufacturers_name'] .'</strong>';
            $manufacturers_list .= '<a class="list-group-item list-group-item-action" href="' . tep_href_link('index.php', 'manufacturers_id=' . $manufacturers['manufacturers_id']) . '">' . $manufacturers_name . '</a>';
          }
          $manufacturers_list .= '</div>';

          $data = $manufacturers_list;
        } else {
// Display a drop-down
          $manufacturers_array = array();
          $manufacturers_array[] = array('id' => '', 'text' => PULL_DOWN_DEFAULT);

          while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {            
            $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
                                           'text' => $manufacturers['manufacturers_name']);
          }

          $data .= '<ul class="list-group list-group-flush">';
            $data .= '<li class="list-group-item">';
              $data .= tep_draw_form('manufacturers', tep_href_link('index.php', '', $request_type, false), 'get');
                $data .= tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, (isset($_GET['manufacturers_id']) ? $_GET['manufacturers_id'] : ''), 'onchange="this.form.submit();" style="width: 100%"') . tep_hide_session_id();
              $data .= '</form>';
            $data .= '</li>';
          $data .= '</ul>';
        }

      }

      return $data;
    }

    function execute() {
      global $SID, $oscTemplate;

      $output = $this->getData();
      
      $tpl_data = ['group' => $this->group, 'file' => __FILE__];
      include 'includes/modules/block_template.php';
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_MANUFACTURERS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Manufacturers Module', 'MODULE_BOXES_MANUFACTURERS_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_BOXES_MANUFACTURERS_CONTENT_PLACEMENT', 'Left Column', 'Should the module be loaded in the left or right column?', '6', '2', 'tep_cfg_select_option(array(\'Left Column\', \'Right Column\'), ', now())");
      tep_db_query("INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Manufacturers List', 'MODULE_BOXES_MANUFACTURERS_MAX_LIST', '9', 'When the number of manufacturers exceeds this number, a drop-down list will be displayed instead of the default list', '6', '3', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_MANUFACTURERS_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_MANUFACTURERS_STATUS', 'MODULE_BOXES_MANUFACTURERS_CONTENT_PLACEMENT', 'MODULE_BOXES_MANUFACTURERS_MAX_LIST',  'MODULE_BOXES_MANUFACTURERS_SORT_ORDER');
    }
  }
  